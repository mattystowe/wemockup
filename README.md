# WeMockup MVP Prototype and Deploy Tests




## Getting Started

1.Download repo

2.npm install && bower install

3.php artisan migrate  (db migrations)

4.Seed - Demo orders .  Run php artisan db:seed --class=DemoOrders



## Environment settings
NOTE: .env file needs to be configured to your local settings.  Defaults should be fine.



Next, you'll want to pull in Gulp as a global NPM package:

npm install --global gulp-cli



4.gulp to Build or gulp watch to watch


5.php artisan serve (serves to http://localhost:8000/)

## Default user login to admin panel

http://localhost:8000/login
email: admin@wemockup.com
password: skymonkey


## Setup

phpenv local 7.0
#composer install --prefer-source --no-interaction
nvm install 8.5.0
npm install -g bower
npm install -g gulp
npm install
bower install
gulp --production


## Testing
./test.sh



## Queues



php artisan queue:listen --queue wemockup-dev-emails
php artisan queue:listen --queue wemockup-dev-itemprocessing
php artisan queue:listen --queue wemockup-dev-itemjobs
php artisan queue:listen --queue wemockup-dev-postprocesses

or run all queues together in the order required -
php artisan queue:listen --queue wemockup-dev-emails,wemockup-dev-itemjobs --timeout=0 --tries=2


##ftp file transfers -Cyberduck CLI for mac and windows
https://trac.cyberduck.io/wiki/help/en/howto/cli#Installation
Upload example
duck --username [username] --password [password] --quiet --assumeyes --nokeychain  --upload ftp.renderfarm/[pathremote]/ [localpath]

Download example
duck --username yourusername --password yourpassword --quiet --assumeyes --nokeychain  --download ftp.renderfarm/output/369240/ /Users/youruser/Desktop/localtest/
