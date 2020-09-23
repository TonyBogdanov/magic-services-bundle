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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TonyBogdanov\MagicServices\Annotation\MagicService;
use TonyBogdanov\MagicServices\Command\Traits\DebugContainerTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\DefinitionGenerator\DefinitionGeneratorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\DefinitionGenerator\DefinitionGeneratorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Singleton\ContainerBuilderSingleton;
use TonyBogdanov\MagicServices\Object\DefinitionObject;

/**
 * Class Dump
 *
 * @package TonyBogdanov\MagicServices\Command\Definition
 *
 * @MagicService(tags={"console.command"})
 */
class Dump extends Command implements
    InspectorAwareInterface,
    DefinitionGeneratorAwareInterface
{

    use DebugContainerTrait;
    use InspectorAwareTrait;
    use DefinitionGeneratorAwareTrait;

    /**
     * @return string|null
     */
    public static function getDefaultName() {
        
        return 'services:definitions:dump';
        
    }

    /**
     * @param array $definition
     *
     * @return string
     */
    protected function preview( array $definition ): string {

        $result = [];
        $padding = 0;

        foreach ( $definition as $key => $value ) {

            $padding = max( $padding, strlen( $key ) );

            if ( is_array( $value ) ) {

                foreach ( $value as $index => $line ) {

                    $result[] = [

                        0 < $index ? '' : $key,
                        is_array( $line ) ? json_encode( $line ) : $line,

                    ];

                }

            } else if ( is_string( $value  ) ) {

                $result[] = [ $key, $value ];

            } else {

                $result[] = [ $key, json_encode( $value ) ];

            }

        }

        return implode( "\n", array_map( function ( array $item ) use ( $padding ): string {

            return ( $item[0] ? '<comment>' . $item[0] . '</comment>: ' : '  ' ) .
                str_repeat( ' ', $padding - strlen( $item[0] ) ) . $item[1];

        }, $result ) );

    }

    protected function configure() {

        $this
            ->setDescription( 'Dumps a list of detected magic services.' )
            ->addOption( 'previews', 'p', InputOption::VALUE_NONE, 'Dump definition previews.' );

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws ReflectionException
     */
    protected function execute( InputInterface $input, OutputInterface $output ): int {

        ContainerBuilderSingleton::setContainerBuilder( $this->getContainerBuilder() );

        $ui = new SymfonyStyle( $input, $output );

        $dumpPreviews = $input->getOption( 'previews' );

        $ui->writeln( 'Scanning services' );
        $definitions = $this->getInspector()->resolveDefinitions();

        if ( 0 === count( $definitions ) ) {

            $ui->warning( 'The magic_services.definitions.services configuration matches no eligible classes,' .
                ' nothing can be detected.' );

            return 0;

        }

        $ui->table( array_merge( [

            'Service',
            'Detection',
            'Ignored',

        ], $dumpPreviews ? [

            'Definition',

        ] : [] ), array_map( function ( DefinitionObject $object ) use ( $dumpPreviews ): array {

            $format = function ( string $value ) use ( $object ): string {

                return $object->isIgnored() ? '<fg=blue>' . strip_tags( $value ) . '</>' : $value;

            };

            return array_map( $format, array_merge( [

                $object->getName(),
                $object->getAnnotation() ? 'Annotation' : 'Interface',
                $object->isIgnored() ? 'YES' : 'NO',

            ], $dumpPreviews ? [

                $object->isIgnored() ? '-' : $this->preview( $this->getDefinitionGenerator()->generate( $object ) ),

            ] : [] ) );

        }, $definitions ) );

        return 0;

    }

    /**
     * Dump constructor.
     */
    public function __construct() {

        parent::__construct();

    }

}
