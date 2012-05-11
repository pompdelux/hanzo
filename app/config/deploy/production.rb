set :domain,      "pdlfront-dk3" # hf@bellcom.dk: _Skal_ være en af dem som er defineret i rollerne
set :deploy_to,   "/var/www/hanzo.dk"
set :symfony_env_prod, "prod"

# Your HTTP server, Apache/etc
role(:web) do
   frontend_list
end
# This may be the same as your `Web` server
role(:app) do
   frontend_list
end

def frontend_list
  contentsArray = Array.new
  contentsArray = File.readlines("tools/deploy/frontend_list.txt")
end

role :db, domain, :primary => true  # This is where Rails migrations will run
