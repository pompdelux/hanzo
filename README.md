# Hanzo - PDL shop v2

[![Build Status](https://magnum.travis-ci.com/pompdelux/hanzo.png?token=UA2TnLisELk6rr7prsvr&branch=master)](https://magnum.travis-ci.com/pompdelux/hanzo)

## Requirements:

First off, the same requirements as [symfony2](http://symfony.com/doc/2.0/reference/requirements.html), besides this, we have these addons:

1. [redis](http://redis.io/) must be installed and working
2. cdn must be setup (this is the same repos as hanzo see docs/vhost.cdn.conf)
3. Apache or Nginx must be setup up (Apache with mod_rewrite)
4. PHP modules needed (recommended)
    * Zend OPcache
    * PDO (mysql)
    * Curl
    * Imagick
    * [phpredis](https://github.com/nicolasff/phpredis) native c extension til at kører sessions i redis, kræver også [NativeSession](https://github.com/drak/NativeSession) til symfony.
6. [Beanstalkd](http://kr.github.io/beanstalkd/) must be instaled

These are only required on dev servers:

1. Compass and Sass [compass](http://compass-style.org/install/)
2. [Grunt](http://gruntjs.com/) must be installed


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
7. `php bin/vendors install`
8. Setup apache, see `dosc/vhost.conf` for an example
9. Install grunt depenencies
 	1. `npm install`
10. Run `grunt watch` in a shell to keep files up2date


## Configuration:

To access a dev page, the structure is as follows, {locale} is one of the configured locales: `http://yourdomain.tld/app_dev.php/{locale}` or `http://yourdomain.tld/app_test.php/{locale}` prod is never used, well - in production but else...

At the moment `da_DK`, `sv_SE`, `nb_NO`, `nl_NL`, `en_GB`, `de_DE`, 'de_CH` and `de_AT` is configued.

Please notice, you need to have the utf8 locales for these languages installed (ex: da_DK.utf8) !


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


## Themes

- [Sass](http://sass-lang.com/)
- [Compass](http://compass-style.org/)

Assets for themes are located under `fx/THEME/`

###Create a new theme with compass:

1. `cd fx`
2. `compass create THEME_NAME --css-dir "css" --javascripts-dir "scripts" --images-dir "images"`

--

Styles are grouped in seperate `.scss` files. e.g. Payment styles are located in `_payment.scss` and importet in `style.scss` (@import "payment"). All sub `.scss` files which should be imported into another instead of being a independet css file, should be prepended with a `_` like `_account.scss`. This way they wont be compiled themself.

The directory of a theme will look like this when built with compass (note that compass only generates the `sass` and `css` folders):

- `fx/THEME/`
  - `css/`
     - `style.scss`
     - `ie.css`
  - `scripts/`
  - `images/`
  - `sass/`
     - `_base.scss`
     - `_header.scss`
     - `_footer.scss`
     - `ie.scss`
     - `style.scss`
  - `config.rb`

Follow the [best practices](http://compass-style.org/help/tutorials/best_practices/)

--

- `_base.scss` - Includes all globale variables and @imports
- `style.scss` - Main stylesheet which includes _base and others
  - `payment.scss` - Styles for payment
  - ...


## Vagrant

There is a vagrant box provider, it requires:

- vagrant version 1.6+
- a running mysql server on your host computer
- a running redis server on your host computer

To use, simply `cd` to the root directory of the project and do a

```bash
$ vagrant up
```

It will take forever (the first time) - when done, you should be able to go to `http://pdl.dev/` and `http://c.pdl.dev/`

To ssh into the box, do a:

```bash
$ vagrant ssh
```

To shut it down:

```bash
$ vagrant halt
```

If the page returns a "nginx - bad gateway" error run the following commands:

```bash
$ vagrant ssh
$ sudo chown www-data. /var/run/php5-fpm*
```

## Beanstalkd

Is for processing orders, so they are send "to ax" in dev mode - and you get your confirmation emails.

To run the beanstalk jobs you do:

```bash
$ php app/console hanzo:ax:pheanstalk-worker --limit=10 --verbose --env=dev_dk
```

where `--limit=10` can be anything, its the number of jobs executes before quitting - remember not to set it to 100.000, php does not handle memory that well...

also, you should always remember to restart the jobs when changing any related code.


## Supervisord

There is a config file for supervisord in [docs/supervisord-hanzo.conf](docs/supervisord-hanzo.conf) - this is a copy of the configuration currently in production.
