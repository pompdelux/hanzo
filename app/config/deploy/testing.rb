#
# Hanzo / Pompdelux testing deploy.
#

set :deploy_to,   "/var/www/testpompdelux" 
set :domain,      "pomp-test"
#set :dbserver,    "pomptest-db"

symfony_env_prods = ["prod_dk"]
#symfony_env_prods = ["prod_dk", "prod_se"]
role :web,        domain
role :app,        domain

set :adminserver, "pomp-test"

# if we ever get other brances, specify which one to deploy here
#set   :branch, "master"

#set :update_vendors, true
set :update_vendors, false

# our redis server. clear cache here
role :redis, domain, :primary => true

# where to run migrations. :db is also a default and may be used internally.
#role :db, dbserver, :primary => true  # where to run migrations

before 'deploy:restart', 'deploy:apcclear'

before 'symfony:cache:warmup', 'symfony:cache:redis_clear'

# also clear redis when calling cache:clear
after 'symfony:cache:clear', 'symfony:cache:redis_clear'

namespace :deploy do
  desc "Roll out apc-clear.php"
  task :copy_apcclear do
    run("wget -q --output-document=/var/www/apc-clear.php http://tools.bellcom.dk/hanzo/apc-clear.php.txt")
  end
  desc "Clear apc cache on the local server"
  task :apcclear do
    run("wget -q -O /dev/null http://localhost/apc-clear.php")
  end
end

# own task. Clear the redis cache
namespace :symfony do
  namespace :cache do
    desc "Clear/Flush redis cache"
    task :redis_clear, :roles => :redis do
      symfony_env_prods.each do |i| 
        run("cd #{latest_release} && php app/console hanzo:redis:cache:clear --env=#{i}")
      end
    end
  end
end


# below is from symfony2.rb. overrides that loops over synfony_env_prods
namespace :symfony do
  desc "Runs custom symfony task"
  task :default do
    symfony_env_prods.each do |i| 
      prompt_with_default(:task_arguments, "cache:clear")
      stream "cd #{latest_release} && #{php_bin} #{symfony_console} #{task_arguments} --env=#{i}"
    end
  end

  namespace :assets do
    desc "Install bundle's assets"
    task :install do
      symfony_env_prods.each do |i| 
        run "cd #{latest_release} && #{php_bin} #{symfony_console} assets:install #{web_path} --env=#{i}"
      end
    end
  end

  namespace :assetic do
    desc "Dumps all assets to the filesystem"
    task :dump do
      symfony_env_prods.each do |i| 
        run "cd #{latest_release} && #{php_bin} #{symfony_console} assetic:dump #{web_path} --env=#{i} --no-debug"
      end
    end
  end

  namespace :cache do
    desc "Clears project cache."
    task :clear do
      symfony_env_prods.each do |i| 
        run "cd #{latest_release} && #{php_bin} #{symfony_console} cache:clear --env=#{i}"
        run "chmod -R g+w #{latest_release}/#{cache_path}"
      end
    end

    desc "Warms up an empty cache."
    task :warmup do
      symfony_env_prods.each do |i| 
        run "cd #{latest_release} && #{php_bin} #{symfony_console} cache:warmup --env=#{i}"
        run "chmod -R g+w #{latest_release}/#{cache_path}"
      end
    end
  end

  namespace :doctrine do
    namespace :cache do
      desc "Clear all metadata cache for a entity manager."
      task :clear_metadata do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:cache:clear-metadata --env=#{i}"
        end
      end

      desc "Clear all query cache for a entity manager."
      task :clear_query do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:cache:clear-query --env=#{i}"
        end
      end

      desc "Clear result cache for a entity manager."
      task :clear_result do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:cache:clear-result --env=#{i}"
        end
      end
    end

    namespace :database do
      desc "Create the configured databases."
      task :create do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:database:create --env=#{i}"
        end
      end

      desc "Drop the configured databases."
      task :drop do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:database:drop --env=#{i}"
        end
      end
    end

    namespace :generate do
      desc "Generates proxy classes for entity classes."
      task :hydrators do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:generate:proxies --env=#{i}"
        end
      end

      desc "Generate repository classes from your mapping information."
      task :hydrators do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:generate:repositories --env=#{i}"
        end
      end
    end

    namespace :schema do
      desc "Processes the schema and either create it directly on EntityManager Storage Connection or generate the SQL output."
      task :create do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:schema:create --env=#{i}"
        end
      end

      desc "Drop the complete database schema of EntityManager Storage Connection or generate the corresponding SQL output."
      task :drop do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:schema:drop --env=#{i}"
        end
      end
    end

    namespace :migrations do
      desc "Execute a migration to a specified version or the latest available version."
      task :migrate do
        symfony_env_prods.each do |i| 
          if Capistrano::CLI.ui.agree("Do you really want to migrate #{i}'s database? (y/N)")
            run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:migrations:migrate --env=#{i} --no-interaction"
          end
        end
      end

      desc "View the status of a set of migrations."
      task :status do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} doctrine:migrations:status --env=#{i}"
        end
      end
    end
  end

  namespace :init do
    desc "Mounts ACL tables in the database"
    task :acl do
      symfony_env_prods.each do |i| 
        run "cd #{latest_release} && #{php_bin} #{symfony_console} init:acl --env=#{i}"
      end
    end
  end

  namespace :propel do
    namespace :database do
      desc "Create the configured databases."
      task :create do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:database:create --env=#{i}"
        end
      end

      desc "Drop the configured databases."
      task :drop do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:database:drop --env=#{i}"
        end
      end
    end

    namespace :build do
      desc "Build the Model classes."
      task :model do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:build-model --env=#{i}"
        end
      end

      desc "Build SQL statements."
      task :sql do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:build-sql --env=#{i}"
        end
      end

      desc "Build the Model classes, SQL statements and insert SQL."
      task :all_and_load do
        symfony_env_prods.each do |i| 
          run "cd #{latest_release} && #{php_bin} #{symfony_console} propel:build --insert-sql --env=#{i}"
        end
      end
    end
  end
end


