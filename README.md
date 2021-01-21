# Slim3 Kickstarter
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
  - PHPMailer
  - Bootstrap 4
  - JQuery
  - scssphp/scssphp
  - matthiasmullie/minify
  - vlucas/phpdotenv
  - zeuxisoo/slim-whoops
  - nette/forms

## Installation :

`composer create-project tchauviere/slim3-kickstarter <app_name>` 
<br>
Will download the project and install everything for you, `<app_name>` is up to you.

*Once packages are installed you will be prompted interactively for `.env` file creation and global environment setup (assets and DB). 
<br>
<strong>If you do not want to be prompted just add</strong> `--no-interaction`<strong> to the</strong> `composer create-project`<strong> command up above</strong>*

If you don't go with interactive mode (default), please mind to do the following once your .env file is ready:
<br>
`php manager asset:compile`
<br>
`php manager migration:run up`
<br>
`php manager seed:run`
<br><br>
Start your developpement :)

## Guide :

### Assets management

`php manager assets:compile [<type>] [--watch]`
<br><br>
Will take care of converting your `scss` files and minify them as well as `js` files from `/assets` directory
to respectively `/public/css` and `/public/js` directories 
(no binary dependencies required).

- `[<type>]` (optionnal): `scss` or `js` (Tell compilator to only take care of `assets/js` or `assets/css`
- `[--watch]` (optionnal): Watch specified folder and auto-compile whenever a change is made

### Cache clear
`php manager cache:clear`
<br><br>
Clear `/cache` folder


### Migration creation

`php manager migration:create <migration_name>`
<br><br>
Will automatically creates a new migration file to `/db/migrations` from this file you can follow Phinx documentation to describe your migration.
<br>
http://docs.phinx.org/en/latest/migrations.html

- `<migration_name>`: Camel case migration name (eg.: Users, UsersAndRoles, ...)

### Migration running

`php manager migration:run <direction> [-t <target>] [--dry-run]`
<br><br>
Run your migrations UP or DOWN.

- `<direction>` : `up` or `down` (Tell manager to execute migration or rollback them).
- `[-t <target>]` (optionnal): `<target>` is the migration timestamp, if specified only this migration will be executed up or down.
- `[--dry-run]` (optionnal): Tell migration to be tested but not persisted to DB.

### Seed creation
`php manager seed:create <seed_name>`
<br><br>
Will automatically creates a new seed file to `/db/seeds`

- `<seed_name>`: Camel case seed name (eg.: Users, Roles, ...)

### Seed running
`php manager seed:run [--seed <name>]`
<br><br>
Run your seeds files.

- `--seed <seed_name>` (optionnal): If specified, will only run this seed file (eg.: Users, Roles, ...)

### Secret generation
`php manager secret:generate`
<br><br>
Will replace your secret located in `.env` file automatically.
<br> 
<strong>BE CAREFUL</strong> when using this if you have already users that are created.
Indeed, this secret is used to salt your Users passwords.

## Usefull links :
http://docs.phinx.org/en/latest/migrations.html
<br>
https://laravel.com/docs/5.8/eloquent
<br>
http://www.slimframework.com/docs/

## Note :
Feel free to open issues, ask questions or make some pull requests !
<br> 
I'm maintaining this on my spare time so give me some of yours to get back to you :)

Enjoy !

[![forthebadge](https://forthebadge.com/images/badges/built-with-love.svg)](https://forthebadge.com)
