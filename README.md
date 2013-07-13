Pirate Scripts
==============

Collection of commandline scripts for use in the Slovenian Pirate Party.
Some mite be of use to others.


#### Daily update script
Script scrapes
- vanilla forum
- mediawiki install
- wordpress site
and generates an uplifting report to be sent to an email address (think mailinglist)

Could add:
- Facebook page/group
- Twitter account
- New member registrations
- Github commits?
- Private mediawiki (rss behind login)

#### [Planned] Financal report builder
Parse CSV from the bank and generate an anomnised transaction report, possibly update it on the website.

#### Other ideas
- Regular meeting planner
- Deadline warning helper (meeting reports, bank reports, event reports ...)
- Email support system integration


Deploy
------

### Configure application
Copy app/conf/paramaters.yml.dist to app/conf/paramaters.yml and configure correctly

### Run composer

Install composer
    curl -s http://getcomposer.org/installer | php

NOTE: You can install composer globaly on your system

Install dependencies
    php composer.phar install

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