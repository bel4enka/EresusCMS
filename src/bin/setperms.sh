#!/bin/sh
##
# Set required file permissions
#
# @author Михаил Красильников <mihalych@vsepofigu.ru>

home=`dirname $0`

function doChmod
{
  if ! chmod $1 $2
  then
    echo "Can't chmod $2!"
    exit 1
  fi
}

function makeWritable
{
  if [ -f $1 ]
  then
    doChmod 'a+rw' $1
  else
    if [ -d $1 ]
    then
      doChmod 'a+rw -R' $1
    else
      echo "ERROR: File $1 not exist!"
      exit 2
    fi
  fi
}

makeWritable "$home/../cfg/settings.php"
makeWritable "$home/../data"
makeWritable "$home/../style"
makeWritable "$home/../templates"
makeWritable "$home/../var"

echo "All done!"
