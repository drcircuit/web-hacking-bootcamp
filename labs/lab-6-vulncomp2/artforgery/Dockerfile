# Use the official Node.js image.
# https://hub.docker.com/_/node
FROM node:slim

# Create and change to the app directory.
WORKDIR /usr/src/app
# Install Python and build tools BEFORE npm install
RUN apt-get update && apt-get install -y python3 make g++
# Copy application dependency manifests to the container image.
COPY package*.json ./

# Install production dependencies.
RUN npm install

# Copy local code to the container image.
COPY . .

# Copy the entrypoint script to the container
# Start the application
EXPOSE 3000
CMD ["node", "app.js"]