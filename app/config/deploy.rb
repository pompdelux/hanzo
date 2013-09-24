#
# Hanzo / Pompdelux deploy - common stuff used by both production and testing
#

# needed to get verbose output. -v doesnt work. Use below to see commands run if deploy fails
# logger.level = Logger::MAX_LEVEL

set :update_vendors, false

set :application, "Hanzo"
set :app_path,    "app"

set :repository,  "git@github.com:bellcom/hanzo.git"
set :scm,         :git

set :stage_dir, 'app/config/deploy'
require 'capistrano/ext/multistage'
set :stages,        %w(testing production)
set :default_stage, "testing"

# use composer for symfony 2.1
set :use_composer, true
set :composer_bin, "/usr/local/bin/composer"

# dont delete web/app_* please
set :clear_controllers, false

set :whoami, `whoami`.strip
set :hostname, `hostname`.strip
set :pwd, `pwd`.strip
set :hosts, ENV["HOSTS"]

# do dump the js and css with uglify
set :dump_assetic_assets,   true

# use below to rsync the files instead of git clone. Requires capistrano_rsync_with_remote_cache installed (gem install)
set :deploy_via,  :rsync_with_remote_cache
# use other rsync_options. Default is: -az --delete
#set :rsync_options, "-rltoDzO --delete"
#set :rsync_options, "-rltzO --delete"
set :rsync_options, "-rltzO --delete --exclude=.git"

set :group_writable, true

#set :deployed_user, "www-data"
set :deployed_group, "www-data"

# will run propel:model:build on each environment if set to propel
set :model_manager, "propelXX"

set :keep_releases,  5

set :shared_files,      ["app/config/parameters.ini", "app/config/hanzo.yml", "cron/config.php"]

set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", web_path + "/images", web_path + "/video", web_path + "/pdfupload"]

set :use_sudo, false

ssh_options[:forward_agent] = true

# own rules for running tasks after deploy
after 'deploy:restart', 'deploy:symlinks', 'symfony:cache:assets_update', 'symfony:cache:redis_clear', 'deploy:apcclear', 'symfony:cache:varnish_clear', 'deploy:cleanup', 'deploy:update_permissions', 'deploy:update_permissions_shared', 'deploy:send_email'
## also clear redis when calling cache:clear
after 'symfony:cache:clear', 'symfony:cache:redis_clear', 'symfony:cache:varnish_clear'
# mail after rollback and warn about clearing cache. Doesnt seem to work with "after 'deploy:rollback", because it tries to clear the old current dir
after 'deploy:rollback', 'deploy:send_email_rollback', 'deploy:rollback_warning'
# send diff mails after updating code
#enable when we are sure it works
after "deploy:update_code", "deploy:pending:default"

## own tasks. copy apc-clear.php, apcclear and reload apache tasks
namespace :deploy do
  desc "Roll out apc-clear.php"
  task :copy_apcclear, :roles => :apache do
    run("wget -q --output-document=/var/www/apc-clear.php http://tools.bellcom.dk/hanzo/apc-clear.php.txt")
  end
  desc "Clear apc cache on the local server"
  task :apcclear, :roles => :apache do
    run("wget -q -O /dev/null http://localhost/apc-clear.php")
  end
  desc "Reload apache"
  task :reload_apache, :roles => :apache do
    run("sudo /etc/init.d/apache2 reload")
  end
# fix permissions
  desc "Update permissions on shared app logs and web dirs to be group writeable"
  task :update_permissions do
    run "sudo chmod -R g+rwX #{current_release} && sudo chgrp -R www-data #{current_release}"
  end
# fix permissions. shouldnt run on static because of pdfs and ftp?
  desc "Update permissions on shared app logs and web dirs to be group writeable"
  task :update_permissions_shared do
    run "sudo chmod -R g+rwX #{shared_path}/app && sudo chgrp -R www-data #{shared_path}/app"
    run "sudo chmod -R g+rwX #{shared_path}/cron && sudo chgrp -R www-data #{shared_path}/cron"
    run "sudo chmod -R g+rwX #{shared_path}/cached-copy && sudo chgrp -R www-data #{shared_path}/cached-copy"
    run "sudo chmod -R g+rwX #{shared_path}/vendor && sudo chgrp -R www-data #{shared_path}/vendor"
  end
  desc "Send email after deploy"
  task :send_email do
    run_locally "echo 'New deploy of hanzo branch: #{branch}. New current release: #{current_release}. Run from: #{hostname}:#{pwd}. By user: #{whoami} (#{hosts})' | mail -s 'Hanzo #{branch} deployed' -c hd@pompdelux.dk -c lv@pompdelux.dk -c un@bellcom.dk mmh@bellcom.dk"
  end
  desc "Send email after rollback"
  task :send_email_rollback do
    run_locally "echo 'Rollback of hanzo branch: #{branch}. New current release: #{current_release}. Run from: #{hostname}:#{pwd}. By user: #{whoami} (#{hosts})' | mail -s 'Hanzo #{branch} rolled back' -c hd@pompdelux.dk -c lv@pompdelux.dk -c un@bellcom.dk mmh@bellcom.dk"
  end
  desc "Rollback warning"
  task :rollback_warning do
    puts "ROLLBACK! The cache might need to be cleared? Run:";puts "cap #{branch} symfony:cache:clear"
  end
# create symlinks
  desc "Create logs and public_html symlinks"
  task :symlinks do
    run("cd #{deploy_to}/current;if [ ! -L logs ];then ln -s app/logs logs;fi;if [ ! -L public_html ];then ln -s web public_html;fi")
  end
  desc "Send deploy to New Relic"
  task :newrelic_notify do
    run_locally("curl -s -H 'x-api-key:75916fb33fa70e01ddca4bd9a761c56989504595a94d03a' -d 'deployment[application_id]=697785' -d 'deployment[host]=#{hostname}' -d 'deployment[user]=#{whoami}' https://rpm.newrelic.com/deployments.xml")
  end
# send diff mails
  namespace :pending do
    desc <<-DESC
      Show the commits since the last deploy
    DESC
    task :default, :except => { :no_release => true } do
      deployed_already = current_revision
      puts "deployed_already: #{deployed_already}"
      to_be_deployed = `cd .rsync_cache && git rev-parse --short "HEAD" && cd ..`.strip
      puts "to_be_deployed: #{to_be_deployed}"
      puts "cd .rsync_cache && git log --no-merges --pretty=format:'* %s %b (%cn)' #{to_be_deployed}..#{deployed_already};cd .."
#      deploydiff2 = `cd .rsync_cache && git log --no-merges --pretty=format:"* %s %b (%cn)" #{to_be_deployed}..#{deployed_already}`
#      puts "#{deploydiff2}"
      puts "cd .rsync_cache && git log --no-merges --pretty=format:'* %s %b (%cn)' #{deployed_already}..#{to_be_deployed};cd .."
      deploydiff = `cd .rsync_cache && git log --no-merges --pretty=format:"* %s %b (%cn)" #{deployed_already}..#{to_be_deployed}`
      puts "#{deploydiff}"
      system("echo 'Starting deploy of hanzo branch: #{branch}. New current release: #{current_release}. Run from: #{hostname}:#{pwd}. By user: #{whoami} (#{hosts}). Whats new:\n#{deploydiff}'| mail -s 'Hanzo #{branch} deploy started' mmh@bellcom.dk")
    end
  end
end

# own task. Clear the redis cache
namespace :symfony do
  namespace :cache do
    desc "Clear/Flush redis cache"
    task :redis_clear, :roles => :redis do
      run("cd #{latest_release} && php app/console hanzo:redis:cache:clear --env=#{symfony_env_prod}")
    end
    desc "Update assets version"
    task :assets_update, :roles => :static do
      symfony_env_prods.each do |i|
        run("cd #{latest_release} && php app/console hanzo:dataio:update assets_version --env=#{i}")
      end
    end
    # Clear Varnish
    desc "Empty the varnish cache"
    task :varnish_clear, :roles => :redis do
      run("cd #{latest_release} && php app/console hanzo:varnish:purge --env=#{symfony_env_prod}")
    end
  end
end

# own task. Run propel migrations
namespace :propel do
  namespace :migration do
    desc "Run migrations"
    task :migrate, :roles => :db do
      symfony_env_prods.each do |i| 
        run("cd #{latest_release} && php app/console propel:migration:migrate --env=#{i}")
      end
    end
    desc "Migrations status"
    task :status, :roles => :db do
      symfony_env_prods.each do |i| 
        run("cd #{latest_release} && php app/console propel:migration:status --env=#{i}")
      end
    end
  end
end


## FROM symfony2.rb - Overridden here to add loop over environments
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
        # don't try to chmod. We have umask in app.php
        #run "#{try_sudo} chmod -R g+w #{latest_release}/#{cache_path}"
        capifony_puts_ok
      end
    end
  end
  # We seem to only run assets:install on pdladmin/redis. Dont know why. Maybe it should be static? But the assets should also be pusted to git.
  namespace :assets do
    desc "Installs bundle's assets"
    task :install, :roles => :redis, :except => { :no_release => true } do
      capifony_pretty_print "--> Installing bundle's assets"

      install_options = ''

      if true == assets_symlinks then
        install_options += " --symlink"
      end

      if true == assets_relative then
        install_options += " --relative"
      end

      symfony_env_prods.each do |i|
        run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} assets:install #{web_path}#{install_options} --env=#{i}'"
      end
      capifony_puts_ok
    end
  end

# FROM symfony2/symfony.rb - Overridden here to only run assetic dump on static server. We dont loop environments because css and js is combined for all
  namespace :assetic do
    desc "Dumps all assets to the filesystem"
    task :dump, :roles => :static,  :except => { :no_release => true } do
      capifony_pretty_print "--> Dumping all assets to the filesystem"

      run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} assetic:dump --env=#{symfony_env_prod} --no-debug'"
      capifony_puts_ok
    end
  end
end


