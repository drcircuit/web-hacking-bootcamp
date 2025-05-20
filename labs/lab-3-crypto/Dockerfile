FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    openssh-server \
    supervisor \
    unzip \
    && docker-php-ext-install pdo pdo_sqlite

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    mkdir -p /var/run/sshd && \
    a2enmod rewrite

RUN useradd -m thanos && \
    echo 'thanos:$6$xKZ5EZA0vqjr5ACZ$jG9yftjy2NtP.iJThuHM1LkIhst3XEC2mRfp3GNdtQ9JMZhDvZL4/Y6l5Az5IL5SNxKkK1.zG6wBbRDAvZF240' >> /etc/shadow && \
    echo "thanos:x:1000:1000::/home/thanos:/bin/bash" >> /etc/passwd


COPY web/ /var/www/html/
COPY thanos.bashrc /home/thanos/.bashrc
COPY supervisord.conf /etc/supervisord.conf

# Setup file ownership
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chown thanos:thanos /home/thanos/.bashrc

WORKDIR /var/www/html
# make db dir and set www-data as owner
RUN mkdir -p /var/www/html/db 
RUN chown -R www-data:www-data /var/www/html/db
COPY vault_plain.txt /var/www/html/vault_plain.txt
RUN php init_db.php
# set permissions for all *.sqlite files to www-data
RUN chmod -R 755 /var/www/html/db && \
    chown -R www-data:www-data /var/www/html/db/*.sqlite
RUN rm -f /var/www/html/db/vault_plain.txt
# set owner of *.sqlite files to www-data
RUN chown -R www-data:www-data /var/www/html/db/*.sqlite

RUN mkdir -p /var/www/html/backups && \
    cp /var/www/html/db/evilcorp_crm.sqlite /var/www/html/backups/evilcorp_crm.sqlite.bak && \
    chown www-data:www-data /var/www/html/backups/evilcorp_crm.sqlite.bak

RUN echo "Options +Indexes" > /var/www/html/backups/.htaccess && \
    chown www-data:www-data /var/www/html/backups/.htaccess

# Install zip if not present
RUN apt-get update && apt-get install -y zip

# Install tools and create the system log for challenge 6
RUN mkdir -p /var/www/html/logs && \
    echo '[INFO] 2025-05-19 12:00:00 - Login attempt - user: admin' > /var/www/html/logs/system.log&& echo '[DEBUG] 2025-05-19 12:00:00 - Auth hash: 5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8' >> /var/www/html/logs/system.log && echo '[INFO] 2025-05-19 12:00:00 - Login successful' >> /var/www/html/logs/system.log && echo '[INFO] 2025-05-19 12:00:00 - Login attempt - user: thanos' >> /var/www/html/logs/system.log && echo '[DEBUG] 2025-05-19 12:00:00 - Auth hash: fb7accbae065dd6a0417aeed7299564d3f58c168    ' >> /var/www/html/logs/system.log && echo '[INFO] 2025-05-19 12:00:00 - Login successful' >> /var/www/html/logs/system.log
# Create password-protected ZIP file for step 5
RUN echo "Flag: WCH{zip_passwords_are_weak}\nNext: /logs/system.log" > /var/www/html/flag_step5.txt && \
    zip -P secretpass /var/www/html/step5_challenge.zip /var/www/html/flag_step5.txt && \
    rm /var/www/html/flag_step5.txt

EXPOSE 80 22

# Install pycryptodome for RSA encryption
RUN apt-get update && apt-get install -y python3-pip && pip3 install pycryptodome
# Copy encryption script and plaintext flag
COPY scripts/encrypt_rsa.py /scripts/encrypt_rsa.py
COPY scripts/gen_blob.py /scripts/gen_blob.py
RUN chmod +x /scripts/encrypt_rsa.py
RUN chmod +x /scripts/gen_blob.py
COPY report_plain.txt /tmp/report_plain.txt
# Run encryption and cleanup
RUN python3 /scripts/encrypt_rsa.py && rm -f /tmp/report_plain.txt
# remove plaintext report_plain.txt
RUN rm -f /tmp/report_plain.txt
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]