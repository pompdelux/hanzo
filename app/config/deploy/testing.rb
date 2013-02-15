#
# Hanzo / Pompdelux testing deploy
#

set :deploy_to,   "/var/www/testpompdelux"

set :symfony_env_prod, "test_dk"
set :symfony_env_prods, ["test_dk", "test_se", "test_no", "test_com", "test_nl", "test_fi", "test_dk_consultant"]

set :adminserver, "pomp-test"
set :staticserver, "pomptest-db"

set :branch, "testing"

# list of server to deploy to
role :app, 'pomp-test', 'pomptest-db'

# :apache should contain our apache servers. Used in reload_apache and apcclear
role :apache, "pomp-test"

# our redis server. clear cache here
role :redis, adminserver, :primary => true

# our static server. run assets dumps here
role :static, staticserver, :primary => true

# where to run migrations. :db is also a default and may be used internally.
role :db, adminserver, :primary => true  # where to run migrations

# own tasks. copy config
namespace :deploy do
  desc "Copy default parameters.ini and hanzo.yml to shared dir"
  task :copy_test_config do
    run("mkdir -p #{shared_path}/app/config/ && wget -q --output-document=#{shared_path}/app/config/parameters.ini http://tools.bellcom.dk/hanzo/parameters_testing.ini && wget -q --output-document=#{shared_path}/app/config/hanzo.yml http://tools.bellcom.dk/hanzo/hanzo_testing.yml")
  end
end

# overridden because of chmod without sudo, and loop envinroments
namespace :symfony do
  namespace :cache do
    [:clear, :warmup].each do |action|
      desc "Cache #{action.to_s}"
      task action, :roles => :app, :except => { :no_release => true } do
        case action
        when :clear
          capifony_pretty_print "--> Clearing cache"
        when :warmup
          capifony_pretty_print "--> Warming up cache"
        end

        symfony_env_prods.each do |i|
          run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} cache:#{action.to_s} --env=#{i}'"
        end
        capifony_puts_ok
      end
    end
  end

end

