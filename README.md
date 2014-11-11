# Hanzo - PDL shop v2

[![Build Status](https://magnum.travis-ci.com/pompdelux/hanzo.png?token=UA2TnLisELk6rr7prsvr&branch=master)](https://magnum.travis-ci.com/pompdelux/hanzo)

## Requirements:

First off, the same requirements as [symfony2](http://symfony.com/doc/current/reference/requirements.html), besides this, we have these addons:

1. [redis](http://redis.io/) must be installed and working
2. cdn must be setup (this is the same repos as hanzo see docs/vhost.cdn.conf)
3. Apache or Nginx must be setup up (Apache with mod_rewrite)
4. PHP version > 5.5
5. PHP modules needed
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
5. Get at least the _dk database from testpompdelux from @mmh and load that
	1. the one from live is too big, so don't try ;)
6. `php bin/vendors install`
7. Setup apache, see `dosc/vhost.conf` for an example
8. Install grunt depenencies
 	1. `npm install`
9. Run `grunt watch` in a shell to keep files up2date


## Configuration:

Note, to ease the flow, we use `app_dev.php` as "index" on locale dev and `test_dev.php` on test - so you do not use the environment files directly.

To access a dev page, the structure is as follows, {locale} is one of the configured locales: `http://yourdomain.tld/{locale}` or `http://yourdomain.tld/app_test.php/{locale}` prod is never used, well - in production but else...

At the moment `da_DK`, `sv_SE`, `nb_NO`, `nl_NL`, `en_GB`, `de_DE`, `de_CH` and `de_AT` is configured.

Please notice, you need to have all the utf8 locales for these languages installed (ex: da_DK.utf8) !


## Misc

* Follow the [coding standards](http://symfony.com/doc/current/contributing/code/standards.html) as much as possible.
* Use a codesniffer php-cs is recomended.

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
- flushing the cache: `FLUSHDB`
	-  **never ever** use `FLUSHALL`
- find a key: `KEYS *xx*`
- list all keys: `KEYS *`
- help, well: `HELP` or go see the [docs](http://redis.io/documentation), they are great.

Currently we use redis for:

* caching
	* short term - db: 0, port: 6379
	* permanent - db: 2, port: 6379
* sessions - db: 1, port: 6380
* stock levels - db: 5, port: 6379

On dev we only use one redis server, so here the port number is 6379 (default)

## Themes

- [Sass](http://sass-lang.com/)
- [Compass](http://compass-style.org/)

Assets for themes are located under `web/fx/THEME/`

###Create a new theme with compass:

1. `cd web/fx`
2. `compass create THEME_NAME --css-dir "css" --javascripts-dir "scripts" --images-dir "images"`

--

Styles are grouped in seperate `.scss` files. e.g. Payment styles are located in `_payment.scss` and importet in `style.scss` (@import "payment"). All sub `.scss` files which should be imported into another instead of being a independet css file, should be prepended with a `_` like `_account.scss`. This way they wont be compiled themself.

The directory of a theme will look like this when built with compass (note that compass only generates the `sass` and `css` folders):

- `web/fx/THEME/`
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

All assets (css and js) are compiled using `grunt`, all assets needed by a theme are added to the file `resources.json` in the theme folder, eks: `app/Resources/themes/2013s1/resources.json`


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

