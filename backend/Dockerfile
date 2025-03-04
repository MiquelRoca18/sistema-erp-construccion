FROM php:8.1-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar el código fuente
COPY . /var/www/html/

# Configurar DocumentRoot para apuntar a public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Cambiar permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto
EXPOSE 80

# Configurar puerto dinámicamente
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground