# Hanzo - PDL shop v2

## Episode IV: Revenge of osCommerce

## Requirements:

First off, the same requirements as [symfony2](http://symfony.com/doc/2.0/reference/requirements.html), besides this, we have these addons:

1. [redis](http://redis.io/) must be installed and working
2. [cdn.hanzo](https://github.com/bellcom/cdn.hanzo) must be setup
3. Java must be installed (to compile compressed js and css files - we use yuicompressor)
4. Apache must be setup with mod_rewrite
5. Apc for php is also a must-have module.

## Install:

1. `git clone git@github.com:bellcom/hanzo.git`
2. `cd hanzo`
3. Copy app/config/parameters.ini.dist to app/config/parameters.ini
  1. Change any settings necessary to connect to your database
4. Copy app/config/hanzo.yml.dist to app/config/hanzo.yml
  1. Change cdn and/or other settings
5. `mysql -u xxx -p yyy hanzo < app/propel/sql/default.sql`
6. add fixture data, here we have a "clean" and a "full" version, the full version includes testdata.
  1. `mysql -u xxx -p yyy hanzo < app/propel/fixtures/fixtures.sql`
  2. `mysql -u xxx -p yyy hanzo < app/propel/fixtures/fixtures_all.sql`
7. `git submodule update --recursive --init`
8. `git submodule foreach --recursive git checkout master`
9. `git submodule foreach --recursive git pull origin master`
10. `php bin/vendors install`
11. Setup apache, see `dosc/vhost.conf` for an example

## Configuration:

The system relies heavily on domain names and tld's. So if you use none standard names, these should be mapped in web/app_dev.php and web/app_test.php


## Misc

Follow the [coding standards](http://symfony.com/doc/current/contributing/code/standards.html) as much as possible.

symfony console:

- `php app/console --help` for help on the cli interface for symfony
- clearing caches:
  - `php app/console cache:clear --env=dev_dk`
  - `php app/console cache:clear --env=test_dk`
  - `php app/console cache:clear --env=prod_dk`
- clearing redis cache:
  - `php app/console hanzo:redis:cache:clear --env=prod_dk`

Note the `env` parameter, this is important, stuff vill break if not ... oh! and run the command as the web user

redis:

- `redis-cli` is the commandline interface for handeling redis related tasks
  redis supports tab-completion
- flushing the cache: `FLUSHALL`
- find a key: `KEYS *xx*`
- help, well: `HELP` or go see the [docs](http://redis.io/documentation), they are great.

