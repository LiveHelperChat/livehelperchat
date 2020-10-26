#!/bin/sh
# This script is run before pushing to github. It does all the javascript cleanups works.

if [ ! -f "index.php" -a \
     ! -d "design" -a \
     ! -d "lib" -a \
     ! -d "pos" -a \
     ! -d "modules" ] ; then
     echo "You seem to be in the wrong directory"
     echo "Place yourself in the LHC root directory and run ./deploys.sh"
     exit 1
fi

echo "Removing lazy load core js files"
rm -rf ./design/defaulttheme/js/lh/dist/*.js

echo "Compiling default js"
gulp

echo "Cleaning up react-app"
rm -rf ./design/defaulttheme/widget/react-app/dist/*.js

echo "Cleaning up wrapper app"
rm -rf ./design/defaulttheme/widget/wrapper/dist/*.js

echo "Cleaning up widget js files"
rm -rf ./design/defaulttheme/js/widgetv2/*.js

echo "Compiling react-js"
cd ./design/defaulttheme/widget/react-app && npm run build && npm run build-ie
cd ../../../../

echo "Compiling wrapper"
cd ./design/defaulttheme/widget/wrapper && npm run build
cd ../../../../

echo "Generating JS/CSS files"
php cron.php -s site_admin -c cron/util/generate_css -p 1

echo "Compressing JS"
gulp js-static