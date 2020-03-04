<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Definition;

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
    ParameterMagicServicesDefinitionsPathAwareInterface
{
    
    use InspectorAwareTrait;
    use DefinitionGeneratorAwareTrait;
    use ParameterMagicServicesDefinitionsPathAwareTrait;

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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \ReflectionException
     */
    protected function execute( InputInterface $input, OutputInterface $output ): int {

        $ui = new SymfonyStyle( $input, $output );

        $ui->writeln( 'Scanning services' );
        $definitions = $this->getInspector()->resolveDefinitions( false );

        if ( 0 === count( $definitions ) ) {

            $ui->warning( 'The magic_services.definitions.services configuration matches no eligible classes,' .
                ' nothing will be generated.' );

            return 0;

        }

        $ui->writeln( 'Generating definitions' );

        ( new Filesystem() )->dumpFile(

            $this->getParameterMagicServicesDefinitionsPath(),
            Yaml::dump( [ 'services' => array_reduce( array_map( function ( DefinitionObject $object ): array {

                return [

                    'name' => $object->getReflection()->getName(),
                    'definition' => $this->getDefinitionGenerator()->generate( $object ),

                ];

            }, $definitions ), function ( array $dump, array $item ): array {

                $dump[ $item['name'] ] = $item['definition'];
                return $dump;

            }, [] ) ], 4 )

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
