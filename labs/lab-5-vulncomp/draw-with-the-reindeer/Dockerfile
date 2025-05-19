FROM node:24

# Install build dependencies
RUN apt-get update && \
    apt-get install -y python3 make g++ && \
    ln -sf /usr/bin/python3 /usr/bin/python

# Set working directory
WORKDIR /usr/src/app

# Copy package.json and package-lock.json
COPY package*.json ./

# Install npm dependencies
RUN npm install

# Copy the rest of the application
COPY . .

# Command to run the app (adjust as needed)
CMD ["npm", "start"]