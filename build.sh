#!/bin/bash

#
# Dispaly error message
#
error()
{
	echo "ERROR: $1"
	exit 1
}

#
# Searching for PHP CLI
#
PHP=`which php`
if [[ ! -f $PHP ]]; then	error "PHP CLI not found!"; fi

#
# Searching for Murash
#
DIR=`dirname $0`
if [[ $DIR = "." ]]; then DIR=`pwd`; fi
MURASH="$DIR/murash.php"

if [[ ! -f $MURASH ]]; then	error "Murash main module not found!"; fi

$PHP $MURASH
