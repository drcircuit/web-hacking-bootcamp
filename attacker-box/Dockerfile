# Use the latest Kali Linux rolling release as the base image
FROM kalilinux/kali-rolling:latest
# set environment variable to avoid interactive prompts for kali
ENV DEBIAN_FRONTEND=noninteractive
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
    python3 \
    python3-pip \
    vim \
    kali-tools-passwords \
    nano \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /root
COPY custom-words.txt /root/custom-words.txt
# copy scripts folder
COPY scripts /root/scripts
# Setup shell
RUN apt-get update && apt-get install -y bash-completion
# Configure bash for a better shell experience
RUN echo "source /etc/bash.bashrc" >> /root/.bashrc && \
    echo "source /usr/share/bash-completion/bash_completion" >> /root/.bashrc && \
    echo "export PS1='\[\e[1;32m\]\u@\h:\[\e[1;34m\]\w\[\e[0m\]\$ '" >> /root/.bashrc && \
    echo "alias ls='ls --color=auto'" >> /root/.bashrc && \
    echo "alias ll='ls -la --color=auto'" >> /root/.bashrc && \
    echo "alias grep='grep --color=auto'" >> /root/.bashrc && \
    echo "export HISTCONTROL=ignoreboth" >> /root/.bashrc && \
    echo "export HISTSIZE=1000" >> /root/.bashrc && \
    echo "export HISTFILESIZE=2000" >> /root/.bashrc && \
    echo "export TERM=xterm-256color" >> /root/.bashrc

# Install oh-my-bash for an enhanced bash experience
RUN git clone https://github.com/ohmybash/oh-my-bash.git /root/.oh-my-bash && \
    cp /root/.oh-my-bash/templates/bashrc.osh-template /root/.bashrc && \
    sed -i 's/OSH_THEME="font"/OSH_THEME="powerline"/' /root/.bashrc && \
    echo "source /usr/share/bash-completion/bash_completion" >> /root/.bashrc && \
    echo "export TERM=xterm-256color" >> /root/.bashrc

# Fix terminal input by ensuring readline bindings
RUN echo "set -o vi" >> /root/.bashrc && \
    echo "bind -m vi-insert '\e':backward-kill-word" >> /root/.bashrc

# Decompress rockyou.txt.gz for use with john
RUN gunzip -k /usr/share/wordlists/rockyou.txt.gz && \
    chmod 644 /usr/share/wordlists/rockyou.txt

# Default command to start a login shell
CMD ["/bin/bash", "-l"]