# Yet another DoctrineDataFixture Module for Zend Framework 3

## Introduction

This is fork from [Houndog/DoctrineDataFixtureModule](https://github.com/Hounddog/DoctrineDataFixtureModule).


The DoctrineDataFixtureModule module intends to integrate [Doctrine2 ORM Data Fixtures](https://github.com/doctrine/data-fixtures) with Zend Framework 3.

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
$ php composer.phar require dkorsak/doctrine-data-fixture-module
```

Then open `config/application.config.php` and add `DoctrineModule`, `DoctrineORMModule` and 
`DoctrineDataFixtureModule` to your `modules`

#### Registering Fixtures

To register fixtures with Doctrine module add the fixtures in your configuration.

```php
<?php
return array(
    'doctrine' => array(
        'fixture' => array(
            'ModuleName' => __DIR__ . '/../src/ModuleName/Fixture',
        )
    )
);
```

## Usage

#### Default
```sh
./vendor/bin/doctrine-module orm:fixtures:load 
```

#### Purge with truncate and without confirmation
```sh
./vendor/bin/doctrine-module orm:fixtures:load -n --purge-with-truncate 
```

#### Append data instead of delete
```sh
./vendor/bin/doctrine-module orm:fixtures:load -n --append
```

## How to inject container into fixtures file


```php
<?php

namespace Application\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineDataFixtureModule\ContainerAwareInterface;
use DoctrineDataFixtureModule\ContainerAwareTrait;

class LoadUser implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $myService = $this->container->get('my_service');        
    }
}

```