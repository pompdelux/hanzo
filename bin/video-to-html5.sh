#!/bin/bash

INDIR='/var/www/video/in'
ORIGDIR='/var/www/video/'
OGVDIR='/var/www/video/'

cd $INDIR
for i in `ls *.mp4 2>/dev/null`;do
  BASENAME=`basename $i .mp4`
  ffmpeg2theora $i -o $OGVDIR/$BASENAME.ogv &>/dev/null
  mv -v  $i $ORIGDIR
done

chown -R heinrich:www-data $ORIGDIR
chmod -R g+rwX $ORIGDIR
