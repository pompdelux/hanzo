#
# Hanzo / Pompdelux dev deploy
#

set :deploy_to,   "/var/www/devpompdelux"

# default environment, used by default functions
set :symfony_env_prod, "test_dk"
set :symfony_env_prods, ["test_ch", "test_at","test_de", "test_fi", "test_se", "test_no", "test_com", "test_nl", "test_dk", "test_dk_consultant"]

set :adminserver, "pdldev"
set :staticserver, "pdldev-db-static"

set :branch, "testing"

# list of server to deploy to
role :app, 'pdldev', 'pdldev-db-static'

# :symfonyweb should contain our apache/nginx servers. Used in reload_apache and opcache-clear
role :symfonyweb, "pdldev"

# our redis server. clear cache here
role :redis, adminserver, :primary => true

# our static server. run assets dumps here
role :static, staticserver, :primary => true

# where to run migrations. :db is also a default and may be used internally.
role :db, adminserver, :primary => true  # where to run migrations

# own tasks. copy config
namespace :deploy do
  desc "Copy default parameters.ini to shared dir"
  task :copy_dev_config do
    run("mkdir -p #{shared_path}/app/config/ && wget -q --output-document=#{shared_path}/app/config/parameters.ini http://tools.bellcom.dk/hanzo/parameters_dev.ini")
  end
end
