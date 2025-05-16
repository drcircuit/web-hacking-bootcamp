# Use the latest Kali Linux rolling release as the base image
FROM kalilinux/kali-rolling:latest

# Update package lists and install tools
RUN apt-get update && apt-get install -y \
    nmap \
    dnsutils \ 
    gobuster \
    seclists \
    hydra \
    openssh-client \ 
    net-tools \ 
    iputils-ping \ 
    traceroute \ 
    curl \
    git \
    vim \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /root
COPY custom-words.txt /root/custom-words.txt
# Default command to keep container running interactively
CMD ["/bin/bash"]