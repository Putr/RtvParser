Dnevnik Bot
===========


Deploy
------

### Configure nginx
Copy etc/nginx.conf.dist to etc/nginx.conf and configure it

### Setup permissions
(For ubuntu. You may have to install setfacl)

    sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
    sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx app/cache app/logs

### Configure application
Copy app/conf/paramaters.yml.dist to app/conf/paramaters.yml and configure correctly

### Run composer

Install composer
    curl -s http://getcomposer.org/installer | php

NOTE: You can install composer globaly on your system

Install dependencies
    php composer.phar install

### [PROD ONLY] Dump assests
    php app/console assetic:dump --env=prod --no-debug

Setup database:
	sudo chown USER:www-data app/data -r
	sudo chmod 775 app/data
    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

### Start using it!
To get list of commands run:
    php app/console


FAQ
---

### Why PHP?
I know php very well. Done is better than perfect.

### Why Symfony2.x for a cli project?
Started looking for php-cli-micro framework. Found only crap(TM), the only viable option was
Symfony CLI component. But as I started thinking I'm gonna need dependency injection, logging,
maby even a database ... you can see where this is going. Fact that i know Symfony2 helped :).