<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Definition;

use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use TonyBogdanov\MagicServices\Annotation\MagicService;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\DefinitionGenerator\DefinitionGeneratorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\DefinitionGenerator\DefinitionGeneratorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutoconfigure\ParameterMagicServicesDefinitionsAutoconfigureAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutoconfigure\ParameterMagicServicesDefinitionsAutoconfigureAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutowire\ParameterMagicServicesDefinitionsAutowireAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsAutowire\ParameterMagicServicesDefinitionsAutowireAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsPath\ParameterMagicServicesDefinitionsPathAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\ParameterMagicServicesDefinitionsPath\ParameterMagicServicesDefinitionsPathAwareTrait;
use TonyBogdanov\MagicServices\Object\DefinitionObject;

/**
 * Class Generate
 *
 * @package TonyBogdanov\MagicServices\Command\Definition
 *
 * @MagicService(tags={"console.command"})
 */
class Generate extends Command implements
    InspectorAwareInterface,
    DefinitionGeneratorAwareInterface,
    ParameterMagicServicesDefinitionsPathAwareInterface,
    ParameterMagicServicesDefinitionsAutowireAwareInterface,
    ParameterMagicServicesDefinitionsAutoconfigureAwareInterface
{
    
    use InspectorAwareTrait;
    use DefinitionGeneratorAwareTrait;
    use ParameterMagicServicesDefinitionsPathAwareTrait;
    use ParameterMagicServicesDefinitionsAutowireAwareTrait;
    use ParameterMagicServicesDefinitionsAutoconfigureAwareTrait;

    /**
     * @return string|null
     */
    public static function getDefaultName() {

        return 'services:definitions:generate';

    }

    protected function configure() {

        $this->setDescription( 'Generates service definitions for detected magic services and writes them to the' .
            ' path configured in <comment>magic_services.definitions.path</comment>.' );

    }

    /**
     * @param array $definitions
     * @param bool $autoWire
     * @param bool $autoConfigure
     *
     * @return $this
     */
    protected function injectDefaults( array & $definitions, bool $autoWire, bool $autoConfigure ): self {

        if ( ! $autoWire && ! $autoConfigure ) {

            return $this;

        }

        $definitions['_defaults'] = [];

        if ( $autoWire ) {

            $definitions['_defaults']['autowire'] = true;

        }

        if ( $autoConfigure ) {

            $definitions['_defaults']['autoconfigure'] = true;

        }

        return $this;

    }

    /**
     * @param array $definitions
     * @param array $services
     *
     * @return $this
     */
    protected function injectServiceDefinitions( array & $definitions, array $services ): self {

        $services = array_map( function ( DefinitionObject $object ): array {

            return [

                'name' => $object->getReflection()->getName(),
                'definition' => $this->getDefinitionGenerator()->generate( $object ),

            ];

        }, $services );

        $services = array_reduce( $services, function ( array $dump, array $item ): array {

            $dump[ $item['name'] ] = $item['definition'];
            return $dump;

        }, [] );

        $definitions = array_merge( $definitions, $services );
        return $this;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws ReflectionException
     */
    protected function execute( InputInterface $input, OutputInterface $output ): int {

        $ui = new SymfonyStyle( $input, $output );

        $ui->writeln( 'Analyzing settings' );
        $autoWire = $this->getParameterMagicServicesDefinitionsAutowire();
        $autoConfigure = $this->getParameterMagicServicesDefinitionsAutoconfigure();

        $ui->writeln( 'Scanning services' );
        $services = $this->getInspector()->resolveDefinitions( false );

        if ( 0 === count( $services ) ) {

            $ui->warning( 'The magic_services.definitions.services configuration matches no eligible classes,' .
                ' nothing will be generated.' );

            return 0;

        }

        $ui->writeln( 'Generating definitions' );

        $definitions = [];

        $this->injectDefaults( $definitions, $autoWire, $autoConfigure );
        $this->injectServiceDefinitions( $definitions, $services );

        ( new Filesystem() )->dumpFile(

            $this->getParameterMagicServicesDefinitionsPath(),
            Yaml::dump( [ 'services' => $definitions ], 4 )

        );

        return 0;

    }

    /**
     * Generate constructor.
     */
    public function __construct() {

        parent::__construct();

    }

}
