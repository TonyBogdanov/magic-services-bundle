<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Services\Parameters;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TonyBogdanov\MagicServices\Inspector\ParameterInspector;
use TonyBogdanov\MagicServices\Object\InterfaceObject;
use TonyBogdanov\MagicServices\Object\ParameterObject;

/**
 * Class Dump
 *
 * @package TonyBogdanov\MagicServices\Command\Services\Parameters
 */
class Dump extends Command {

    /**
     * @var ParameterInspector
     */
    protected $parameterInspector;

    protected function configure() {

        $this->setDescription( "Dumps a list of container parameters matching the configured" .
            " magic_services.parameters regular expression." );

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute( InputInterface $input, OutputInterface $output ) {

        $ui = new SymfonyStyle( $input, $output );

        if ( ! $this->parameterInspector->hasRegex() ) {

            $ui->error( "The magic_services.parameters configuration option is NULL and therefore no parameters" .
                " can be detected." );
            return;

        }

        $parameters = $this->parameterInspector->getParameters();
        if ( 0 === count( $parameters ) ) {

            $ui->error( "The magic_services.parameters configuration option doesn't match any parameters." );
            return;

        }

        $ui->table( [

            'Parameter',
            'Interface',
            'Exists',
            'Valid',

        ], array_map( function ( ParameterObject $parameter ): array {

            $exists = interface_exists( $parameter->getInterface()->getClassName() );
            $valid = InterfaceObject::createFromReflection(

                $parameter->getConfig(),
                new \ReflectionClass( $parameter->getInterface()->getClassName() )

            )->same( $parameter->getInterface() );

            return [

                $parameter->getName(),
                $parameter->getInterface()->getName(),

                $exists ? '<info>YES</info>' : '<fg=red>NO</>',
                $valid ? '<info>YES</info>' : '<fg=red>NO</>',

            ];

        }, $parameters ) );

    }

    /**
     * Dump constructor.
     *
     * @param ParameterInspector $parameterInspector
     */
    public function __construct( ParameterInspector $parameterInspector ) {

        parent::__construct( 'services:parameters:dump' );

        $this->parameterInspector = $parameterInspector;

    }

}
