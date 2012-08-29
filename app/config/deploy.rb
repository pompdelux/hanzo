
set :application, "Hanzo"
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
#set :rsync_options, "-rltoDzO --delete"
set :rsync_options, "-rltzO --delete"

#set :copy_exclude, [".git", "spec"]

#set :deployed_user, "www-data"
set :deployed_group, "www-data"

set :model_manager, "propel"

set :keep_releases,  5

set :shared_files,      ["app/config/parameters.ini", "app/config/hanzo.yml", "cron/config.php"]

set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor", web_path + "/images", web_path + "/video", web_path + "/pdfupload"]

# see https://github.com/morhekil/capistrano-deepmodules
require 'capistrano/deepmodules'
set :git_enable_submodules, 1

#set :use_sudo, false
set :use_sudo, true

ssh_options[:forward_agent] = true

# hf@bellcom.dk, update translations and clear cache
namespace :deploy do
  desc "Update translations"
  task :translations do
    run "#{current_path}/tools/deploy/translations.sh #{current_path}/app/Resources/ "
  end
# create symlinks 
  desc "Create logs and public_html symlinks"
  task :symlinks do
    run("cd #{deploy_to}/current;if [ ! -L logs ];then ln -s app/logs logs;fi;if [ ! -L public_html ];then ln -s web public_html;fi")
  end
end

before 'deploy:restart', 'deploy:symlinks'

