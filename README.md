![Symfony](https://img.shields.io/badge/symfony-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)
![PhpStorm](https://img.shields.io/badge/phpstorm-143?style=for-the-badge&logo=phpstorm&logoColor=black&color=black&labelColor=darkorchid)


![](https://img.shields.io/badge/v6.0-Breaking%20Change-red)

# Symfony/Doctrine Seed Bundle
Used to load/unload seed data from a doctrine database or anything that needs seeded. 

Example would be to load a table with a list of states and abbreviations, or populate the users table with initial admin user(s).
Unlike the [DoctrineFixturesBundle](https://github.com/doctrine/DoctrineFixturesBundle) which is mainly for development this bundle is for seeding a database before the initial push to production.

## Installation
Install the package with:
```console
composer require evotodi/seed-bundle
```


(Optional) Load seeds as services `config/services.yaml`  
In Symfony default services.yaml classes in src/ are loaded as services, so creating a folder src/DataSeeds/ will load the seeds as services. 
```yaml
services:
    Evotodi\SeedBundle\DataSeeds\:
        resource: '../DataSeeds/*'
```

## Building a seed to populate a database
The `Seed` class is a `Command` and : 

- Must extend `Evotodi\SeedBundle\Command\Seed`
- Must return the seed name from the static `seedName` method
- Seed naming must follow the colon seperated naming convention for symfony console commands.


```php
<?php

namespace App\Seeds;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\User;

class UserSeed extends Seed
{
    
    /**
    * Return the name of your seed
    */
    public static function seedName(): string
    {
        /**
        * The seed won't load if this is not set
        * The resulting command will be seed:user
        */
        return 'user';
    }
    
    /**
    * Optional ordering of the seed load/unload.
    * Seeds are loaded/unloaded in ascending order starting from 0. 
    * Multiple seeds with the same order are randomly loaded. 
    */
    public static function getOrder(): int 
    {
      return 0; 
    }
    
    /**
    * The load method is called when loading a seed 
    */
    public function load(InputInterface $input, OutputInterface $output): int
    { 

        /**
        * Doctrine logging eats a lot of memory, this is a wrapper to disable logging
        */ 
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
        
        /**
        * Must return an exit code.
        * A value other than 0 or Command::SUCCESS is considered a failed seed load/unload.
        */ 
        return 0;
    }
    
    /**
    * The unload method is called when unloading a seed 
    */
    public function unload(InputInterface $input, OutputInterface $output): int
    {
        //Clear the table
        $this->manager->getConnection()->exec('DELETE FROM user');
        
        /**
        * Must return an exit code.
        * A value other than 0 or Command::SUCCESS is considered a failed seed load/unload.
        */ 
        return 0;
    }
}
```

## Seed commands
The SeedBundle gives you two default commands and one for each seed you made.  
With the previous example, I'd have:

```
bin/console seed:load
bin/console seed:unload
bin/console seed:user
```
*Note: If your seeds do not show up in the command list under seed: then clear the cache*

## Global seed commands
The global `seed:load` and `seed:unload` allow you to run multiple seeds in one command.  
The rest of this section will only show `seed:load` as it works the same as `seed:unload`.   

* `seed:load` will load all seeds in ascending order.  
* `seed:load user` will load only the user seed (see the example above).     
* Multiple seed like `seed:load user country town` will only load those seeds.  
* `seed:load --skip country` will load all seed except country. Multiple skips are allowed.
* `seed:load --debug` with the debug flag will print what will be loaded and in what order.
* `seed:load --break` will exit the seed load if a seed fails.
* `seed:load --from country` will start with the county seed and load from there skipping lesser order values and possibly skipping same order values as country.  
See [Global seed ordering](#global-seed-ordering) for more details.  

## Global seed name matching
Seed names are matched using [webmozarts/glob](https://github.com/webmozarts/glob) filtering by turning the seed names into path like strings.  
An example would be if you had the following seeds:
- prod:users:us
- prod:users:eu
- prod:users:it
- prod:prices
- prod:products
- dev:users
- dev:prices
- dev:products

And wanted to only load the users in the 'prod' group then call `seed:load prod/users/*`    
Or to load the whole 'prod' group then call `seed:load prod/**/*`  
Or load the 'dev' prices and products along with the 'prod' users then call `seed:load dev:prices dev:products prod:users:*`    

*Note: colons and forward slashed are interchangeable because all colons are replaced with forward slashes for filtering.* 

Please see the readme of [webmozarts/glob](https://github.com/webmozarts/glob) for more information on glob patterns.

## Global seed ordering
Every seed has a `getOrder` method that is used to sort them. The default value is `0`.  
Seeds are loaded/unloaded in ascending order.  

#### Caution: Seeds with the same order value are loaded semi-randomly. This is especially a concern when using the --from option. 
#### Example issue of ordering and --from
Seeds:
* seed-a order 0
* seed-b order 1
* seed-c order 1
* seed-d order 1
* seed-e order 2

Calling `seed:load --from seed-c` will start loading with seed-c but since seed-b and seed-d have the same order one or both may or may not be loaded depending on what order they were loaded into the registry.     
It is suggested to used the `--debug` flag to verify the order of loading or sequentially order your seeds.

## Manual seed commands
Calling `seed:user load` (from example above) will load only the user seed. Conversely calling `seed:user unload` will unload it.  

## Example project
In the example folder is a project that can be used to experiment with the Seed bundle.  
It shows how to seed a database or flat file. 

## Thanks
Thanks to soyuka/SeedBundle

## Contributions
Contributions are very welcome! 

Please create detailed issues and PRs.  

## Licence

This package is free software distributed under the terms of the [MIT license](LICENSE).

## Updates
* 2022-05-10 ![](https://img.shields.io/badge/v6.0-Breaking%20Change-red)
  * Breaking changes to previous versions as this is mostly a re-write of the bundle.
  * Seeds no longer need to have a name ending in seed
  * Setting the seed name is no longer supported in the configure method. Use the static method seedName to return the seed name.
  * The configuration file has been dropped
  * Php 8+ is required
  * Symfony 6+ is required
* 2021-12-06
  * Updated dependencies to allow for symfony 5.3 and 5.4
* 2020-06-03
  * Updated dependencies to allow for Symfony 4.4.* and 5.0.* and 5.1.*
* 2020-04-23
  * Updated dependencies to allow for Symfony 4.4.* and 5.0.*
  * Added a required return exit code to load and unload functions
  * Updated tests to reflect required return code
