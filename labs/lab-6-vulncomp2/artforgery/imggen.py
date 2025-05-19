import os
import json
import requests
from openai import AzureOpenAI

# Initialize the Azure OpenAI client
client = AzureOpenAI(
    api_version="2024-02-01",
    azure_endpoint="https://capdnbai.openai.azure.com/",
    api_key=os.environ["AZURE_OPENAI_API_KEY"],
)

# Read pieces from the JSON file
with open('pieces.json', 'r') as file:
    pieces = json.load(file)

# Ensure the img directory exists
os.makedirs('img', exist_ok=True)

# Loop through each piece and generate an image
for index, piece in enumerate(pieces):
    image_path = f'img/{index}.png'
    # Check if the image already exists
    if os.path.exists(image_path):
        print(f"Image for {piece['title']} already exists, skipping.")
        continue

    prompt = f"{piece['title']}: {piece['description']} in a sci-fi NFT art style"
    result = client.images.generate(
        model="wchtests",  # the name of your DALL-E 3 deployment
        prompt=prompt,
        n=1,
        size="1024x1024"
    )

    # Extract the image URL from the response
    image_url = json.loads(result.model_dump_json())['data'][0]['url']

    # Download the image
    image_data = requests.get(image_url).content

    # Save the image to the img directory
    with open(image_path, 'wb') as img_file:
        img_file.write(image_data)

    print(f"Generated and saved image for {piece['title']} at {image_path}")

print("All images generated and saved.")
