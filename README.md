Module
======
The `module` package is a module/modular system.


Main Usecase
=====
If your want your app to support third-party development with features like:
* install, upgrade, and update notifications
* Decoupled app specific modules


Setup
=====
Add psr-0 autoloading for the modules directory, this assumes the default config file is being used.
For this example the modules will be stored in the folder `app/Modules/` with the namespace `App\Modules`

```
"psr-0": {
    "App\\Modules": ""
}
```

Example Module
=====
I will create a new repo for an "Example module" shortly.


### Disclaimer
This package is under heavy development.
For now, use only for testing and development purposes only.