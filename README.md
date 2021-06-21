## Crud Generator for initial setup - Documentation

### Install
```php
composer require pemba/crud
```

### Enable the package (Optional)
This package implements Laravel auto-discovery feature. After you install it, the package provider and facade are added automatically for laravel >= 5.5.

### Configuration
After installation, you have to publish config file.
```php
php artisan vendor:publish --tag=crud-config
```

#Usage
To generate module enter following command
```php
php artisan create:module moduleName --migration //--migration is optional
```
Above command create following files/folders
```text
1.Http/Controllers
    ModuleNameController.php
2.Http/Requests
    ModuleNameRequest.php
3.Models
    ModuleName.php
4.Database/migrations
5.Database/seeders
6.Routes
    web.php
7.Views
    index.blade.php
    create.blade.php
    edit.blade.php
    form.blade.php
8.Policy
    ModuleNamePolicy.php
9.Providers
    ModuleNameServiceProvider.php
```

### Other commands
with php artisan
```php
create:model ModelName --migration moduleName //--migration is optional
create:controller ControllerName moduleName
create:migration migration_name table_name moduleName
```
