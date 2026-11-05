FROM php:8.3-apache-bookworm

RUN apt-get update \
  && apt-get install -y curl gnupg2 unixodbc-dev $PHPIZE_DEPS \
  && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
  && curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list -o /etc/apt/sources.list.d/mssql-release.list \
  && apt-get update \
  && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
  && pecl install sqlsrv pdo_sqlsrv \
  && docker-php-ext-enable sqlsrv pdo_sqlsrv

RUN a2enmod rewrite \
  && sed -ri 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/\" /etc/apache2/sites-available/000-default.conf && exec apache2-foreground"]
