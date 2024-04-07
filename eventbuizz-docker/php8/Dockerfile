FROM php:8.0-fpm

#system updates
RUN apt-get update

#system required packages
RUN apt-get install -y cron wget nano curl git zip unzip zlib1g-dev libzip-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev exiftool

RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl

# install php extensions
RUN docker-php-ext-install bcmath pdo_mysql zip
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-configure exif  
RUN docker-php-ext-install exif  
RUN docker-php-ext-enable exif  

# Install nodejs
RUN curl -sL https://deb.nodesource.com/setup_10.x -o nodesource_setup.sh && bash nodesource_setup.sh && apt-get -y --force-yes install nodejs npm
RUN npm install forever -g

#install wkhtmltopdf & wkhtmltopng
#RUN wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.stretch_amd64.deb
#RUN apt-get install -y ./wkhtmltox_0.12.6-1.stretch_amd64.deb

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 9000