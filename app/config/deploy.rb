#
# Hanzo / Pompdelux deploy - common stuff used by both production, testing and dev
#

# Use below to get more verbose output. Will show all commands
# logger.level = Logger::MAX_LEVEL

set :update_vendors, false

set :application, "Hanzo"
set :app_path,    "app"

set :repository,  "git@github.com:pompdelux/hanzo.git"
set :scm,         :git

set :stage_dir, 'app/config/deploy'
require 'capistrano/ext/multistage'
set :stages,        %w(dev testing production)
set :default_stage, "testing"

# use composer for symfony 2.1
set :use_composer, true
set :composer_bin, "/usr/local/bin/composer"
set :composer_options, "--no-dev --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction"

# dont delete web/app_* please
set :clear_controllers, false

set :whoami, `whoami`.strip
set :hostname, `hostname`.strip
set :pwd, `pwd`.strip
set :hosts, ENV["HOSTS"]

# do dump the js and css with uglify
# un@2014.05.12 depricated, should be removed along with any other assetic
# related stuff, but do so when we are 100% certain it works as expected
set :dump_assetic_assets, false

# use below to rsync the files instead of git clone. Requires capistrano_rsync_with_remote_cache installed (gem install)
set :deploy_via,  :rsync_with_remote_cache
# use other rsync_options. Default is: -az --delete
#set :rsync_options, "-rltoDzO --delete"
set :rsync_options, "-rltzO --delete --exclude=.git"

set :group_writable, true

set :deployed_group, "www-data"

# will run propel:model:build on each environment if set to propel
set :model_manager, "propelXX"

set :keep_releases,  3

set :shared_files,      ["app/config/parameters.ini", "cron/config.php", "app/config/products_id_map.php"]

set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", web_path + "/images", web_path + "/video", web_path + "/pdfupload"]

set :use_sudo, false

ssh_options[:forward_agent] = true

# set an initial deploydiff so rollback works
set :deploydiff, "Nothing - Rollback maybe?"

# own rules for running tasks after deploy
after 'deploy:restart', 'deploy:symlinks', 'symfony:cache:assets_update', 'symfony:cache:redis_clear', 'deploy:restart_beanstalkd_worker', 'deploy:opcode_clear', 'symfony:cache:varnish_clear', 'deploy:cleanup', 'deploy:clear_opcode', 'deploy:update_permissions', 'deploy:update_permissions_shared', 'deploy:update_permissions_releases'
# send_email moved here. dont want a deploy email on rollback
after 'deploy', 'deploy:send_email', 'deploy:graphite_notify'
## also clear redis and varnish when calling cache:clear
after 'symfony:cache:clear', 'symfony:cache:redis_clear', 'symfony:cache:varnish_clear', 'deploy:update_permissions', 'deploy:update_permissions_releases'
# mail after rollback and warn about clearing cache. Doesn't seem to work with "after 'deploy:rollback", because it tries to clear the old current dir
after 'deploy:rollback', 'deploy:send_email_rollback', 'deploy:rollback_warning'
# save whats new diff just after updating the code. This might break an initial deploy even if using deploy:cold
after 'deploy:update_code', 'deploy:pending_updates'
# have an update all permissions
after 'deploy:update_permissions_all', 'deploy:update_permissions', 'deploy:update_permissions_shared', 'deploy:update_permissions_releases'
# create logs dir after setup to allow nginx to start
after 'deploy:setup', 'deploy:create_log_dir'

## own tasks. copy apc-clear.php, apcclear and reload apache tasks
namespace :deploy do
  desc "Roll out tools.php for apc clearing"
  task :copy_apcclear, :roles => :symfonyweb do
    capifony_pretty_print "--> Copying the old tools.php to the server"
    run("sudo wget -q --output-document=/var/www/tools.php http://tools.bellcom.dk/hanzo/tools.php.txt")
    capifony_puts_ok
  end
  desc "Clear apc cache on the local server"
  task :apcclear, :roles => :symfonyweb do
    run("wget -q -O /dev/null http://localhost/tools.php?action=apc-clear")
  end
  desc "Clear OPcache or apc on the local server"
  task :opcode_clear, :roles => :symfonyweb do
    capifony_pretty_print "--> Clearing opcode cache, APC or OPcache"
    run("wget -q -O /dev/null http://localhost/tools.php?action=opcode-clear")
    capifony_puts_ok
  end
  desc "Reload apache"
  task :reload_apache, :roles => :symfonyweb do
    capifony_pretty_print "--> Reloading apache"
    run("sudo /etc/init.d/apache2 reload")
    capifony_puts_ok
  end
  desc "Reload php5fpm"
  task :reload_php5fpm, :roles => :symfonyweb do
    capifony_pretty_print "--> Reloading php5-fpm"
    run("sudo /etc/init.d/php5-fpm reload")
    capifony_puts_ok
  end
  desc "Reload nginx"
  task :reload_nginx, :roles => :symfonyweb do
    capifony_pretty_print "--> Reloading nginx"
    run("sudo /etc/init.d/nginx reload")
    capifony_puts_ok
  end
  desc "Switch to nginx"
  task :use_nginx, :roles => :symfonyweb do
    capifony_pretty_print "--> Stopping Apache, starting nginx/php5-fpm"
    run "sudo /etc/init.d/apache2 stop"
    run "sudo /etc/init.d/php5-fpm start"
    run "sudo /etc/init.d/nginx start"
    run "sudo /usr/sbin/update-rc.d -f apache remove"
    run "sudo /usr/sbin/update-rc.d nginx defaults"
    run "sudo /usr/sbin/update-rc.d php5-fpm defaults"
    capifony_puts_ok
  end
  desc "Switch to apache"
  task :use_apache, :roles => :symfonyweb do
    capifony_pretty_print "--> Stopping nginx/php5-fpm, starting Apache"
    run "sudo /etc/init.d/nginx stop"
    run "sudo /etc/init.d/php5-fpm stop"
    run "sudo /etc/init.d/apache2 start"
    run "sudo /usr/sbin/update-rc.d -f nginx remove"
    run "sudo /usr/sbin/update-rc.d -f php5-fpm remove"
    run "sudo /usr/sbin/update-rc.d apache2 defaults"
    capifony_puts_ok
  end
# fix permissions all
  desc "Update permissions on everything"
  task :update_permissions_all do
    capifony_pretty_print "--> Setting correct permissions..."
    capifony_puts_ok
  end
# fix permissions
  desc "Update permissions on the current replease dir to be group writeable"
  task :update_permissions do
    capifony_pretty_print "--> Setting correct permissions for /current"
    run "sudo chmod -R g+rwX #{current_release} && sudo chgrp -R www-data #{current_release}"
    capifony_puts_ok
  end
# fix permissions. shouldnt run on static because of pdfs and ftp?
  desc "Update permissions on shared app logs and web dirs to be group writeable"
  task :update_permissions_shared do
    capifony_pretty_print "--> Setting correct permissions for /shared"
    run "sudo chmod -R g+rwX #{shared_path}/app && sudo chgrp -R www-data #{shared_path}/app"
    run "sudo chmod -R g+rwX #{shared_path}/cron && sudo chgrp -R www-data #{shared_path}/cron"
    run "sudo chmod -R g+rwX #{shared_path}/cached-copy && sudo chgrp -R www-data #{shared_path}/cached-copy"
    run "sudo chmod -R g+rwX #{shared_path}/vendor && sudo chgrp -R www-data #{shared_path}/vendor"
    capifony_puts_ok
  end
# fix permissions on old releases dir.
  desc "Update permissions on old releases dirs to be group writeable"
  task :update_permissions_releases do
    capifony_pretty_print "--> Setting correct permissions for /releases"
    run "sudo chmod -R g+rwX #{deploy_to}/releases && sudo chgrp -R www-data #{deploy_to}/releases"
    capifony_puts_ok
  end
# create log dir for nginx to startup
  desc "Create shared log dir"
  task :create_log_dir do
    capifony_pretty_print "--> Creating shared/app/logs"
    run "mkdir -p #{shared_path}/app/logs"
    capifony_puts_ok
  end
  desc "Send email after deploy"
  task :send_email do
    capifony_pretty_print "--> Sending deploy status mail"
    run_locally "echo 'New deploy of hanzo branch: #{branch}\nNew current release: #{current_release}\nRun from: #{hostname}:#{pwd}\nBy user: #{whoami}\nOn hosts (empty if all): #{hosts}\nWhats new:\n#{deploydiff}' | mail -s 'Hanzo #{stage} deployed' -c it-drift@pompdelux.dk pdl@bellcom.dk"
    capifony_puts_ok
  end
  desc "Send email after rollback"
  task :send_email_rollback do
    capifony_pretty_print "--> Sending rollback status mail"
    run_locally "echo 'Rollback of hanzo branch: #{branch}\nRun from: #{hostname}:#{pwd}\nBy user: #{whoami}\nOn hosts (empty if all): (#{hosts})' | mail -s 'Hanzo #{stage} rolled back' -c it-drift@pompdelux.dk pdl@bellcom.dk"
    capifony_puts_ok
  end
  desc "Rollback warning"
  task :rollback_warning do
    puts "ROLLBACK! The autoloader and cache might need to be cleared? Run:";puts "cap #{stage} symfony:composer:dump_autoload";puts "cap #{stage} symfony:cache:clear"
  end
# create symlinks
  desc "Create logs and public_html symlinks"
  task :symlinks do
    capifony_pretty_print "--> Creating symlinks for public_html and logs"
    run("cd #{deploy_to}/current;if [ ! -L logs ];then ln -s app/logs logs;fi;if [ ! -L public_html ];then ln -s web public_html;fi")
    capifony_puts_ok
  end
# save whats new in a variable used later in send_email
  desc "Show the commits since the last deploy"
  task :pending_updates, :except => { :no_release => true } do
    capifony_pretty_print "--> Getting changelog from git"
    deployed_already = previous_revision
    to_be_deployed = `cd .rsync_cache && git rev-parse --short "HEAD" && cd ..`.strip
    set :deploydiff, `cd .rsync_cache && git log --no-merges --pretty=format:"* %s %b (%cn)" #{deployed_already}..#{to_be_deployed}`.gsub("'", "\"")
    capifony_puts_ok
  end
  # restart supervisord job
  desc "Restarting supervisor hanzo beanstalkd job"
  task :restart_beanstalkd_worker, :roles => :redis do
    capifony_pretty_print "--> Restarting supervisor hanzo beanstalkd job"
    run("supervisorctl restart hanzo:*")
    capifony_puts_ok
  end
# post deploy to graphite
  desc "Send deploy to Graphite"
  task :graphite_notify do
    capifony_pretty_print "--> Sending deploy info to Graphite"
    run_locally("echo \"deploys.#{stage}.pompdelux:1|c\" | nc -w 1 -u 10.0.0.51 8125")
    capifony_puts_ok
  end

end

namespace :symfony do
  namespace :cache do
    # own task. Clear the redis cache
    desc "Clear/Flush redis cache"
    task :redis_clear, :roles => :redis do
      capifony_pretty_print "--> Clearing redis cache"
      run("cd #{latest_release} && php app/console hanzo:redis:cache:clear --env=#{symfony_env_prod}")
      capifony_puts_ok
    end
    desc "Update assets version"
    task :assets_update, :roles => :static do
      capifony_pretty_print "--> Updating assets"
      symfony_env_prods.each do |i|
        run("cd #{latest_release} && php app/console hanzo:dataio:update assets_version --env=#{i}")
      end
      capifony_puts_ok
    end
    # own task. Clear Varnish
    desc "Empty the varnish cache"
    task :varnish_clear, :roles => :redis do
      capifony_pretty_print "--> Clearing varnish cache"
      run("cd #{latest_release} && php app/console hanzo:varnish:purge --env=#{symfony_env_prod}")
      capifony_puts_ok
    end
  end
  namespace :composer do
    # own task. Update the composer binary
    desc "Run composer self-update"
    task :selfupdate do
      capifony_pretty_print "--> Self-updating composer"
      run "sudo /usr/local/bin/composer self-update"
      capifony_puts_ok
    end
  end
end

# own tasks. Run propel migrations
namespace :propel do
  namespace :migration do
    desc "Run migrations"
    task :migrate, :roles => :db do
      capifony_pretty_print "--> Running propel migrations"
      symfony_env_prods.each do |i|
        run("cd #{latest_release} && php app/console propel:migration:migrate --env=#{i}")
      end
      capifony_puts_ok
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
          #run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} cache:#{action.to_s} --env=#{i} #{console_options}'"
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
# FROM /var/lib/gems/1.8/gems/capifony-2.4.1/lib/symfony2/symfony.rb - Overridden here to only run assetic dump on static server. We dont loop environments because css and js is combined for all
  namespace :assetic do
    desc "Dumps all assets to the filesystem"
    task :dump, :roles => :static,  :except => { :no_release => true } do
      capifony_pretty_print "--> Dumping all assets to the filesystem"

      #run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} assetic:dump --env=#{symfony_env_prod} #{console_options} #{latest_release}/#{web_path}'"
      run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} assetic:dump --env=#{symfony_env_prod} --no-debug'"
      capifony_puts_ok
    end
  end
end

