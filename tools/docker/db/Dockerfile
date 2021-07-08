FROM debian:wheezy
MAINTAINER Henrik Farre <henrik@bellcom.dk>

ENV DEBIAN_FRONTEND noninteractive
ENV DEBIAN_PRIORITY critical
ENV DEBCONF_NOWARNINGS yes
ENV MYSQL_PASS my5QLpw

# Default config
RUN echo tzdata tzdata/Areas select Europe | debconf-set-selections; \
  echo tzdata tzdata/Zones/Europe select Copenhagen | debconf-set-selections; \
  echo mysql-server mysql-server/root_password password ${MYSQL_PASS} | debconf-set-selections;\
  echo mysql-server mysql-server/root_password_again password ${MYSQL_PASS} | debconf-set-selections;

# Update and install
RUN apt-get -q -y update;\
  apt-get -q -y install wget;\
  wget --quiet -O - http://www.dotdeb.org/dotdeb.gpg | apt-key add -

ADD ./add-files/dotdeb.list /etc/apt/sources.list.d/dotdeb.list

RUN apt-get -q -y update;\
  apt-get -q -y upgrade;\
  apt-get -q -y install ssh vim pwgen locales mysql-server-5.5 redis-server redis-tools beanstalkd

# Locales
RUN sed -i -e 's/# da_DK.UTF-8/da_DK.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# en_DK.UTF-8/de_DE.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# de_DE.UTF-8/de_DE.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# fi_FI.UTF-8/fi_FI.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# nb_NO.UTF-8/nb_NO.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# nl_NL.UTF-8/nl_NL.UTF-8/' /etc/locale.gen;\
  sed -i -e 's/# sv_SE.UTF-8/sv_SE.UTF-8/' /etc/locale.gen;\
  locale-gen

# Root password + fix /var/www/ permissions to allow ssh pub key auth
RUN echo "root:root" | chpasswd;\
  chsh -s /bin/bash www-data;

# Redis / mysql / beanstalkd setup
RUN sed -i -e 's/bind 127.0.0.1/# bind 127.0.0.1/' /etc/redis/redis.conf;\
 sed -i -e 's/bind-address\s\+= 127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/my.cnf;\
 sed -i -e 's/#START=yes/START=yes/' /etc/default/beanstalkd;

# phpmyadmin
# file exists in /var/www (volume)
# ADD add-files/root.my.cnf /root/.my.cnf
ADD ./add-files/dot.bashrc /root/.bashrc

ADD ./add-files/startup-in-container.sh /opt/startup-in-container.sh

ADD ./add-files/utf8-default.cnf /etc/mysql/conf.d/utf8-default.cnf
ADD ./add-files/locale /etc/default/locale

# Redis / mysql / beanstalkd
EXPOSE 6379 3306 11300

ENTRYPOINT ["/opt/startup-in-container.sh"]

# So that we at somepoint can do stuff in startup-in-container.sh
CMD ["--dev"]
