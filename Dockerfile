FROM mediawiki
RUN apt-get -y update \ 
&& apt-get install -y libicu-dev \ 
&& docker-php-ext-configure intl \ 
&& docker-php-ext-install intl
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN echo hi
# COPY . /var/www/html/wizzypedia
# RUN ls /var/www/html/wizzypedia
