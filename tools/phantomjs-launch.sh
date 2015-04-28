#!/bin/bash

if ! type phantomjs > /dev/null; then
    echo "Install from here: http://phantomjs.org/download.html or apt-get install phantomjs"
    exit 1
else
    phantomjs --webdriver=172.17.42.1:8643 --ignore-ssl-errors=true
fi