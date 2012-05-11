set :domain,      "pompdelux-test" # hf@bellcom.dk: _Skal_ være en af dem som er defineret i rollerne
set :deploy_to,   "/var/www/test.hanzo.dk"
set :symfony_env_prod, "test"

# Your HTTP server, Apache/etc
role(:web) do
   frontend_test_list
end
# This may be the same as your `Web` server
role(:app) do
   frontend_test_list
end

def frontend_test_list
  contentsArray = Array.new
  contentsArray = File.readlines("tools/deploy/frontend_test_list.txt")
end

role :db, domain, :primary => true  # This is where Rails migrations will run

