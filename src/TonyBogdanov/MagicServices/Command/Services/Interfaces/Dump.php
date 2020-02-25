<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Services\Interfaces;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TonyBogdanov\MagicServices\Inspector\InterfaceInspector;
use TonyBogdanov\MagicServices\Object\InterfaceObject;

/**
 * Class Dump
 *
 * @package TonyBogdanov\MagicServices\Command\Services\Interfaces
 */
class Dump extends Command {

    /**
     * @var InterfaceInspector
     */
    protected $interfaceInspector;

    protected function configure() {

        $this->setDescription( "Dumps a list of interfaces / classes matching the configured" .
            " magic_services.interfaces names / patterns." );

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute( InputInterface $input, OutputInterface $output ) {

        $ui = new SymfonyStyle( $input, $output );

        if ( ! $this->interfaceInspector->canFindInterfaces() ) {

            $ui->error( "The magic_services.interfaces configuration option is empty and therefore no interfaces /" .
                " classes can be detected." );
            return;

        }

        $interfaces = $this->interfaceInspector->resolveInterfaces();
        if ( 0 === count( $interfaces ) ) {

            $ui->error( "The magic_services.interfaces configuration option doesn't match any classes / interfaces." );
            return;

        }

        $ui->table( [

            'Class / Interface',
            'Aware Interface',
            'Exists',
            'Valid',

        ], array_map( function ( InterfaceObject $interface ): array {

            $exists = interface_exists( $interface->getClassName() );
            $valid = $exists && InterfaceObject::createFromReflection(

                $interface->getConfig(),
                new \ReflectionClass( $interface->getClassName() )

            )->same( $interface );

            return [

                $interface->getDependency()->getType(),
                $interface->getName() . '\\' . $interface->getBaseClassName(),

                $exists ? '<info>YES</info>' : '<fg=red>NO</>',
                $valid ? '<info>YES</info>' : '<fg=red>NO</>',

            ];

        }, $interfaces ) );

    }

    /**
     * Dump constructor.
     *
     * @param InterfaceInspector $interfaceInspector
     */
    public function __construct( InterfaceInspector $interfaceInspector ) {

        parent::__construct( 'services:interfaces:dump' );

        $this->interfaceInspector = $interfaceInspector;

    }

}
