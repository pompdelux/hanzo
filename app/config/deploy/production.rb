#
# Hanzo / Pompdelux production deploy.
#

set :deploy_to,   "/var/www/pompdelux"

# default environment, used by default functions
set :symfony_env_prod, "prod_dk"
set :symfony_env_prods, ["prod_ch", "prod_at", "prod_de", "prod_fi", "prod_se", "prod_no", "prod_com", "prod_nl", "prod_dk", "prod_ch_consultant", "prod_at_consultant", "prod_de_consultant", "prod_fi_consultant", "prod_se_consultant", "prod_no_consultant", "prod_nl_consultant", "prod_dk_consultant"]

set :adminserver, "pdladmin"
set :staticserver, "pdlstatic1"

set :branch, "master"

# list of servers to deploy to
role :app, 'pdlfront-dk3', 'pdlfront-dk2', 'pdlfront-no1', 'pdlfront-nl1', 'pdlfront-fi1', 'pdlfront-dk4', 'pdlfront-dk5', 'pdlfront-de1', 'pdladmin', 'pdlkons-dk1', 'pdlstatic1'

# :symfonyweb should contain our apache/nginx servers running symfony. Used in reload_apache and opcode-clear
role :symfonyweb, 'pdlfront-dk3', 'pdlfront-dk2', 'pdlfront-no1', 'pdlfront-nl1', 'pdlfront-fi1', 'pdlfront-dk4', 'pdlfront-dk5', 'pdlfront-de1', 'pdladmin', 'pdlkons-dk1'

# our redis server. clear cache here
role :redis, adminserver, :primary => true

# our static server. run assets dumps here
role :static, staticserver, :primary => true

# where to run migrations. :db is also a default and may be used internally.
role :db, adminserver, :primary => true  # where to run migrations

# only notify New Relic on production deploys
after 'deploy:send_email', 'deploy:newrelic_notify', 'deploy:post_dashing'

# own tasks. copy config
namespace :deploy do
  desc "Copy default parameters.ini and hanzo.yml to shared dir"
  task :copy_prod_config do
    capifony_pretty_print "--> Copying parameters.ini and hanzo.yml"
    run("mkdir -p #{shared_path}/app/config/ && wget -q --output-document=#{shared_path}/app/config/parameters.ini http://tools.bellcom.dk/hanzo/parameters.ini && wget -q --output-document=#{shared_path}/app/config/hanzo.yml http://tools.bellcom.dk/hanzo/hanzo.yml")
    capifony_puts_ok
  end
# own tasks. copy vhost
  desc "Copy default vhost from stat"
  task :copy_vhost, :roles => :symfonyweb do
    run("sudo wget -q --output-document=/etc/apache2/sites-available/pompdelux http://tools.bellcom.dk/hanzo/pompdelux-vhost.txt")
  end
# own tasks. enable vhost
  desc "Enable vhost from stat"
  task :enable_vhost, :roles => :symfonyweb do
    run("sudo a2ensite pompdelux")
  end
# post deploy to new relic. Only for prod
  desc "Send deploy to New Relic"
  task :newrelic_notify do
    capifony_pretty_print "--> Sending deploy info to New Relic"
    run_locally("curl -s -H 'x-api-key:9c7777826c9d0aed79810e82e0d07dacde3a7e94a94d03a' -d 'deployment[app_name]=Pompdelux' -d 'deployment[user]=#{whoami}' https://rpm.newrelic.com/deployments.xml")
    capifony_puts_ok
  end
  desc "Post deploy to Pepper Potts dashing"
  task :post_dashing do
    capifony_pretty_print "--> Posting to dashing (pepper-potts.pompdelux.com:3030)"
    set :now, `date "+%d/%m %H:%M:%S"`.strip
    run_locally "curl -s -d '{\"auth_token\": \"puz6Raejpuz6Raej\", \"text\": \"#{now}<br />af #{whoami}\" }' http://pepper-potts.bellcom.dk:3030/widgets/lastdeploy"
    capifony_puts_ok
  end
end
