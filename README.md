Module
======
The `module` package is a module/modular system.


Main Usecase
=====
If your want your app to support third-party development with features like:
* install, upgrade, and update notifications
* Decoupled app specific modules


Installation
=====

#### Step 1
Open your composer.json file and add the following line:
```
{
    "require": {
        "ryun/module": "dev-master"
    },
}
```

Run composer update from the command line
```
composer update
```

#### Step 2
Add the following to the list of service providres in app/config/app.php.
```
	'Ryun\Module\ModuleServiceProvider',
```

#### Step 3
Run the following command to finish setup:
```
php artisan module:setup
```

Add psr-0 autoloading for the modules directory, the entry below assumes the default config file is being used.
For this example the modules will be stored in the folder `app/Modules/` with the namespace `App\Modules`

```
"psr-0": {
    "App\\Modules": ""
}
```

Generate Module Command
=====
To generate a boilerplate module, run the following command:
```
php artisan module:make [module name]
```

Example Module
=====
I will create a new repo for an "Example module" shortly.



### Disclaimer
This package is under heavy development.
For now, use only for testing and development purposes only.