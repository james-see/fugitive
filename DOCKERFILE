FROM ubuntu:16.04
MAINTAINER James Campbell <james@jamescampbell.us>

RUN DEBIAN_FRONTEND=noninteractive

# ensure UTF-8
RUN locale-gen en_US.UTF-8
ENV LANG       en_US.UTF-8
ENV LC_ALL     en_US.UTF-8

# Update apt-get local index
RUN DEBIAN_FRONTEND="noninteractive" apt-get update -y --force-yes

# Install Redis
RUN DEBIAN_FRONTEND="noninteractive" apt-get -y --force-yes install redis-server

#Configure Redis
RUN sed -i -e"s/^bind\s*=\s*127.0.0.1/bind = 0.0.0.0/" /etc/redis/redis.conf

#Install NGINX
RUN DEBIAN_FRONTEND="noninteractive" apt-get -y install nginx

#Install PHP7
RUN DEBIAN_FRONTEND="noninteractive" apt-get -y install php7.0-fpm
RUN DEBIAN_FRONTEND="noninteractive" apt-get -y install php7.0-curl php7.0-gd php7.0-intl php-pear php-imagick php7.0-imap php7.0-mcrypt php-memcache php7.0-pspell php7.0-recode php7.0-sqlite3 php7.0-tidy php7.0-xmlrpc php7.0-xsl php7.0-mbstring php-gettext php7.0-redis
RUN DEBIAN_FRONTEND="noninteractive" apt-get -y install php-apcu

#Configure NGINX
RUN sed -i -e"s/keepalive_timeout\s*65/keepalive_timeout 2/" /etc/nginx/nginx.conf
ADD nginx/default /etc/nginx/sites-available/default
RUN sed -i -e "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g" /etc/php/7.0/fpm/php.ini

#Install apps
RUN DEBIAN_FRONTEND="noninteractive" apt-get -y install vim git sendmail htop 
RUN DEBIAN_FRONTEND="noninteractive" git clone https://github.com/jamesacampbell/fugitive.git
