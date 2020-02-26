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
  definitions:
    path: '%kernel.project_dir%/config/magic_services.yaml'
  aware:
    path: '%kernel.project_dir%/src/DependencyInjection/Aware'
    namespace: 'App\DependencyInjection\Aware'
    parameters:
      - regex: '/^kernel\.(.+)$/i'
        name: 'ParameterKernel$1'
      - regex: '/^app\.(.+)$/i'
        name: 'ParameterApp$1'
    services:
      - type: 'Doctrine\Common\Annotations\Reader'
        service: '@Doctrine\Common\Annotations\Reader'
        name: 'AnnotationReader'
      - type: 'Psr\Log\LoggerInterface'
        service: '@logger'
        name: 'Logger'
      - 'Doctrine\ORM\EntityManager'
```

`definitions.path`

Path to the configuration file where generated magic service definitions will be
stored (must end with `.yml` or `.yaml`).

Definitions dumped here will be loaded automatically by the bundle, no need to do it
yourself.

`aware.path`

Path to the folder where *aware* interfaces and traits will be generated. It should be
part of your package so that composer can autoload the files.

`aware.namespace`

The namespace under which all generated *aware* interfaces and traits will live.\
Example:

The final class name of an *aware* interface with the name `FontManager` and
configured namespace `App\DependencyInjection\Aware` will be
`App\DependencyInjection\Aware\FontManager\FontManagerAwareInterface`.

`aware.parameters`

An array of definitions for generating *aware* interfaces from container parameters.
Each entry must be an array, or when automatic generation of optional settings is
sufficient, you can also use a string (will be used as the `regex` sub-setting).

`aware.parameters.*.regex`

A regular expression for matching one or more parameters defined in the container, to
generate *aware* interfaces for.

`aware.parameters.*.name`

An optional name for the generated *aware* interface.

You can also use capturing groups from the regular expression to insert matched
parts here.

If this is omitted, a name will be automatically selected by prepending the name of
the matched parameter with a `parameter` prefix.

For example, for the `kernel.project_dir` parameter, the following name will be used:
`ParameterKernelProject_Dir`.

The **final** *aware* name will be normalized (see Normalization).

`aware.services`

An array of definitions for generating *aware* interfaces from services.
Each entry must be an array, or when automatic generation of optional settings is
sufficient, you can also use a string (will be used as the `type` sub-setting).

`aware.services.*.type`

The type of the object an *aware* interface is being generated for. This should be a
full class or interface name.

`aware.services.*.service`

An optional name of a service, of the configured type, to be used for dependency
injection.

If this is omitted, a service with the name of the configured type will be assumed.
This is useful when using a magic service, or an interface.

`aware.services.*.name`

An optional name for the generated *aware* interface.

The **final** *aware* name will be normalized (see Normalization).

If this is omitted, a name will be automatically selected from the base name of the
configured type after stripping an `Interface` suffix (if such is present).

For example, for a configured type `Doctrine\Common\Annotations\Reader` the selected
name will be `Reader`.

Use this to specify more concrete names to avoid duplication.

This name will be used when generating the final *aware* interface and trait, as well
as the getters / setters.

For example, for an `AnnotationReader` name & `Doctrine\Common\Annotations\Reader`
type, the following *aware* interface & trait will be generated:

```php
Configured\Aware\Namespace\AnnotationReader\AnnotationReaderAwareInterface;
Configured\Aware\Namespace\AnnotationReader\AnnotationReaderAwareTrait;
```

and the following getters / setters:

```php
getAnnotationReader(): \Doctrine\Common\Annotations\Reader;
setAnnotationReader( \Doctrine\Common\Annotations\Reader $annotationReader );
```

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

`services:definitions:dump`

Dumps a table of service definitions matching the configured `services` names /
patterns.

## Annotations

`TonyBogdanov\MagicServices\Annotation\MagicService`

Add this annotation to any class you want to have service definition generated for.
If your class uses *aware* interfaces or implements 
`TonyBogdanov\MagicServices\Aware\ServiceAwareInterface`, you don't need to also
add the annotation.

The annotation, however, allows you to further customize the generated definition:

`@MagicService(public=true)`

This will mark the generated service as [public](https://symfony.com/doc/current/service_container.html#public-versus-private-services).

`@MagicService(tags={"console.command","another_tag"}`

This allows you to specify [tags](https://symfony.com/doc/current/service_container/tags.html) for the service.

## Normalization

When generating the final *aware* interface / trait name, a normalization process is
performed on the input. When it is a class / interface name, only the base name is
used.

The sequence is split into words, where each word may only contain lowercase or
uppercase latin letters, numbers and an underscore. Everything else is replaced
with a single space. The first letters of all words are then capitalized and
the spaces are removed, but underscores are kept.

Example:

- `kernel.project_dir` becomes `KernelProject_Dir`
- `App\Service\FontManager` becomes `FontManager`.

*Aware* interfaces / traits will use this name as both a folder and a class name,
adding an additional `AwareInterface` and `AwareTrait` suffix respectively.
