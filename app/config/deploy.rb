# Links:
# http://capifony.org/
# http://stackoverflow.com/questions/8718259/capifony-and-directory-owners
# http://blog.servergrove.com/2011/09/07/deploying-symfony2-projects-on-shared-hosting-with-capifony/
# http://stackoverflow.com/questions/2633758/deploying-a-rails-app-to-multiple-servers-using-capistrano-best-practices
# http://www.zalas.eu/multistage-deployment-of-symfony-applications-with-capifony
# http://stackoverflow.com/questions/9454556/capifony-update-vendors-and-deps-file <- only update vendors on changes in deps
#
set :application, "Hanzo"
#set :domain,      "pompdelux-test" # hf@bellcom.dk: _Skal_ være en af dem som er defineret i rollerne
#set :deploy_to,   "/var/www/hanzo_deploy"
set :app_path,    "app"

set :repository,  "git@github.com:bellcom/hanzo.git"
set :scm,         :git

set :stage_dir, 'app/config/deploy' # needed for Symfony2 only
require 'capistrano/ext/multistage'
set :stages,        %w(testing production)
set :default_stage, "testing"

# mmh@bellcom.dk: use below to rsync the files instead of git clone. Requires capistrano_rsync_with_remote_cache installed (gem install)
set :deploy_via,  :rsync_with_remote_cache

set :model_manager, "propel"

## Your HTTP server, Apache/etc
#role(:web) do
#   frontend_list
#end
## This may be the same as your `Web` server
#role(:app) do
#   frontend_list
#end

# hf@bellcom.dk: æh, pas:
#role :db, domain, :primary => true  # This is where Rails migrations will run

set :keep_releases,  3

set :shared_files,      ["app/config/parameters.ini"]

set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]

# hf@bellcom.dk: i en deploy skal den være true, ellers false, måske skal vi have den i en task
#set :update_vendors, false
set :update_vendors, true 

set :git_enable_submodules, 1

set :use_sudo, false

ssh_options[:forward_agent] = true

#
# hf@bellcom.dk, run route builder before cache warm
#
before "symfony:cache:warmup", "route_builder"

desc "Build hanzo routes"
task :route_builder do
  run("cd #{latest_release} && php app/console hanzo:router:builder")
end


# 
# hf@bellcom.dk, read server list from file
# 
#def frontend_list
#  contentsArray = Array.new
#  contentsArray = File.readlines("tools/deploy/frontend_list.txt")
#end

#
# hf@bellcom.dk, update translations and clear cache
#
namespace :deploy do
  desc "Update translations"
  task :translations do
    run "#{current_path}/tools/deploy/translations.sh #{current_path}/app/Resources/ "
  end

 desc "Create logs and public_html symlinks"
  task :symlinks do
   run("cd #{deploy_to}/current;if [ ! -L logs ];then ln -s app/logs logs;fi;if [ ! -L public_html ];then ln -s web public_html;fi")
  end

# Other examples:
#  desc "Symlink shared configs and folders on each release."
#  task :symlink_shared do
#    run "ln -nfs #{shared_path}/config/database.yml #{release_path}/config/database.yml"
#    run "ln -nfs #{shared_path}/assets #{release_path}/public/assets"
#  end
#  
#  desc "Sync the public/assets directory."
#  task :assets do
#    system "rsync -vr --exclude='.DS_Store' public/assets #{user}@#{application}:#{shared_path}/"
#  end
end

before 'deploy:restart', 'deploy:symlinks'
