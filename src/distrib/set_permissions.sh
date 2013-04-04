#!/bin/sh

cd `dirname $0`/..

chmod -R a+rwX cache
chmod a+rw config/global.yml
chmod a+rw config/plugins.yml
chmod -R a+rwX logs
chmod -R a+rwX public