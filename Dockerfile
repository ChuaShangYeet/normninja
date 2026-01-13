# 1. Base image
FROM php:8.2-apache

# 2. Install system deps
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath gd

# 3. Enable mod_rewrite for Laravel
RUN a2enmod rewrite

# 4. Configure PHP upload limits
RUN echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# 5. Set working directory
WORKDIR /var/www/html/

# 6. Copy project files
COPY . .

# 7. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 8. Build frontend if exists
RUN npm install && npm run build || echo "No frontend build"

# 9. Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 10. Fix Apache root to Laravel public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 11. Expose port 80 (Render uses $PORT automatically)
EXPOSE 80

# 12. Start Apache
CMD ["apache2-foreground"]
