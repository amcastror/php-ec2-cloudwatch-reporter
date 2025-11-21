FROM php:8.2-cli

WORKDIR /app

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app code
COPY . /app

RUN composer install --no-interaction

CMD ["php", "src/main.php"]