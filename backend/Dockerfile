FROM php:8.1-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install zip pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar el código fuente
COPY . /var/www/html/

# Configurar DocumentRoot para apuntar a public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Cambiar permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto
EXPOSE 80

# Configurar puerto dinámicamente
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground

# Añadir a tu Dockerfile existente (después de las otras extensiones)
RUN pecl install apcu && docker-php-ext-enable apcu

# Configurar APCu para producción
RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini
RUN echo "apc.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini
RUN echo "apc.ttl=300" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini