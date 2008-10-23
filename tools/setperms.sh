#!/bin/sh
#
# This script sets file permissions
#
# @author Mikhail Krasilnikov <mk@procreat.ru>
#
# $Id$
#

home=$0
if [ $home = "." ]; then
	home=`pwd`
fi
home=`realpath $home/../../..`

chmod a+rw "$home/cfg/settings.php"
chmod -R a+rw "$home/data"
chmod -R a+rw "$home/style"
chmod -R a+rw "$home/templates"
