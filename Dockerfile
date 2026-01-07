FROM php:8.1-apache

# เปิดใช้งาน module rewrite ของ apache (เผื่อมึงทำ URL สวยๆ)
RUN a2enmod rewrite

# ติดตั้ง extension mysqli ให้คุยกับ database รู้เรื่อง
RUN docker-php-ext-install mysqli pdo pdo_mysql
