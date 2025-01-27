# H5P Plugin for Laravel Framework

Require it in the Composer.

```bash
composer require escolasoft/laravel-h5p
```

Publish the Views, Config and so things.

```bash
publish php artisan vendor:publish --provider="EscolaSoft\LaravelH5p\LaravelH5pServiceProvider"
```

Migrate the Database

```bash
php artisan migrate
```

Add to Composer-Classmap:

```php
'classmap': [
    "vendor/h5p/h5p-core/h5p-default-storage.class.php",
    "vendor/h5p/h5p-core/h5p-development.class.php",
    "vendor/h5p/h5p-core/h5p-event-base.class.php",
    "vendor/h5p/h5p-core/h5p-file-storage.interface.php",
    "vendor/h5p/h5p-core/h5p.classes.php",
    "vendor/h5p/h5p-editor/h5peditor-ajax.class.php",
    "vendor/h5p/h5p-editor/h5peditor-ajax.interface.php",
    "vendor/h5p/h5p-editor/h5peditor-file.class.php",
    "vendor/h5p/h5p-editor/h5peditor-storage.interface.php",
    "vendor/h5p/h5p-editor/h5peditor.class.php"
],
```

```php
'providers' => [
    EscolaSoft\LaravelH5p\LaravelH5pServiceProvider::class,
];
```

Create necessary symlinks, if it fails create the symlinks manually.
```bash
php artisan laravel-h5p:install
```

Link files (only necessary if the step above fails)

on linux:
```
cd public/assets/vendor/h5p
ln -s ../../../../storage/app/public/h5p/libraries
ln -s ../../../../storage/app/public/h5p/exports
```

on windows (in cmd with admin rights or without admin rights if dev mode is turned on):
```
mklink /d /j public\assets\vendor\h5p\libraries storage\app\public\h5p\libraries
mklink /d /j public\assets\vendor\h5p\exports storage\app\public\h5p\exports
```

You probably will need to add it to your `app/Http/Middleware/VerifyCsrfToken.php` due to H5P ajax requests without Laravel CSRF token:

```php
protected $except = [
    //
    'admin/h5p/ajax',
    'admin/h5p/ajax/*'
];
```

## Credits

[Abdelouahab Djoudi](https://github.com/djoudi/Laravel-H5P) - Package creator.

[Anass Boutakaoua](https://github.com/soyamore/Laravel-H5P) - Laravel 7 support and base for this package.
