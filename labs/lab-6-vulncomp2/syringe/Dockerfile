FROM node:24

# Install Python and build tools BEFORE npm install
RUN apt-get update && apt-get install -y python3 make g++

WORKDIR /usr/src/app

# Copy dependencies first
COPY package*.json ./

# Install deps after system deps are present
RUN npm install

# Copy the rest of the app
COPY . .

# Default port
EXPOSE 3000

# Start the app
CMD ["node", "app.js"]
