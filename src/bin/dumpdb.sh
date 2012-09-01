#!/bin/sh
##
# Dumps MySQL database
#
# @author Михаил Красильников <mihalych@vsepofigu.ru>
#

home=`dirname $0`
CONFIG="$home/../cfg/main.php"

#
# Check dependencies
#
`which expr &>/dev/null`
if [ $? -ne 0 ]
then
	echo "Can't find 'expr' binary.\n"
	exit 1
fi

`which grep &>/dev/null`
if [ $? -ne 0 ]
then
	echo "Can't find 'grep' binary.\n"
	exit 1
fi

`which mysqldump &>/dev/null`
if [ $? -ne 0 ]
then
	echo "Can't find 'mysqldump' binary.\n"
	exit 1
fi


STRING=`cat $CONFIG | grep conf.*\'user\'`
USER=`expr match "$STRING" ".*'\(.*\)';"`

STRING=`cat $CONFIG | grep conf.*\'password\'`
PASSWORD=`expr match "$STRING" ".*'\(.*\)';"`

STRING=`cat $CONFIG | grep conf.*\'name\'`
DATABASE=`expr match "$STRING" ".*'\(.*\)';"`

OPTIONS="--user=$USER --password=$PASSWORD"
OPTIONS="$OPTIONS --skip-opt"
OPTIONS="$OPTIONS --quick"
OPTIONS="$OPTIONS --force"
OPTIONS="$OPTIONS --create-options"
OPTIONS="$OPTIONS --add-drop-table"
OPTIONS="$OPTIONS --skip-add-locks"
OPTIONS="$OPTIONS --skip-disable-keys"
OPTIONS="$OPTIONS --skip-extended-insert"
OPTIONS="$OPTIONS --skip-lock-tables"
OPTIONS="$OPTIONS --skip-set-charset"
OPTIONS="$OPTIONS --skip-tz-utc"

mysqldump $OPTIONS $DATABASE --result-file=$DATABASE.sql
