# Auto-magic service definition generator

[![Latest Stable Version](https://poser.pugx.org/tonybogdanov/magic-services-bundle/v/stable)](https://packagist.org/packages/tonybogdanov/magic-services-bundle)
[![License](https://poser.pugx.org/tonybogdanov/magic-services-bundle/license)](https://packagist.org/packages/tonybogdanov/magic-services-bundle)

## Installation

```bash
composer require tonybogdanov/magic-services-bundle
```

## Configuration

Example:

```yaml
magic_services:
  aware_path: '%kernel.project_dir%/src/DependencyInjection/Aware'
  aware_namespace: 'App\DependencyInjection\Aware'
  config_path: '%kernel.project_dir%/config/magic_services.yaml'
  parameters:
    - '/^app\./i'
    - '/^kernel\./i'
    - '/^twig\.default_path/i'
  interfaces:
    - '%kernel.project_dir%/src/**/*.php'
    - '!%kernel.project_dir%/src/Entity/**/*.php'
    - 'Doctrine\ORM\EntityManager'
    - 'Symfony\Contracts\Cache\CacheInterface'
```

`aware_path`

Path to the folder where *aware* interfaces and traits will be generated. It should be
part of your package so that composer can autoload the files.

`aware_namespace`

The namespace under which all generated *aware* interfaces and traits will live.

`config_path`

Path to the configuration file where generated magic service definitions will be
stored (must end with `.yml` or `.yaml`).\
Definitions dumped here will be loaded automatically by the bundle, no need to do it
yourself.

`parameters`

An optional array of regular expressions to determine which parameters from
the container should have an *aware* interface generated for them.

If left unconfigured / empty, no *aware* interfaces will be generated.

`interfaces`

An optional array of glob patterns or interface / class names to determine classes /
interfaces, which should have an *aware* interface generated for them.

Each entry will be tested if pointing to an existing class or interface and considered
a glob pattern otherwise. Be sure that all classes / interfaces configured here
actually exist and are loadable.

You can also exclude files with a negative glob pattern by using `!` at the start
of the pattern.\
For example:

```yaml
interfaces:
  - 'src/*.php'
  - 'src/Hello.php'
``` 

These rules will match *all* PHP files in the `src/` folder, *except* for `Hello.php`.

Keep in mind that using a negative glob pattern to exclude files that were never
matched by a positive pattern will have no effect.

When a class / interface name is specified instead, only the base class / interface
name will be used when generating the *aware* interface. Common suffixes will also be
stripped (`Interface`).

For example, `Symfony\Contracts\Cache\CacheInterface` will yield the following *aware*
interface: `Aware\Namespace\Cache\CacheAwareInterface` and `getCache` / `setCache`.

`Doctrine\ORM\EntityManager` will yield the following *aware* interface:
`Aware\Namespace\EntityManagerAwareInterface` and `getEntityManager` /
`setEntityManager`.

Refer to the `services:interfaces:dump` command to review the names.

If left unconfigured / empty, no *aware* interfaces will be generated.

## Commands

`services:parameters:dump`

Dumps a table of all container parameters matching the configured `parameters`
expressions, along with the name of the *aware* interface that would be generated per
each. Additionally displays whether the interface already exists and if its structure
is valid, i.e. would not be changed if re-generated.

`services:parameters:generate`

Generates *aware* interfaces for container parameters matching the configuration.
Interface files will be saved under `aware_path`. Use `services:parameters:dump` to
review the parameters before generating.

`services:interfaces:dump`

Dumps a table of all classes / interfaces matching the configured `interfaces` names /
patterns, along with the name of the *aware* interface that would be generated per
each. Additionally displays whether the interface already exists and if its structure
is valid, i.e. would not be changed if re-generated.

`services:traits:generate`

Generates *aware* traits for all currently generated *aware* interfaces.\
Keep in mind that this command will scan **all** available *aware* interface files
and generate corresponding trait files for them. If you have obsolete or invalid
interface files, you will need to clean them yourself first.
