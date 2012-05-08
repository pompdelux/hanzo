#!/bin/bash

cd $1

BRANCH=`git branch`

if [[ "${BRANCH}" == "* master"  ]]; then
  OUT=`git pull`
  if [[ $? != 0 ]]; then
    echo "------------- [ERROR] -------------"
    echo "git exited with non zero status"
    echo ${OUT}
  fi
else
  echo "------------- [ERROR] -------------"
  echo "Master branch is not selected"
  echo "${BRANCH}"
  exit 1;
fi
