FROM php:8.2-cli

# Install PHP extensions (optional)
RUN docker-php-ext-install mysqli

# Copy files to container
COPY . /var/www/html
WORKDIR /var/www/html

# Expose port
EXPOSE 10000

# Start built-in server
CMD ["php", "-S", "0.0.0.0:10000"]
