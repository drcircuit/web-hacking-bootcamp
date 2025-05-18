FROM php:7.4-apache
ENV DEBIAN_FRONTEND=noninteractive
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    iputils-ping \
    sqlite3 \
    libsqlite3-dev \
    unzip \
    && docker-php-ext-install pdo pdo_sqlite

ENV APACHE_DOCUMENT_ROOT /var/www/html

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
RUN mkdir -p /var/www/html/db
# make sure the db folder is writable
RUN chown -R www-data:www-data /var/www/html/db
RUN chmod 755 /var/www/html/db
RUN php /var/www/html/init_db.php
RUN chmod 644 /var/www/html/db/evilcorp_crm.sqlite
RUN chown -R www-data:www-data /var/www/html/db/evilcorp_crm.sqlite
# Enable SHA512 password hashing
RUN sed -i 's/^ENCRYPT_METHOD.*/ENCRYPT_METHOD SHA512/' /etc/login.defs && \
    sed -i 's/^password.*pam_unix.so.*/password [success=1 default=ignore] pam_unix.so obscure sha512/' /etc/pam.d/common-password

# Set up SSH with Thanos user
RUN apt-get update && apt-get install -y openssh-server supervisor && \
    useradd -m thanos && echo "thanos:invincible" | chpasswd -c SHA512 && \
    mkdir -p /var/run/sshd && \
    echo "PermitRootLogin yes" >> /etc/ssh/sshd_config && \
    echo "PasswordAuthentication yes" >> /etc/ssh/sshd_config

# Welcome message flag
RUN echo 'echo "Welcome, Thanos. FLAG: WCH{thanos_password_cracked}"' >> /home/thanos/.profile && \
    chown thanos:thanos /home/thanos/.bashrc
COPY supervisord.conf /etc/supervisord.conf

# Create the injected note for command injection challenge
RUN mkdir -p /opt/netlog && \
    echo 'Internal diagnostic log:\n\nAll clear on node3, log rotated.\n\nFLAG: WCH{cmd_injection_network_recon}' > /opt/netlog/.internal_note && \
    chmod 644 /opt/netlog/.internal_note
# Create secret.tpl for template injection challenge
RUN mkdir -p /var/www/private && \
    echo 'WCH{tpl_eval_abused_successfully}' > /var/www/private/secret.tpl && \
    chmod 644 /var/www/private/secret.tpl

RUN echo "<?php\n// FLAG: WCH{tpl_secret_leak_detected}\n" > /var/www/html/config.php
# create templates dir
RUN mkdir -p /var/www/html/templates && \
    chown -R www-data:www-data /var/www/html/templates
RUN echo "<p>This is the default email template preview.</p>" > /var/www/html/templates/default_email.php

# Create a fake backup location
RUN mkdir -p /opt/backup && \
    chown -R www-data:www-data /opt/backup && \
    chmod 755 /opt/backup
# Extract Thanos' real shadow hash into a fake backup location
RUN grep '^thanos:' /etc/shadow > /opt/backup/shadow.bak && \
    chmod 644 /opt/backup/shadow.bak
    
# add flag WCH{shadow_hash_backup_found}to shadow.bak
RUN echo 'FLAG: WCH{shadow_hash_backup_found}' >> /opt/backup/shadow.bak && \
    chown -R www-data:www-data /opt/backup/shadow.bak
# final flag in .bashrc
RUN echo 'echo "FLAG: WCH{evilcorp_host_compromised_ssh}"' >> /home/thanos/.bashrc && \
    chown thanos:thanos /home/thanos/.bashrc
# Expose ports
EXPOSE 80 22

# Start Supervisor to manage Apache and SSH
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]