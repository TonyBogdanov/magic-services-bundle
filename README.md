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
  parameters_regex: '/^app\.|^kernel\.|^twig\.default_path/i'
  config_path: '%kernel.project_dir%/config/magic_services.yaml'
  aware_path: '%kernel.project_dir%/src/DependencyInjection/Aware'
  aware_namespace: 'App\DependencyInjection\Aware'
```

`parameters_regex`

An optional regular expression to determine which parameters from the container should
have an *aware* interface generated for them.

If left unconfigured parameters will yield no *aware* interfaces.

`config_path`

Path to the generated magic service definitions (must end with `.yml` or `.yaml`).
Definitions dumped here will be loaded automatically by the bundle, no need to do it
yourself.

`aware_path`

Path to the folder where *aware* interfaces and traits will be generated. It should be
part of your package so that composer can autoload the files.

`aware_namespace`

The namespace under which all generated *aware* interfaces and traits will live.

## Commands

`services:parameters:dump`

Dumps a table of all container parameters matching the configured `parameters_regex`
expression, along with the name of the *aware* interface that would be generated per
each. Additionally displays whether the interface already exists and if its structure
is valid, i.e. would not be changed if re-generated.

`services:parameters:generate`

Generates *aware* interfaces for container parameters matching the configuration.
Interface files will be saved under `aware_path`. Use `services:parameters:dump` to
review the parameters before generating.
