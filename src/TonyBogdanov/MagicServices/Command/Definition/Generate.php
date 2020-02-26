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
use TonyBogdanov\MagicServices\DefinitionGenerator;
use TonyBogdanov\MagicServices\Inspector;
use TonyBogdanov\MagicServices\Object\DefinitionObject;

/**
 * Class Generate
 *
 * @package TonyBogdanov\MagicServices\Command\Definition
 */
class Generate extends Command {

    /**
     * @var Inspector
     */
    protected $inspector;

    /**
     * @var DefinitionGenerator
     */
    protected $definitionGenerator;

    /**
     * @var string
     */
    protected $path;

    protected function configure() {

        $this->setDescription( 'Generates service definitions for detected magic services and writes them to the' .
            ' path configured in <comment>magic_services.definitions.path</comment>.' );

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     * @throws \ReflectionException
     */
    protected function execute( InputInterface $input, OutputInterface $output ) {

        $ui = new SymfonyStyle( $input, $output );

        $ui->writeln( 'Scanning services' );
        $definitions = $this->inspector->resolveDefinitions( false );

        if ( 0 === count( $definitions ) ) {

            $ui->warning( 'The magic_services.definitions.services configuration matches no eligible classes,' .
                ' nothing will be generated.' );

            return;

        }

        $ui->writeln( 'Generating definitions' );

        ( new Filesystem() )->dumpFile(

            $this->path,
            Yaml::dump( [ 'services' => array_reduce( array_map( function ( DefinitionObject $object ): array {

                return [

                    'name' => $object->getReflection()->getName(),
                    'definition' => $this->definitionGenerator->generate( $object ),

                ];

            }, $definitions ), function ( array $dump, array $item ): array {

                $dump[ $item['name'] ] = $item['definition'];
                return $dump;

            }, [] ) ], 4 )

        );

    }

    /**
     * Generate constructor.
     *
     * @param Inspector $inspector
     * @param DefinitionGenerator $definitionGenerator
     * @param string $path
     */
    public function __construct(

        Inspector $inspector,
        DefinitionGenerator $definitionGenerator,
        string $path

    ) {

        parent::__construct( 'services:definitions:generate' );

        $this->inspector = $inspector;
        $this->definitionGenerator = $definitionGenerator;

        $this->path = $path;

    }

}
