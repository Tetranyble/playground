# Stage 1: Install dependencies
FROM composer:2 AS backend_builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts

COPY . .

# Stage 2: Final image
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Ensure the Alpine package repositories are correctly set up and update package index
RUN echo "https://dl-cdn.alpinelinux.org/alpine/v3.19/main" > /etc/apk/repositories \
    && echo "https://dl-cdn.alpinelinux.org/alpine/v3.19/community" >> /etc/apk/repositories \
    && apk update

# Install necessary PHP extensions and dependencies
RUN apk --no-cache add nginx curl bash supervisor tzdata \
    && apk --no-cache add php82 php82-fpm php82-mysqli php82-json php82-openssl php82-curl \
       php82-zlib php82-xml php82-phar php82-intl php82-dom php82-xmlreader \
       php82-ctype php82-session php82-mbstring php82-gd php82-pdo php82-pdo_mysql php82-tokenizer \
    && ln -s /usr/sbin/php-fpm82 /usr/bin/php-fpm \
    && docker-php-ext-install pdo pdo_mysql

# Copy built Laravel backend from stage 1
COPY --from=backend_builder /app .

# Copy Nginx and PHP-FPM configuration files
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisor.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
