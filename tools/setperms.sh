#!/bin/sh
#
# Set required file permissions
#
# @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
#
# $Id$
#

home=`dirname $0`
if [ $home = "." ]; then
	home=`pwd`
fi
home="$home/../.."

chmod -R a+rw "$home/data"
chmod -R a+rw "$home/style"
chmod -R a+rw "$home/templates"
chmod -R a+rw "$home/var"
