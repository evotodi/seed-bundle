
[![Build Status](https://travis-ci.org/evotodi/seed-bundle.svg?branch=master)](https://travis-ci.org/evotodi/seed-bundle)

# Symfony/Doctrine Seed Bundle

Used to load/unload seed data from the database. Example would be to load a table with a list of states and abbreviations, or populate the users table with initial admin user(s).
Unlike the [DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle) which is mainly for development this bundle is for seeding the database before the initial push to production.

## Installation
Install the package with:
```console
composer require evotodi/seed-bundle
```

## Configuration
Create the config yaml file `config/packages/evo_seed.yaml`
```yaml
evo_seed:
  directory: 'path/to/seeds/directory'
  namespace: 'Namespace\For\Seeds'
```

(optional) load seeds as services `config/services.yaml`  
In Symfony 4.4 default services.yaml classes in src/ are loaded as services, so creating a folder src/Seeds/ will load the seeds as services. 
```yaml
services:
    Evotodi\SeedBundle\DataSeeds\:
        resource: '../DataSeeds/*'
```

## Building a Seed

The `Seed` class is a `Command` and : 

- Must extend `Evotodi\SeedBundle\Command\Seed`
- Must have a class name that ends by `Seed`
- Must call `setSeedName` in the configure method
- Namespace MUST match namespace in evo_seed.yaml

```php
<?php

namespace App\Seeds;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\User;

class UserSeed extends Seed
{

    protected function configure()
    {
        //The seed won't load if this is not set
        //The resulting command will be {prefix}:country
        $this->setSeedName('mySeed');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output)
    { 

        //Doctrine logging eats a lot of memory, this is a wrapper to disable logging
        $this->disableDoctrineLogging();

        $users = [
            [
                'email' => 'admin@admin.com',
                'password' => 'password123',
                'roles' => ['ROLE_ADMIN'],
            ],


        ];

        foreach ($users as $user){
            $userRepo = new User();
            $userRepo->setEmail($user['email']);
            $userRepo->setRoles($user['roles']);
            $userRepo->setPassword($this->passwordEncoder->encodePassword($userRepo, $user['password']));
            $this->manager->persist($userRepo);
        }
        $this->manager->flush();
        $this->manager->clear();
        return 0; //Must return an exit code
    }
    
    public function unload(InputInterface $input, OutputInterface $output){
        //Clear the table
        $this->manager->getConnection()->exec('DELETE FROM user');
        return 0; //Must return an exit code
    }

    public function getOrder(): int 
    {
      return 0; 
    }
    

}
```

## Loading a seed

The SeedBundle gives you two default commands and one for each seed you made. With the previous example, I'd have:

```
bin/console seed:load #calls the load method of every seed
bin/console seed:unload #calls the unload method of every seed
bin/console seed:mySeed #load is implied
bin/console seed:mySeed unload
```

The global `seed:load` and `seed:unload` allow you to run multiple seeds in one command. You can of course skip seeds `bin/console seed:load --skip Town` but also name the one you want `bin/console seed:load Country`. For more informations, please use `bin/console seed:load --help`.

## Seed order

Every seed has a `getOrder` method that is used to sort them. The default value is `0`. 

## Thanks
Thanks to soyuka/SeedBundle

## Contributions
Contributions are very welcome! 

Please create detailed issues and PRs.  

## Licence

This package is free software distributed under the terms of the [MIT license](LICENSE).

## Updates
* 2020-04-23
    * Updated dependencies to allow for Symfony 4.4.* and 5.0.*
    * Added a required return exit code to load and unload functions
    * Updated tests to reflect required return code
