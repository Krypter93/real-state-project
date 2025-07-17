FROM php:8.2-apache

# Install PHP extensions required for MySQL connection
RUN docker-php-ext-install pdo pdo_mysql

# Copy application source
COPY . /var/www/html/

# Expose web server port
EXPOSE 80

# Start Apache web server
CMD ["apache2-foreground"]
