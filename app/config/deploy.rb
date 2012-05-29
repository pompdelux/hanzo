# Links:
# http://capifony.org/
# http://stackoverflow.com/questions/8718259/capifony-and-directory-owners
# http://blog.servergrove.com/2011/09/07/deploying-symfony2-projects-on-shared-hosting-with-capifony/
# http://stackoverflow.com/questions/2633758/deploying-a-rails-app-to-multiple-servers-using-capistrano-best-practices
# http://www.zalas.eu/multistage-deployment-of-symfony-applications-with-capifony
# http://stackoverflow.com/questions/9454556/capifony-update-vendors-and-deps-file <- only update vendors on changes in deps
#

set :application, "Hanzo"
# below moved to specific files. production.rb and testing.rb
#set :domain,      "pompdelux-test"
#set :deploy_to,   "/var/www/hanzo_deploy"
set :app_path,    "app"

set :repository,  "git@github.com:bellcom/hanzo.git"
set :scm,         :git

set :stage_dir, 'app/config/deploy'
require 'capistrano/ext/multistage'
set :stages,        %w(testing production)
set :default_stage, "testing"

# mmh@bellcom.dk: use below to rsync the files instead of git clone. Requires capistrano_rsync_with_remote_cache installed (gem install)
set :deploy_via,  :rsync_with_remote_cache
# use other rsync_options. Default is: -az --delete
set :rsync_options, "-rlptoDzO --delete"

#set :copy_exclude, [".git", "spec"]

set :deployed_group, "www-data"

set :model_manager, "propel"

set :keep_releases,  5

set :shared_files,      ["app/config/parameters.ini", "app/config/hanzo.yml"]

set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", web_path + "/images"]

# hf@bellcom.dk: i en deploy skal den være true, ellers false, måske skal vi have den i en task
set :update_vendors, false
#set :update_vendors, true 

set :git_enable_submodules, 1

set :use_sudo, false

ssh_options[:forward_agent] = true

# hf@bellcom.dk, update translations and clear cache
namespace :deploy do
  desc "Update translations"
  task :translations do
    run "#{current_path}/tools/deploy/translations.sh #{current_path}/app/Resources/ "
  end

  desc "Update permissions on shared app logs and web dirs to be group writeable"
  task :update_permissions do
    run "cd #{shared_path} && sudo chmod g+rwX -R app/config && sudo chmod g+rwX app/logs web"
    run "cd #{shared_path} && sudo chgrp -R www-data cached-copy"
  end
 
desc "Create logs and public_html symlinks"
  task :symlinks do
   run("cd #{deploy_to}/current;if [ ! -L logs ];then ln -s app/logs logs;fi;if [ ! -L public_html ];then ln -s web public_html;fi")
  end

end

before 'deploy:restart', 'deploy:symlinks'

after 'deploy', 'deploy:update_permissions'

