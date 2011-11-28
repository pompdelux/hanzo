Hanzo - PDL shop v2
===================

Install:

1. `git clone git@github.com:bellcom/hanzo.git`
2. `cd hanzo`
3. `php bin/vendors install`
4. copy app/config/parameters.ini.dist to app/config/parameters.ini
5. change any settings necessary to connect to your database
4. copy app/config/config.yml.dist to app/config/config.yml
6. change propel dns and/or other settings
7. `php app/console propel:database:create`
8. `php app/console hanzo:router:builder`
9. setup apache, see dosc/vhost.conf for an example
