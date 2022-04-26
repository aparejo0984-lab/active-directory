# Active Directory
Active Directory Package to import users

## Installation
This project using composer.
```
$ composer require a.parejo/active-directory
```

## Usage
Import users from active directory
```php
<?php

use RandomPassword\Password;

$password = new Password(10);
echo $password->generate();
```