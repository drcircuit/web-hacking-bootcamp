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

# Setup user and hashed password using SHA512
RUN useradd -m thanos && \
    echo 'thanos:$6$saltstring$9FA34UZ2RbcweWyh8Z5oJduTGSv0xZB9LflDIjQO/4fxD6m7AKcT0rGlxFtyoLsmTgOqa2yZXTrDhJmWGEyR.1' >> /etc/shadow && \
    echo "thanos:x:1000:1000::/home/thanos:/bin/bash" >> /etc/passwd

COPY web/ /var/www/html/
COPY thanos.bashrc /home/thanos/.bashrc
COPY supervisord.conf /etc/supervisord.conf

# Setup file ownership
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chown thanos:thanos /home/thanos/.bashrc

WORKDIR /var/www/html
# make db dir and set owership to www-data
RUN mkdir -p /var/www/html/db && \
    chown -R www-data:www-data /var/www/html/db
RUN php init_db.php

# set owner of *.sqlite files to www-data
RUN chown -R www-data:www-data /var/www/html/db/*.sqlite

RUN mkdir -p /var/www/html/backups && \
    cp /var/www/html/db/evilcorp_crm.sqlite /var/www/html/backups/evilcorp_crm.sqlite.bak && \
    chown www-data:www-data /var/www/html/backups/evilcorp_crm.sqlite.bak

RUN echo "Options +Indexes" > /var/www/html/backups/.htaccess && \
    chown www-data:www-data /var/www/html/backups/.htaccess

EXPOSE 80 22

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]