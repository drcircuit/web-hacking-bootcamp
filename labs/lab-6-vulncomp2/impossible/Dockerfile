# Use Node.js 14 (widely compatible with sqlite3)
FROM node:24

# Install build tools and Python required by node-gyp before npm install
RUN apt-get update && \
    apt-get install -y python3 make g++

# Create and change to the app directory
WORKDIR /usr/src/app

# Copy dependency manifests first
COPY package*.json ./

# Install production dependencies
RUN npm install

# Copy the rest of the application
COPY . .

# Expose the app port
EXPOSE 3000

# Start the app
CMD ["npm", "start"]
