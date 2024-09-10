# Sử dụng hình ảnh chính thức của PHP
FROM php:8.1-fpm

# Cài đặt các phần mở rộng cần thiết
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Sao chép mã nguồn Laravel vào container
COPY . /var/www

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Chạy lệnh composer install
RUN composer install

# Gán quyền cho thư mục storage và bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 9000 và start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
