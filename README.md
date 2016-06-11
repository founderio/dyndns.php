# php_dyndns

## Installing the dependencies

Install composer:
```
curl -s http://getcomposer.org/installer | php
```

Install dependencies:
```
php composer.phar install
```

Required PHP modules:
* simplexml

Can be installed by using e.g.:
```
sudo apt-get install php-simplexml
```
Don't forget to reload your server config afterwards. For apache, use:
```
sudo service apache2 reload
```