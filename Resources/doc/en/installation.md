### Installation ###

#### New Project ####

The essiest way to install is use [PlatformLite](https://github.com/ad3n/PlatformLite)

+ Create an empty database

+ Clone [PlatformLite](https://github.com/ad3n/PlatformLite)

+ Run composer update

+ Follow the instructions and then

+ Run `php bin/console platform:setup`

#### Existing Project ####

+ Add dependencies

```lang=php
    "knplabs/knp-paginator-bundle": "^2.5@dev",
    "knplabs/knp-menu-bundle": "^2.1@dev",
    "friendsofsymfony/user-bundle": "^2.0@dev",
    "friendsofsymfony/jsrouting-bundle": "^2.0@dev"
    "symfonyid/symfonyid-admin-bundle": "^0.7"
````

+ Run composer update

+ Register the bundle

```lang=php
//AppKernel.php
    new Symfony\Bundle\AsseticBundle\AsseticBundle(),
    new FOS\UserBundle\FOSUserBundle(),
    new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
    new Knp\Bundle\MenuBundle\KnpMenuBundle(),
    new SymfonyId\AdminBundle\SymfonyIdAdminBundle(),
```

+ Register the config

```lang=php
symfonyid_admin:
    app_title: 'SYMFONYID ADMIN BUNDLE'
    app_short_title: 'SIAB'
    menu:
        name: 'symfonyid_menu'
        loader: 'symfonyid.admin.menu.default_menu_loader'
    upload_dir: 'uploads'
    driver: 'orm' #odm or both
    translation_domain: 'SymfonyIdAdminBundle'
    filters: ['name']
    user:
        form_class: 'symfonyid.admin.form.user_form' #your user form
        model_class: 'AppBundle\Entity\User' #your user entity
        auto_enable: true
```