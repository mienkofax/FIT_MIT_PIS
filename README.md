# PIS

## Web server setup

1. clone git

2. make directories `tmp/` and `log/` writable.
```bash
sudo chmod -R a+rw tmp log
```

3. download libraries
```bash
composer install
```

4. start web server
```bash
php -S localhost:8000 -t www
```
and visit `http://localhost:8000` in your browser

## Links
* [Nette dokumentácia](https://doc.nette.org/cs/2.4/)
* [Jak začít a propojit Doctrine a Nette Framework](http://blog.honzacerny.com/post/3-jak-zacit-a-propojit-doctrine-a-nette-framework)

## Ubuntu dependencies

### MySQL + PHP 7.0 + Apache + phpMyAdmin

```bash
sudo apt-get install apache2 -y
sudo apt-get install mysql-server mysql-client -y
sudo apt-get install php7.0-mysql php7.0-curl php7.0-json php7.0-cgi libapache2-mod-php7.0 php7.0 -y
sudo apt-get install phpmyadmin php-mbstring php-gettext -y
sudo chgrp www-data /var/www
sudo chmod -R 777 /var/www
sudo chmod -R g+s /var/www
sudo usermod -a -G www-data $USER
```

### Composer
```
sudo apt-get install composer
```

#Other

## Create nette project (unnecessary)
composer create-project nette/sandbox pharmacy
cd pharmacy
composer require kdyby/doctrine
