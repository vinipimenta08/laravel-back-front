## Installation

git Init repository

```bash

# clone the repo
$ git clone https://github.com/ABXtech/optin-frontend.git

# go into app's directory
$ cd optin-frontend

```

You can install the package:

```bash

# installs
$ composer install

$ npm install

```

Generate the application's encryption key using:

```bash

# Generate
$ php artisan key:generate

```


Need to upload the style sheet for the project:

```bash

# run development
$ npm run dev 

# run watch
$ npm run watch 

# run production
$ npm run production 


```

start application services pm2:

```bash

#install pm2
$ sudo npm i -g pm2

# start serve
$ pm2 start laravel-frontend-start.yml
