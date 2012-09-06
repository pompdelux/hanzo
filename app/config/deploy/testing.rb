#
# Hanzo / Pompdelux testing deploy. 
#

set :deploy_to,   "/var/www/testpompdelux" 

#symfony_env_prods = ["test_dk", "test_se", "test_no", "test_com", "test_nl"]
set :symfony_env_prods, ["test_dk", "test_dk_consultant"]
#symfony_env_prods = ["test_dk", "test_se", "test_no"]

set :adminserver, "pomp-test"

# if we ever get other brances, specify which one to deploy here
set   :branch, "testing"

#set :update_vendors, true
set :update_vendors, false

role(:web) do
   frontend_test_list
end
# :app is the same list of servers as :web. its default and may be used internally?
role(:app) do
   frontend_test_list
end

role :apache, "pomp-test"

# our redis server. clear cache here
role :redis, adminserver, :primary => true

# where to run migrations. :db is also a default and may be used internally.
role :db, adminserver, :primary => true  # where to run migrations

# function to get web frontends from a file
def frontend_test_list
  contentsArray = Array.new
  contentsArray = File.readlines("tools/deploy/frontend_test_list.txt")
end
# own tasks. copy config, copy apc-clear.php and apcclear task
namespace :deploy do
  desc "Copy default parameters.ini and hanzo.yml to shared dir"
  task :copy_test_config do
    run("mkdir -p #{shared_path}/app/config/ && wget -q --output-document=#{shared_path}/app/config/parameters.ini http://tools.bellcom.dk/hanzo/parameters_testing.ini && wget -q --output-document=#{shared_path}/app/config/hanzo.yml http://tools.bellcom.dk/hanzo/hanzo_testing.yml")
  end
end

