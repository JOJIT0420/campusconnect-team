FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into Apache web root
COPY . /var/www/html/

# Set file ownership permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80