# Laravel Lectern

The `lectern` package provides you with classes and interfaces which help you with abstracting the data layer of your laravel application by embracing the repository pattern.

![Packagist Version](https://img.shields.io/packagist/v/aplr/lectern?style=flat-square)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/aplr/lectern/Tests?style=flat-square)
![PHP from Packagist](https://img.shields.io/packagist/php-v/aplr/lectern?style=flat-square)
[![GitHub license](https://img.shields.io/github/license/aplr/lectern?style=flat-square)](https://github.com/aplr/lectern/blob/master/LICENSE)

## Installation

To install the latest version of `aplr/lectern` just require it using composer.

```bash
composer require aplr/lectern
```

This package is using Laravel's package auto-discovery, so it doesn't require you to manually add the ServiceProvider. If you've opted out of this feature, add the ServiceProvider to the providers array in config/app.php:

```php
Aplr\Lectern\ServiceProvider::class,
```

## Usage

> TODO, dig through the code for now

## Licence

Lectern is licenced under The MIT Licence (MIT).
