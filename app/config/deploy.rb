#
# Hanzo / Pompdelux deploy - common stuff used by both production and testing
#

# needed to get verbose output. -v doesnt work
logger.level = Logger::MAX_LEVEL

set :application, "Hanzo"
set :app_path,    "app"

set :repository,  "git@github.com:bellcom/hanzo.git"
set :scm,         :git

set :stage_dir, 'app/config/deploy'
require 'capistrano/ext/multistage'
set :stages,        %w(testing production)
set :default_stage, "testing"

# use composer for symfony 2.1. TODO, needed?
set :use_composer, true
set :composer_bin, "/usr/local/bin/composer"

# dont delete web/app_* please
set :clear_controllers, false

# TODO, is this needed?
set :update_vendors, false

set :whoami, `whoami`.strip
set :hostname, `hostname`.strip
set :pwd, `pwd`.strip
set :hosts, ENV["HOSTS"]

# do dump the js and css with uglify
set :dump_assetic_assets,   true

# mmh@bellcom.dk: use below to rsync the files instead of git clone. Requires capistrano_rsync_with_remote_cache installed (gem install)
set :deploy_via,  :rsync_with_remote_cache
# use other rsync_options. Default is: -az --delete
#set :rsync_options, "-rltoDzO --delete"
set :rsync_options, "-rltzO --delete"

#set :deployed_user, "www-data"
set :deployed_group, "www-data"

set :model_manager, "propel"

set :keep_releases,  5

set :shared_files,      ["app/config/parameters.ini", "app/config/hanzo.yml", "cron/config.php"]

set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", web_path + "/images", web_path + "/video", web_path + "/pdfupload"]

set :use_sudo, false

ssh_options[:forward_agent] = true

## our own added logic
#before 'symfony:assetic:dump', 'deploy:update_permissions'
#
#before 'symfony:cache:warmup', 'symfony:cache:assets_update'
#before 'symfony:cache:warmup', 'symfony:cache:redis_clear'
#
## also clear redis when calling cache:clear
#after 'symfony:cache:clear', 'symfony:cache:redis_clear'
## also clear apc when clearing cache
#after 'symfony:cache:clear', 'deploy:apcclear'
## also update permissions after cache:clear
#after 'symfony:cache:clear', 'deploy:update_permissions'
#
#before 'deploy:restart', 'deploy:symlinks'
#after 'deploy:restart', 'deploy:update_permissions'
#after 'deploy:restart', 'deploy:update_permissions_shared'
#after 'deploy:restart', 'deploy:cleanup'
#after 'deploy:restart', 'deploy:send_email'
#after 'deploy:cleanup', 'deploy:apcclear'
#after 'deploy:apcclear', 'symfony:cache:varnish_clear'
#
## clear cache after rollback. Doesnt seem to work because it tries to clear the old current dir
##after 'deploy:rollback', 'symfony:cache:clear'
## so warn instead and send an extra mail
#after 'deploy:rollback', 'deploy:send_email_rollback'
#after 'deploy:rollback', 'deploy:rollback_warning'
#
## own tasks. copy config, copy apc-clear.php and apcclear task
#namespace :deploy do
#  desc "Roll out apc-clear.php"
#  task :copy_apcclear, :roles => :apache do
#    run("wget -q --output-document=/var/www/apc-clear.php http://tools.bellcom.dk/hanzo/apc-clear.php.txt")
#  end
#  desc "Clear apc cache on the local server"
#  task :apcclear, :roles => :apache do
#    run("wget -q -O /dev/null http://localhost/apc-clear.php")
#  end
#  desc "Reload apache"
#  task :reload_apache, :roles => :apache do
#    run("sudo /etc/init.d/apache2 reload")
#  end
## fix permissions
#  desc "Update permissions on shared app logs and web dirs to be group writeable"
#  task :update_permissions do
#    run "sudo chmod -R g+rwX #{current_release} && sudo chgrp -R www-data #{current_release}"
#  end
## fix permissions. shouldnt run on static because of pdfs and ftp?
#  desc "Update permissions on shared app logs and web dirs to be group writeable"
#  task :update_permissions_shared do
#    run "sudo chmod -R g+rwX #{shared_path}/app && sudo chgrp -R www-data #{shared_path}/app"
#    run "sudo chmod -R g+rwX #{shared_path}/cron && sudo chgrp -R www-data #{shared_path}/cron"
#    run "sudo chmod -R g+rwX #{shared_path}/cached-copy && sudo chgrp -R www-data #{shared_path}/cached-copy"
#    run "sudo chmod -R g+rwX #{shared_path}/logs && sudo chgrp -R www-data #{shared_path}/logs"
#    run "sudo chmod -R g+rwX #{shared_path}/vendor && sudo chgrp -R www-data #{shared_path}/vendor"
#  end
#  desc "Send email after deploy"
#  task :send_email do
#    run_locally "echo 'New deploy of hanzo branch: #{branch}. New current release: #{current_release}. Run from: #{hostname}:#{pwd}. By user: #{whoami} (#{hosts})' | mail -s 'Hanzo #{branch} deployed' -c hd@pompdelux.dk -c lv@pompdelux.dk -c un@bellcom.dk mmh@bellcom.dk"
#  end
#  desc "Send email after rollback"
#  task :send_email_rollback do
#    run_locally "echo 'Rollback of hanzo branch: #{branch}. New current release: #{current_release}. Run from: #{hostname}:#{pwd}. By user: #{whoami} (#{hosts})' | mail -s 'Hanzo #{branch} rolled back' -c hd@pompdelux.dk -c lv@pompdelux.dk -c un@bellcom.dk mmh@bellcom.dk"
#  end
#  desc "Rollback warning"
#  task :rollback_warning do
#    puts "REMEMBER TO CLEAR THE CACHE AFTER A ROLLBACK! RUN:";puts "cap #{branch} symfony:cache:clear"
#  end
## create symlinks 
#  desc "Create logs and public_html symlinks"
#  task :symlinks do
#    run("cd #{deploy_to}/current;if [ ! -L logs ];then ln -s app/logs logs;fi;if [ ! -L public_html ];then ln -s web public_html;fi")
#  end
#  desc "Send deploy to New Relic"
#  task :newrelic_notify do
#    run_locally("curl -s -H 'x-api-key:75916fb33fa70e01ddca4bd9a761c56989504595a94d03a' -d 'deployment[application_id]=697785' -d 'deployment[host]=#{hostname}' -d 'deployment[user]=#{whoami}' https://rpm.newrelic.com/deployments.xml")
#  end
#end
#
#
## own task. Clear the redis cache
#namespace :symfony do
#  namespace :cache do
#    desc "Clear/Flush redis cache"
#    task :redis_clear, :roles => :redis do
#      symfony_env_prods.each do |i|
#        run("cd #{latest_release} && php app/console hanzo:redis:cache:clear --env=#{i}")
#      end
#    end
#  end
#  namespace :cache do
#    desc "Update assets version"
#    task :assets_update do
#      symfony_env_prods.each do |i|
#        run("cd #{latest_release} && php app/console hanzo:dataio:update assets_version --env=#{i}")
#      end
#    end
#  end
#end
#
## own task. Clear Varnish
#namespace :symfony do
#  namespace :cache do
#    desc "Empty the varnish cache"
#    task :varnish_clear do
#      run("cd #{latest_release} && php app/console hanzo:varnish:purge --env=#{symfony_env_prods[0]}")
#    end
#  end
#end
#
## own task. Run propel migrations
#namespace :propel do
#  namespace :migration do
#    desc "Run migrations"
#    task :migrate, :roles => :db do
#    #  symfony_env_prods.each do |i| 
#        #run("cd #{latest_release} && php app/console propel:migration:migrate --env=#{i}")
#        run("cd #{latest_release} && php app/console propel:migration:migrate --env=prod_dk")
#    #  end
#    end
#    desc "Migrations status"
#    task :status, :roles => :db do
##      symfony_env_prods.each do |i| 
#        #run("cd #{latest_release} && php app/console propel:migration:status --env=#{i}")
#        run("cd #{latest_release} && php app/console propel:migration:status --env=prod_fi")
##      end
#    end
#  end
#end


# BELOW IS FROM THE OLD VERSION OF CAP/SYMFONY (2.0)

## below is from symfony2.rb. overrides that loops over synfony_env_prods
#namespace :symfony do
#  desc "Runs custom symfony task"
#  task :default do
#    symfony_env_prods.each do |i| 
#      prompt_with_default(:task_arguments, "cache:clear")
#      stream "cd #{latest_release} && #{php_bin} #{symfony_console} #{task_arguments} --env=#{i}"
#    end
#  end
#
#  namespace :assets do
#    desc "Install bundle's assets"
#    task :install do
#      symfony_env_prods.each do |i| 
#        run "cd #{latest_release} && #{php_bin} #{symfony_console} assets:install #{web_path} --env=#{i}"
#      end
#    end
#  end
#
#  namespace :assetic do
#    desc "Dumps all assets to the filesystem"
#    task :dump, :roles => :static do
#      run "cd #{latest_release} && #{php_bin} #{symfony_console} assetic:dump #{web_path} --env=prod_dk --no-debug"
#    end
#  end
#
#  namespace :cache do
#    desc "Clears project cache."
#    task :clear do
#      symfony_env_prods.each do |i| 
#        run "cd #{latest_release} && #{php_bin} #{symfony_console} cache:clear --env=#{i}"
## mmh. This chmod fails because some cache dirs and files are owned by www-data. Ignore the errors and continue, because the www-data dirs already seems to have g+w. Original line commented out below.
#        #run "chmod -R g+w #{latest_release}/#{cache_path}"
#        run "sudo chmod -R g+rwX #{latest_release}/#{cache_path}"
#      end
#    end
#
#    desc "Warms up an empty cache."
#    task :warmup do
#      symfony_env_prods.each do |i| 
#        run "cd #{latest_release} && #{php_bin} #{symfony_console} cache:warmup --env=#{i}"
#        run "chmod -R g+w #{latest_release}/#{cache_path}"
#      end
#    end
#  end
#
#  namespace :doctrine do
#    namespace :cache do
#      desc "Clear all metadata cache for a entity manager."
#      task :clear_metadata do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:cache:clear-metadata --env=#{i}"
#        end
#      end
#
#      desc "Clear all query cache for a entity manager."
#      task :clear_query do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:cache:clear-query --env=#{i}"
#        end
#      end
#
#      desc "Clear result cache for a entity manager."
#      task :clear_result do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:cache:clear-result --env=#{i}"
#        end
#      end
#    end
#
#    namespace :database do
#      desc "Create the configured databases."
#      task :create do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:database:create --env=#{i}"
#        end
#      end
#
#      desc "Drop the configured databases."
#      task :drop do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:database:drop --env=#{i}"
#        end
#      end
#    end
#
#    namespace :generate do
#      desc "Generates proxy classes for entity classes."
#      task :hydrators do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:generate:proxies --env=#{i}"
#        end
#      end
#
#      desc "Generate repository classes from your mapping information."
#      task :hydrators do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:generate:repositories --env=#{i}"
#        end
#      end
#    end
#
#    namespace :schema do
#      desc "Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output."
#      task :create do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:schema:create --env=#{i}"
#        end
#      end
#
#      desc "Drop the complete database schema of EntityManager Storage Connection or generate the corresponding SQL output."
#      task :drop do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:schema:drop --env=#{i}"
#        end
#      end
#    end
#
#    namespace :migrations do
#      desc "Execute a migration to a specified version or the latest available version."
#      task :migrate do
#        symfony_env_prods.each do |i| 
#          if Capistrano::CLI.ui.agree("Do you really want to migrate #{i}'s database? (y/N)")
#            run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:migrations:migrate --env=#{i} --no-interaction"
#          end
#        end
#      end
#
#      desc "View the status of a set of migrations."
#      task :status do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:migrations:status --env=#{i}"
#        end
#      end
#    end
#  end
#
#  namespace :init do
#    desc "Mounts ACL tables in the database"
#    task :acl do
#      symfony_env_prods.each do |i| 
#        run "cd #{latest_release} && #{php_bin} #{symfony_console} init:acl --env=#{i}"
#      end
#    end
#  end
#
#  namespace :propel do
#    namespace :database do
#      desc "Create the configured databases."
#      task :create do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:database:create --env=#{i}"
#        end
#      end
#
#      desc "Drop the configured databases."
#      task :drop do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:database:drop --env=#{i}"
#        end
#      end
#    end
#
#    namespace :build do
##      desc "Build the Model classes."
##      task :model do
##        symfony_env_prods.each do |i| 
##          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:build-model --env=#{i}"
##        end
##      end
#
## from newer version 
#      desc "Builds the Model classes"
#      task :model, :roles => :app, :except => { :no_release => true } do
#        command = "propel:model:build"
#        if /2\.0\.[0-9]+.*/ =~ symfony_version
#          command = "propel:build-model"
#        end
#
#        capifony_pretty_print "--> Generating Propel classes"
#
#        symfony_env_prods.each do |i| 
#          run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} #{command} --env=#{i}'"
#        end
#        capifony_puts_ok
#      end
#
#      desc "Build SQL statements."
#      task :sql do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:build-sql --env=#{i}"
#        end
#      end
#
#      desc "Build the Model classes, SQL statements and insert SQL."
#      task :all_and_load do
#        symfony_env_prods.each do |i| 
#          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:build --insert-sql --env=#{i}"
#        end
#      end
#    end
#  end
#end
