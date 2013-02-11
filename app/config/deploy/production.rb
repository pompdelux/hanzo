#
# Hanzo / Pompdelux production deploy. 
#

# needed to get verbose output. -v doesnt work
logger.level = Logger::MAX_LEVEL

set :deploy_to,   "/var/www/pompdelux" 

set :symfony_env_prods, ["prod_dk", "prod_se", "prod_no", "prod_com", "prod_nl", "prod_fi", "prod_dk_consultant", "prod_se_consultant", "prod_no_consultant", "prod_nl_consultant", "prod_fi_consultant"]

set :adminserver, "pdladmin"
set :staticserver, "pdlstatic1"

# if we ever get other brances, specify which one to deploy here
set   :branch, "master"

# use composer for symfony 2.1
set :use_composer, true
set :composer_bin, "/usr/local/bin/composer"

# dont delete web/app_* please
set :clear_controllers, false

#set :update_vendors, true
set :update_vendors, false

# list of production frontend servers fetched from a file. If we dont need the list for other serivces/scripts, move it back here.
role(:web) do
   frontend_list
end
# :app is the same list of servers as :web. its default and may be used internally.
role(:app) do
   frontend_list
end
# :apache should contain our apache servers. NICETO. Bare drop pdlstatic1 fra frontend_list array'et i stedet for endnu en fil
role(:apache) do
   frontend_list_apache
end

# our redis server. clear cache here
role :redis, adminserver, :primary => true

# our redis server. clear cache here
role :static, staticserver, :primary => true

# where to run migrations. :db is also a default and may be used internally.
role :db, adminserver, :primary => true  # where to run migrations

# function to get web frontends from a file
def frontend_list
  contentsArray = Array.new
  contentsArray = File.readlines("tools/deploy/frontend_list.txt")
end
def frontend_list_apache
  contentsArray = Array.new
  contentsArray = File.readlines("tools/deploy/frontend_list_apache.txt")
end

# only notify New Relic on production deplos
after 'deploy:send_email', 'deploy:newrelic_notify'


# own tasks. copy config, copy apc-clear.php and apcclear task
namespace :deploy do
  desc "Copy default parameters.ini and hanzo.yml to shared dir"
  task :copy_prod_config do
    run("mkdir -p #{shared_path}/app/config/ && wget -q --output-document=#{shared_path}/app/config/parameters.ini http://tools.bellcom.dk/hanzo/parameters.ini && wget -q --output-document=#{shared_path}/app/config/hanzo.yml http://tools.bellcom.dk/hanzo/hanzo.yml")
  end
# own tasks. copy vhost
  desc "Copy default vhost from stat"
  task :copy_vhost, :roles => :apache do
    run("sudo wget -q --output-document=/etc/apache2/sites-available/pompdelux http://tools.bellcom.dk/hanzo/pompdelux-vhost.txt")
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
