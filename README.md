# Slim3 Boilerplate
[![Latest Stable Version](https://poser.pugx.org/tchauviere/slim3-kickstarter/v/stable)](https://packagist.org/packages/tchauviere/slim3-kickstarter)
[![Latest Unstable Version](https://poser.pugx.org/tchauviere/slim3-kickstarter/v/unstable)](https://packagist.org/packages/tchauviere/slim3-kickstarter)
[![License](https://poser.pugx.org/tchauviere/slim3-kickstarter/license)](https://packagist.org/packages/tchauviere/slim3-kickstarter)
[![composer.lock](https://poser.pugx.org/tchauviere/slim3-kickstarter/composerlock)](https://packagist.org/packages/tchauviere/slim3-kickstarter)


This slim3 boilerplate is made for everyone who would like to kickstart quickly a web project.

It is composed of many great packages such as:

  - Eloquent
  - Phinx
  - Monolog
  - Symfony Console
  - Bootstrap 3 (should be updated to version 4 soon)
  - JQuery
  - leafo/scssphp
  - matthiasmullie/minify

## Guide

### Create project

`composer create-project --no-interaction tchauviere/slim3-kickstarter <app_name>` :
Will download the project and install everything for you where `<app_name>` is up to you ;)

### Assets management

`php manager assets:compile` : 
Will take care of converting your `scss` files and minify them as well as `js` files from `/assets` directory
to respectively `/public/css` and `/public/js` directories 
(no binary dependencies required).

### Migration creation

`php manager migration:create <migration_name>` :
Will automatically creates a new migration file to `/db/migrations`
from this file you can follow Phinx documentation to describe your migration.
http://docs.phinx.org/en/latest/migrations.html

- `<migration_name>`: Camel case migration name (eg.: Users, UsersAndRoles, ...)

### Migration running

`php manager migration:run <direction> [-t <target>] [--dry-run]`

Run your migrations UP or DOWN.

- `<direction>` : `up` or `down` (Tell manager to execute migration or rollback them).
- `[-t <target>]` (optionnal): `<target>` is the migration timestamp, if specified only this migration will be executed up or down.
- `[--dry-run]` (optionnal): Tell migration to be tested but not persisted to DB.

### Seed creation
`php manager seed:create <seed_name>`

Will automatically creates a new seed file to `/db/seeds`

- `<seed_name>`: Camel case seed name (eg.: Users, Roles, ...)

### Seed running
`php manager seed:run [--seed <name>]`

Run your seeds files.

- `--seed <seed_name>` (optionnal): If specified, will only run this seed file (eg.: Users, Roles, ...)

### Secret generation
`php manager secret:generate`

Will replace your secret located in `/src/config/settings.php` automatically. BE CAREFUL when using this if you have already users that are created.
Indeed, this secret is used to salt your Users passwords.


## Note
Feel free to open issues, ask questions or make some pull requests ! I'm maintaining this on my spare time so give me some time to get back to you :)

Enjoy !

[![forthebadge](https://forthebadge.com/images/badges/built-with-love.svg)](https://forthebadge.com)
