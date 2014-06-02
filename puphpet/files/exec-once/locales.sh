#!/bin/bash

echo 'Installing additional locales'
echo "" >>  /etc/locale.gen
echo "da_DK.UTF-8 UTF-8" >>  /etc/locale.gen
echo "de_AT.UTF-8 UTF-8" >>  /etc/locale.gen
echo "de_CH.UTF-8 UTF-8" >>  /etc/locale.gen
echo "de_DE.UTF-8 UTF-8" >>  /etc/locale.gen
echo "en_GB.UTF-8 UTF-8" >>  /etc/locale.gen
echo "fi_FI.UTF-8 UTF-8" >>  /etc/locale.gen
echo "nb_NO.UTF-8 UTF-8" >>  /etc/locale.gen
echo "nl_NL.UTF-8 UTF-8" >>  /etc/locale.gen
echo "sv_SE.UTF-8 UTF-8" >>  /etc/locale.gen
echo "sv_FI.UTF-8 UTF-8" >>  /etc/locale.gen

sed -i -e 's/# da_DK.UTF-8/## da_DK.UTF-8/' /etc/locale.gen

locale-gen
