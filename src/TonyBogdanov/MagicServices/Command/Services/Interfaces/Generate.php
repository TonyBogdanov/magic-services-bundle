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
use TonyBogdanov\MagicServices\Util\Generator;

/**
 * Class Generate
 *
 * @package TonyBogdanov\MagicServices\Command\Services\Interfaces
 */
class Generate extends Command {

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var InterfaceInspector
     */
    protected $interfaceInspector;

    protected function configure() {

        $this->setDescription( "Generates aware interfaces for interfaces / classes matching the configured" .
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

        $ui->progressStart( count( $interfaces ) );

        foreach ( $interfaces as $interface ) {

            $this->generator->dumpInterface( $interface );
            $ui->progressAdvance();

        }

        $ui->progressFinish();

    }

    /**
     * Generate constructor.
     *
     * @param Generator $generator
     * @param InterfaceInspector $interfaceInspector
     */
    public function __construct( Generator $generator, InterfaceInspector $interfaceInspector ) {

        parent::__construct( 'services:interfaces:generate' );

        $this->generator = $generator;
        $this->interfaceInspector = $interfaceInspector;

    }

}
