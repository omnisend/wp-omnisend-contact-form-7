# wp-omnisend-contact-form-7

Plugin for _Contact Form 7_ WordPress plugin. More information can be found [here](https://wordpress.com/plugins/contact-form-7).

https://contactform7.com/2020/07/28/accessing-user-input-data/

## Key class

-   `WPCF7_Service` - integration should implement class and be added to `WPCF7_Integration` with `add_service` to be displayed in integration list

## Contact form 7 actions

-   `wpcf7_init` Plugin initiated. Register Omnisend service.
-   `wpcf7_submit` Website user submitted form. Check and send contact to Omnisend.
-   `wpcf7_save_contact_form` Admin user saved/modified form - save Omnisend related data to form.

## Contact form 7 filters

-   `wpcf7_editor_panels` Add Omnisend configuration [panel](img/panel.png) for selected form.
-   `wpcf7_pre_construct_contact_form_properties` Constructs contact form properties. This is called only once from the constructor.

## PHP Linting

WordPress.org team mandates our plugin to be linted
against [WordPress coding standards](https://github.com/WordPress/WordPress-Coding-Standards).

After each push to any branch `PHP Standards` action will run and all the PHP code will be linted. See action output for results.

### Linting locally

Tools needed:

-   php (7.4 version is recommended because at the time of writing WordPress coding standards supports only up to 7.4 version);
-   composer (can be installed as described in https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos);

After installing those tools one can run in local plugin dir (omnisend-for-contact-form-7) helper script:

```shell
./lint.sh check
./lint.sh fix
```

or all commands manually. Following commands

```shell
composer update
composer install
```

install linting tool and standards. And then actual linting `phpcs` script can be initiated with

```shell
./vendor/squizlabs/php_codesniffer/bin/phpcs --ignore=.js --standard=WordPress omnisend-connect
```

A second `phpcbf` script can be run to automatically correct coding standard violations:

```shell
./vendor/squizlabs/php_codesniffer/bin/phpcbf --ignore=.js --standard=WordPress omnisend-connect
```
