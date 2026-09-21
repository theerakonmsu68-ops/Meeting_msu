FROM php:8.3-apache

# ติดตั้ง Certificate สำหรับเชื่อม TiDB ผ่าน TLS
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates \
    && update-ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# PDO MySQL สำหรับ MySQL / TiDB
RUN docker-php-ext-install pdo_mysql

# เปิด Apache modules ที่ระบบใช้งาน
RUN a2enmod rewrite headers

# อนุญาตให้ .htaccess ทำงาน
RUN printf '%s\n' \
    '<Directory /var/www/html>' \
    '    Options FollowSymLinks' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    > /etc/apache2/conf-available/meeting-msu.conf \
    && a2enconf meeting-msu

# ป้องกัน Apache เตือน ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# ตำแหน่งโปรเจกต์
WORKDIR /var/www/html

# Copy โปรเจกต์เข้า Container
COPY . /var/www/html/

# กำหนดสิทธิ์
RUN chown -R www-data:www-data /var/www/html

# ค่า Default สำหรับ Render
ENV PORT=10000

EXPOSE 10000

# ให้ Apache ใช้ PORT ที่ Render กำหนด
CMD ["sh", "-c", "sed -ri \"s/^Listen 80$/Listen ${PORT}/\" /etc/apache2/ports.conf && sed -ri \"s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]