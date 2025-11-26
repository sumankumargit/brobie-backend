FROM php:8.2-apache

# Enable rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Allow .htaccess rules
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Correctly configure Apache to use Railway dynamic port env
RUN echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Expose (Railway ignores this but fine)
EXPOSE 80

CMD ["apache2-foreground"]
