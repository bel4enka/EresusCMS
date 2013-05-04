#!/bin/sh

cd `dirname $0`/..

chmod -R a+rwX app/cache
chmod -R a+rwX app/logs
chmod a+rw config/global.yml
chmod a+rw config/plugins.yml
chmod -R a+rwX web/upload