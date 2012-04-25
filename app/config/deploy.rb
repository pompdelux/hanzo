set :application, "Hanzo"
set :domain,      "debian1"
set :deploy_to,   "/var/www/hanzo"
set :app_path,    "app"

set :repository,  "git@github.com:bellcom/hanzo.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, `subversion` or `none`

# set :deploy_via,  :rsync_with_remote_cache

set :model_manager, "propel"
# Or: `propel`

role :web,        "debian1", "debian2" # Your HTTP server, Apache/etc
role :app,        "debian1", "debian2" # This may be the same as your `Web` server
# hf: æh, pas:
role :db,         domain, :primary => true       # This is where Rails migrations will run

set  :keep_releases,  3

set :shared_files,      ["app/config/parameters.ini"]

set :shared_children,     [app_path + "/logs", web_path + "/uploads", "vendor"]

#set :update_vendors, false
set :update_vendors, true 

set :git_enable_submodules, 1

set :use_sudo, false

ssh_options[:forward_agent] = true

# Links:
# http://capifony.org/
# http://stackoverflow.com/questions/8718259/capifony-and-directory-owners
# http://blog.servergrove.com/2011/09/07/deploying-symfony2-projects-on-shared-hosting-with-capifony/
# http://stackoverflow.com/questions/2633758/deploying-a-rails-app-to-multiple-servers-using-capistrano-best-practices
# >>> http://www.zalas.eu/multistage-deployment-of-symfony-applications-with-capifony
