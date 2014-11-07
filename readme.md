Yaml file support for Laravel 4 Configuration
====

This package is based on a devitek/yaml-configuration package. It uses Symfony/Yaml parser. This Yaml configuration package is part of Larapress CMS.

Installation
====

Run the following command from your terminal:

```
composer require "bosnadev/yaml: 1.*"
```

or add this to require section in your composer.json file:

```
"bosnadev/yaml": "0.*"
```

and run ```composer update```

After updating composer, add the **YamlServiceProvider** to the providers array in app/config/app.php

```
'providers' => [
  // other providers
  ...
  
  'Bosnadev\Yaml\YamlServiceProvider'
];
```

And that's it! You can now add your .yaml configuration files into **app/config** or in your package config folders. 
You can continue to use regular php files for configs.

PHP:

```
<?php
return [
  'enable_registration' => true,
  'themes_dir'          => %public_path%/themes
];
```

Will be equivalent to :

```
---
enable_registration: true
themes_dir: %public_path%/themes
```

Paths Helpers
====

You can use paths helpers provided by Laravel like that :

```yaml
routes_file: %app_path%/routes.php
unit_test: %base_path%/behat.yml
main_style: %public_path%/css/style.css
manifest: %storage_path%/meta
```

* %app\_path% refers to app\_path()
* %base\_path% refers to base\_path()
* %public\_path% refers to public\_path()
* %storage\_path% refers to storage\_path()

License
--------

This package is open-sourced software licensed under MIT License.