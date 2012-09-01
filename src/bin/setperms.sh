#!/bin/sh
##
# Set required file permissions
#
# @author Михаил Красильников <mihalych@vsepofigu.ru>

home=`dirname $0`
chmod a+rw "$home/../cfg/settings.php"
chmod -R a+rw "$home/../data"
chmod -R a+rw "$home/../style"
chmod -R a+rw "$home/../templates"
chmod -R a+rw "$home/../var"
