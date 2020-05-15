FROM php:7.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    vim \
    openssl \
    curl \
    wget \
    gnupg \
    apt-transport-https \
    lsb-release \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    libmagickwand-dev \
    libghc-postgresql-libpq-dev \
    libbz2-dev \
    libfontconfig && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install calendar zip intl mbstring pdo_mysql bcmath opcache pdo
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd
RUN docker-php-ext-install bz2
RUN docker-php-ext-install soap

RUN pecl install imagick
RUN docker-php-ext-enable imagick

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

COPY ./config/php.ini /usr/local/etc/php/

WORKDIR /code

# Make sure we're installing what we think we're installing!
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
    && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
    && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
    && rm -f /tmp/composer-setup.*

# We should use another container for this, since we are mixing PHP and Node
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash - \
    && apt-get install -y nodejs

# Installing yarn
RUN curl -o- -L https://yarnpkg.com/install.sh | bash

RUN sed -i -e "s/pm.max_children\s*=\s*[0-9]*/pm.max_children = 30/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i -e "s/pm.max_spare_servers\s*=\s*[0-9]*/pm.max_spare_servers = 10/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i -e "s/;pm.max_requests\s*=\s*[0-9]*/pm.max_requests = 500/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i -e "s/access.log/;access.log/g" /usr/local/etc/php-fpm.d/docker.conf

# Set ENV for yarn
ENV PATH "$PATH:/root/.yarn/bin"

EXPOSE 9000
CMD ["php-fpm"]