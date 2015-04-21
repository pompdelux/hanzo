#!/bin/bash

IMAGE='bellcom/pompdelux-web:wheezy'
NAME="pompdelux-web"

echo "Stopping existing containers"
RUNNING_CONTAINERS=$(docker ps -q)

# if [[ command -v mailcatcher >/dev/null 2>&1 ]]; then
#   MAILCATCHER_IS_RUNNING=`netstat -tna | grep 1025 | wc -l`
#   if [[ $MAILCATCHER_IS_RUNNING == 0 ]]; then
#     mailcatcher --smtp-ip 172.17.42.1
#   fi
# fi

if [[ -n $RUNNING_CONTAINERS ]]; then
  docker stop $RUNNING_CONTAINERS
fi

# Not the prettiest way, but the output changes to much to cut -c is usefull, and using word delimiters in grep also fails if the name is used in the image name
CONTAINER_EXIST=`docker ps -a | grep -c " $NAME "`
if [[ $CONTAINER_EXIST > 0 ]]; then
  echo "Restarting docker container $NAME"
  docker restart pompdelux-db
  docker restart $NAME
else
  # -d detach
  echo "Starting new docker with name $NAME"
  docker run -d -h pompdelux-db -v $(pwd)/mysql/wheezy:/var/lib/mysql --name pompdelux-db bellcom/pompdelux-db:wheezy
  docker run -d -h pompdelux-web --link pompdelux-db:pompdelux-db -i -t -p 80:80 -p 443:443 -p 9000:9000 -v $(pwd)/../../:/var/www/pompdelux -v $(pwd)/nginx:/etc/nginx/sites-enabled/ --name $NAME $IMAGE
fi
