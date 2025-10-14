FROM php:8.1-cli-alpine

# Устанавливаем необходимые расширения и зависимости для Xdebug
RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    linux-headers \
    $PHPIZE_DEPS

# Устанавливаем Xdebug для coverage
RUN pecl install xdebug-3.3.2 \
    && docker-php-ext-enable xdebug

# Конфигурация Xdebug для coverage (без отладки)
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаем рабочую директорию
WORKDIR /app

# Копируем composer файлы для кеширования слоя с зависимостями
COPY composer.json composer.lock* ./

# Устанавливаем зависимости (если есть composer.lock)
RUN composer install --no-scripts --no-autoloader --prefer-dist || true

# Копируем весь проект
COPY . .

# Генерируем autoload
RUN composer dump-autoload --optimize

# Устанавливаем права
RUN chmod -R 755 /app

CMD ["php", "-v"]
