
# LoveLock
LoveLock is the readymade app by Dating App Script, a location-based,
STANDALONE  matchmaking app to start your dating app venture.
With the industry leading features and affordable pricing, LoveLock helps you take the dating app from your dreams to reality.
It is a complete Tinder-clone app; get the source code and start building or we have the best value for money customization packages for you.

## Installation

### Requirements
* Linux server
* Apache/2.4.34 (Ubuntu 16.04)
* mySQL 5.7.23
* PHP version: 7.2.8
* phpMyAdmin 4.8.2
* SSL Certificate installed

### Note:
**This is an advanced topic.  This is only recommended for those with advanced knowledge in Laravel and server  or those that have the assistance of a programmer.
### Procedure:
#### First Step:
* Extract the project to the path /var/www/html/lovelock.local. You can create your own folder name related to your app name.


* Copy all the content of the lovelock folder to lovelock.local
##### Note: Please change the name LoveLock to you app name. Suppose there is a folder called lovelock.local then change to yourapp.local.

#### Open the terminal
* Please open up the terminal and type the following commands.
```

$ cd /var/www/html/lovelock.local
$ mv lovelock/* .
$ mv lovelock/.* .
```

#### Create custom routes
* In the path "lovelock/routes" there is a file called customWeb.example.php. Copy the customWeb.example.php as customWeb.php file(This file will contain the custom routes that the customer want to put and the custom blade file will be in frontend/custom folder)
```
$ cp routes/customWeb.example.php routes/customWeb.php
$ composer install
```
* In the LoveLock root folder there is file called .env.example. Copy the .env.example as .env file

```
$ cp .env.example .env
```

#### Please open some editor like Sublime to make changes to the file
* Open the .env file in text editor. Change app name and url in the .env file.
```
// Please change the following
APP_NAME=LoveLock
APP_URL=https://lovelock.wpdating.com
```
* Now put the database details in the .env file. Make sure you have created database and database user.

```
DB_DATABASE=lovelock_database
DB_USERNAME=username
DB_PASSWORD=password
```

* Please open up the terminal and type the following
```
$ php artisan key:generate
$ touch storage/logs/laravel.log
$ chmod 777 storage/logs/laravel.log
$ chmod -R 777 storage
$ chmod -R 777 bootstrap/cache/
$ php artisan migrate
$ php artisan storage:link
```

* You need to generate client id and secret id. To generate the keys please open the terminal and type the following.
```
// This will generate client id and secret id
// Please keep those client id and secret id safe.
// You will be needing those to connect apps later

$ php artisan passport:install
```

#### Subscription seeding:
* First fill the details in the .env file of subscription.
This is the subscription package which will be used in app.
Open the .env file in the editor and fill the following.
```

// This is for the free plan.
PLAN_SUBSCRIPTION_NAME0=
PLAN_SUBSCRIPTION_PLAN_CODE0=
PLAN_SUBSCRIPTION_DESCRIPTION0=

// You do not need to enter price for free plan.
PLAN_SUBSCRIPTION_PRICE0=
PLAN_SUBSCRIPTION_INTERVAL0=
PLAN_SUBSCRIPTION_INTERVAL_COUNT0=
PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID0=
PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID0=

PLAN_SUBSCRIPTION_NAME1=
PLAN_SUBSCRIPTION_PLAN_CODE1=
PLAN_SUBSCRIPTION_DESCRIPTION1=
PLAN_SUBSCRIPTION_PRICE1=
PLAN_SUBSCRIPTION_INTERVAL1=
PLAN_SUBSCRIPTION_INTERVAL_COUNT1=
PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID1=
PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID1=

PLAN_SUBSCRIPTION_NAME2=
PLAN_SUBSCRIPTION_PLAN_CODE2=
PLAN_SUBSCRIPTION_DESCRIPTION2=
PLAN_SUBSCRIPTION_PRICE2=
PLAN_SUBSCRIPTION_INTERVAL2=
PLAN_SUBSCRIPTION_INTERVAL_COUNT2=
PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID2=
PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID2=


PLAN_SUBSCRIPTION_NAME3=
PLAN_SUBSCRIPTION_PLAN_CODE3=
PLAN_SUBSCRIPTION_DESCRIPTION3=
PLAN_SUBSCRIPTION_PRICE3=
PLAN_SUBSCRIPTION_INTERVAL3=
PLAN_SUBSCRIPTION_INTERVAL_COUNT3=
PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID3=
PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID3=

PLAN_SUBSCRIPTION_FREE_LIKES_COUNT=
```
* Then open the terminal and run the following code:
```
$ php artisan db:seed --class=PlansTableSeeder
$ php artisan db:seed --class=FeatureCustomLocationTableSeeder
$ php artisan db:seed --class=FeatureSuperLikeTableSeeder
$ php artisan db:seed --class=FeatureBoostProfileTableSeeder
$ php artisan db:seed --class=FeatureAdTableSeeder

```
#### Generate two Demo Users
* Two demo users will be generated with following details:

#####User 1:
 * Phone: +1123456789
* Password: Password1

#####User 2: 
* Phone: +1987654321
* Password: Password2

```
$ php artisan db:seed --class=DemoUsersTableSeeder
```

#### Install supervisor:
This process is used for queuing SMS while sending the verification code. Open up the terminal and enter the following command.
```
$ sudo apt-get install supervisor
```
#### Configuring Supervisor:
* Supervisor configuration files are typically stored in the /etc/supervisor/conf.d directory. Within this directory, you may create any number of configuration files that instruct supervisor how your processes should be monitored.
* For example, let's create a laravel-worker.conf file that starts and monitors a queue:work process:

```
$ cd /etc/supervisor/conf.d
$ nano laravel-worker.conf
```
* [put the following in the file laravel-worker.conf] and save the file.

|Configuring Supervisor|
|---|
|[program:laravel-worker]|
|process_name=%(program_name)s_%(process_num)02d|
|command=php /home/lovelock_user/public_html/lovelock/artisan queue:work --sleep=3 --tries=3|
|autostart=true|
|autorestart=true|
|user=lovelock_user|
|numprocs=8|
|redirect_stderr=true|
|stdout_logfile=/home/lovelock_user/public_html/worker.log|

#####Note: The path may vary in above code. It depends where you have placed the files. So, please change the path before saving.
#### Starting the supervisor:

```
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```
#### To setup twilio variables in .env:
* Signup in twilio for sending SMS (which is used for phone number verification).
[twilio.com](https://www.twilio.com/)

* Authenticate the number inorder to get the API auth token.

* Then use these token to put in the .env file of the lovelock.

* For twilio account sid : goto dashboard
For auth token : [https://www.twilio.com/console/project/settings](https://www.twilio.com/console/project/settings)
* For twilio phone no : [https://www.twilio.com/console/phone-numbers/getting-started](https://www.twilio.com/console/phone-numbers/getting-started)
* Open the .env file in the editor and put that number in twilio phone number.

```
TWILIO_ACCOUNT_SID=example
TWILIO_AUTH_TOKEN=example
TWILIO_PHONE_NUMBER=example
```
#### To setup mail variables in .env:
* Now to setup the SMTP data to send the emails.
* Fill the following in the .env file.
```
MAIL_DRIVER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=
```
#### To setup facebook variables in .env (optional):
Only follow the below steps if you want to use facebook login on your app.
* Setup facebook setting values for facebook login.
* Go to [https://developers.facebook.com/](https://developers.facebook.com/)
* Login to it.
* Go to my app > add new app.
* Create new app ID.
* Goto dashboard.
* Setup the facebook login : [https://developers.facebook.com/apps/530503194037503/fb-login/quickstart/](https://developers.facebook.com/apps/530503194037503/fb-login/quickstart/)
```
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
FACEBOOK_REDIRECT=
FACEBOOK_DEFAULT_GRAPH_VERSION=v2.12
```
##Additional Configuration

#### Placing Assets
* Put the assets like css, js, font in the path storage/app/public/

#### Turn sending sms on or off
* For setting sms to send sms when registration is completed and when phone number is verified, set these variable. Setting '1' will send the sms when the user is registered and phone is verified. Default is '0'.
```
SMS_SEND_CODE_FOR_REGISTRATION_SUCCESS=
SMS_SEND_CODE_FOR_PHONE_VERIFIED=
```
* For demo purpose if you want to bypass the location check for getting list of the swipe users then set the following env variables(0 for returning users within certain distance and 1 for returning  all users)
```
DEMO_TURN_OFF_LOCATION=
```

#### For Generating fake users
* If you want to generate fake users you can use this process.
##### You can skip the following step.
* There is file called  countrycode.json as countrycode_copy.json and location.json as location_copy.json file in path 'lovelock/storage'. You can make a copy of those two files and change the values inside.
* countrycode.json contains the list of country code and location.json contains fake locations.
* Here you can edit those copied files. You can add or remove the country codes and also edit longitude and latitude. 
* Then fill the following fields in the .env file

```
STORAGE_LOCATION_JSON_FILE_NAME=location_copy.json
STORAGE_COUNTRY_CODE_JSON_FILE_NAME=countrycode_copy.json
```
##### Now generate fake profiles
* Please run the following code: This will generate 10 fake users per command.
```
$ php artisan db:seed
```

# For the API documentation
Please follow this link for the documentation : [click here](https://lovelockdocs.wpdating.com)

#Upgrade Notes
#### Upgrade to 5.0.2.0
```
$ php artisan db:seed --class=FeatureCustomLocationTableSeeder
```

#### Upgrade to 6.0.2.4
```
$ composer dump-autoload
$ composer install
```