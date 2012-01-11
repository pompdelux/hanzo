Hanzo - PDL shop v2
===================

Requirements:

First off, the same requirements as [symfony2(http://symfony.com/doc/2.0/reference/requirements.html)], besides this, we have these addons:

1. [redis](http://redis.io/) must be installed and working
2. cdn.hanzo must be setup, for now - please ask
3. Java must be installed (to compile compressed js and css files - we use yuicompressor)
4. Apache must be setup with mod_rewrite
5. Apc for php is also a must-have module.

Install:

1. `git clone git@github.com:bellcom/hanzo.git`
2. `cd hanzo`
3. Copy app/config/parameters.ini.dist to app/config/parameters.ini
  1. Change any settings necessary to connect to your database
4. Copy app/config/config.yml.dist to app/config/config.yml
  1. Change propel dns and/or other settings
5. Copy app/config/hanzo.yml.dist to app/config/hanzo.yml
  1. Change cdn and/or other settings
6. `php bin/vendors install`
7. `php app/console propel:database:create`
8. `php app/console hanzo:router:builder`
9. Setup apache, see dosc/vhost.conf for an example
10. `git submodule update --recursive --init`

Configuration:

The system relies heavily on domain names and tld's. So if you use none standard names, these should be mapped via `tld_map: {}` in app/config/hanzo.yml
