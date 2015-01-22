#
# Hanzo / Pompdelux production deploy.
#

set :deploy_to,   "/var/www/pompdelux"

# default environment, used by default functions
set :symfony_env_prod, "prod_dk"
set :symfony_env_prods, ["prod_ch", "prod_at", "prod_de", "prod_fi", "prod_se", "prod_no", "prod_com", "prod_nl", "prod_dk", "prod_ch_consultant", "prod_at_consultant", "prod_de_consultant", "prod_fi_consultant", "prod_se_consultant", "prod_no_consultant", "prod_nl_consultant", "prod_dk_consultant"]

set :adminserver, "pdladmin1.bellcom.dk"
set :staticserver, "pdlstatic1.bellcom.dk"

set :branch, "master"

# list of servers to deploy to
role :app, 'pdlweb1.bellcom.dk', 'pdlweb2.bellcom.dk', 'pdlweb3.bellcom.dk', 'pdlweb4.bellcom.dk', 'pdladmin1.bellcom.dk', 'pdlstatic1.bellcom.dk'

# :symfonyweb should contain our apache/nginx servers running symfony. Used in reload_apache and opcode-clear
role :symfonyweb, 'pdlweb1.bellcom.dk', 'pdlweb2.bellcom.dk', 'pdlweb3.bellcom.dk', 'pdlweb4.bellcom.dk', 'pdladmin1.bellcom.dk'

# our redis server. clear cache here
role :redis, adminserver, :primary => true

# our static server. run assets dumps here
role :static, staticserver, :primary => true

# where to run migrations. :db is also a default and may be used internally.
role :db, adminserver, :primary => true  # where to run migrations

# only notify New Relic on production deploys
after 'deploy:send_email', 'deploy:newrelic_notify', 'deploy:post_dashing'

before 'deploy:send_email', 'deploy:run_pagespeed'

# own tasks. copy config
namespace :deploy do
  desc "Copy default parameters.ini to shared dir"
  task :copy_prod_config do
    capifony_pretty_print "--> Copying parameters.ini"
    run("mkdir -p #{shared_path}/app/config/ && wget -q --output-document=#{shared_path}/app/config/parameters.ini http://tools.bellcom.dk/hanzo/parameters.ini")
    capifony_puts_ok
  end
# post deploy to new relic. Only for prod
  desc "Send deploy to New Relic"
  task :newrelic_notify do
    capifony_pretty_print "--> Sending deploy info to New Relic"
    run_locally("curl -s -H 'x-api-key:9c7777826c9d0aed79810e82e0d07dacde3a7e94a94d03a' -d 'deployment[app_name]=Pompdelux' -d 'deployment[user]=#{whoami}' https://rpm.newrelic.com/deployments.xml")
    capifony_puts_ok
  end
# post deploy to new Pepper Potts dashing
  desc "Post deploy to Pepper Potts dashing"
  task :post_dashing do
    capifony_pretty_print "--> Posting to dashing (pepper-potts.pompdelux.com:3030)"
    set :now, `date "+%d/%m %H:%M:%S"`.strip
    run_locally "curl -s -d '{\"auth_token\": \"puz6Raejpuz6Raej\", \"text\": \"#{now}<br />af #{whoami}\" }' http://pepper-potts.pompdelux.com:3030/widgets/lastdeploy"
    capifony_puts_ok
  end
# run pagespeed test and save the result for later use in mail
  desc "Run google pagespeeds tests on the site"
  task :run_pagespeed, :except => { :no_release => true } do
    capifony_pretty_print "--> Running Pagespeed tests"
    set :pagespeed_desktop, `if [ -x /usr/local/bin/psi ]; then /usr/local/bin/psi --strategy=desktop http://www.pompdelux.com/da_DK/dreng-110-152-cm | tr -s " ";else echo "psi not found, skipping pagespeed test";fi`
    set :pagespeed_mobile, `if [ -x /usr/local/bin/psi ]; then /usr/local/bin/psi --strategy=mobile http://www.pompdelux.com/da_DK/dreng-110-152-cm | tr -s " ";else echo "psi not found, skipping pagespeed test";fi`
    capifony_puts_ok
  end
# overriding the global send_email to add pagespeed only in prod deploy
  desc "Send email after deploy"
  task :send_email do
    capifony_pretty_print "--> Sending deploy status mail"
    run_locally "echo 'New deploy of hanzo branch: #{branch}\nNew current release: #{current_release}\nRun from: #{hostname}:#{pwd}\nBy user: #{whoami}\nOn hosts (empty if all): #{hosts}\nWhats new:\n#{deploydiff}\n\nPagespeed tests:#{pagespeed_desktop}#{pagespeed_mobile}' | mail -s 'Hanzo #{stage} deployed' -c hd@pompdelux.dk -c cc@pompdelux.dk pdl@bellcom.dk"
    capifony_puts_ok
  end
end
