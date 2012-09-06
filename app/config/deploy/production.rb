#
# Hanzo / Pompdelux production deploy. 
#

set :deploy_to,   "/var/www/pompdelux" 

set :symfony_env_prods, ["prod_dk", "prod_se", "prod_no", "prod_com", "prod_nl", "prod_fi", "prod_dk_consultant", "prod_se_consultant", "prod_no_consultant", "prod_nl_consultant", "prod_fi_consultant"]

set :adminserver, "pdladmin"

# if we ever get other brances, specify which one to deploy here
set   :branch, "master"

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


