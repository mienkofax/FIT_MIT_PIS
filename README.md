# PIS project setup

## About project

 * [Lekáreň (34) - zadanie](https://wis.fit.vutbr.cz/FIT/st/cwk.php?title=AIS:Projects-topics&csid=648413&id=11321#L%C3%A9k%C3%A1rna)
 * [Zadanie projektu do predmetu PIS](https://www.fit.vutbr.cz/study/courses/PIS/private/cviceni/projekt.html)

#### 1. Clone git

```
git clone git@github.com:mienkofax/PIS.git
```

#### 2. Make directories `tmp/` and `log/` writable

```bash
sudo chmod -R a+rw tmp log
```

#### 3. Download nette dependencies

```bash
composer update
```

#### 4. Configure db

open in browser localhost/phpmyadmin, copy to SQL dialog and set ***password***

```
CREATE DATABASE `doctrine_devel` COLLATE 'utf8_czech_ci';
CREATE USER 'doctrine'@'localhost' IDENTIFIED BY 'heslo';
GRANT USAGE ON * . * TO 'doctrine'@'localhost' IDENTIFIED BY 'heslo' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
GRANT ALL PRIVILEGES ON `doctrine\_%` . * TO 'doctrine'@'localhost';
FLUSH PRIVILEGES;
```

#### 5. Configure acces to db

edit file: ```app/config/config.local.neon```

```
parameters:

doctrine:
	user: doctrine
	password: password
	dbname: doctrine_devel
```

#### 6. Create db using terminal

```
php ./www/index.php orm:schema-tool:create
```

#### 7. Update db using doctrine from actual code

```
php ./www/index.php orm:schema-tool:update --force
```

#### 8. Start web server

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
sudo apt-get install php7.0-pdo-sqlite -y    #support for kdyby
sudo chgrp www-data /var/www
sudo chmod -R 777 /var/www
sudo chmod -R g+s /var/www
sudo usermod -a -G www-data $USER
```

### Composer
```
sudo apt-get install composer
```

## Other

## Create nette project (unnecessary)

```
composer create-project nette/sandbox pharmacy
cd pharmacy
composer require kdyby/doctrine
```
