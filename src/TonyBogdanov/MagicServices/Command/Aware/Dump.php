<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Aware;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TonyBogdanov\MagicServices\AwareGenerator;
use TonyBogdanov\MagicServices\Inspector;
use TonyBogdanov\MagicServices\Object\AwareObject;

/**
 * Class Dump
 *
 * @package TonyBogdanov\MagicServices\Command\Aware
 */
class Dump extends Command {

    /**
     * @var Inspector
     */
    protected $inspector;

    /**
     * @var AwareGenerator
     */
    protected $awareGenerator;

    /**
     * @param SymfonyStyle $ui
     * @param string $title
     * @param array $objects
     *
     * @return $this
     */
    protected function listObjects( SymfonyStyle $ui, string $title, array $objects ) {

        $ui->title( $title );
        $ui->table( [

            'Name',
            'Type',
            'Dependency',
            'Interface',
            'Trait',

        ], array_map( function ( AwareObject $object ): array {

            return [

                $object->getName(),
                $object->getType(),
                $object->getDependency(),

                $this->awareGenerator->isInterfaceExist( $object ) ? '<info>EXISTS</info>' : '<error>MISSING</error>',
                $this->awareGenerator->isTraitExist( $object ) ? '<info>EXISTS</info>' : '<error>MISSING</error>',

            ];

        }, $objects ) );

        return $this;

    }

    protected function configure() {

        $this
            ->setDescription( 'Dumps a list of detected *aware* objects based on the configured parameters' .
                ' & services.' )
            ->addOption( 'parameters', 'p', InputOption::VALUE_NONE, 'Dump parameters.' )
            ->addOption( 'services', 's', InputOption::VALUE_NONE, 'Dump services.' );

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute( InputInterface $input, OutputInterface $output ) {

        $ui = new SymfonyStyle( $input, $output );

        $dumpParameters = $input->getOption( 'parameters' );
        $dumpServices = $input->getOption( 'services' );

        if ( ! $dumpParameters && ! $dumpServices ) {

            $ui->error( 'Nothing to dump. Please call the command with --parameters or --services.' );
            return;

        }

        try {

            $parameters = $dumpParameters ? $this->inspector->resolveParameters() : [];
            $services = $dumpServices ? $this->inspector->resolveServices() : [];

            if ( $dumpParameters && 0 === count( $parameters ) ) {

                $ui->warning( 'The magic_services.aware.parameters configuration matches no parameters, nothing' .
                    ' can be detected.' );

            }

            if ( $dumpServices && 0 === count( $services ) ) {

                $ui->warning( 'The magic_services.aware.services configuration matches no services, nothing' .
                    ' can be detected.' );

            }

            if ( $dumpParameters && 0 < count( $parameters ) ) {

                $this->listObjects( $ui, 'Parameters', $parameters );

            }

            if ( $dumpServices && 0 < count( $services ) ) {

                $this->listObjects( $ui, 'Services', $services );

            }

        } catch ( \RuntimeException $e ) {

            $ui->error( $e->getMessage() );

        }

    }

    /**
     * Dump constructor.
     *
     * @param Inspector $inspector
     * @param AwareGenerator $awareGenerator
     */
    public function __construct( Inspector $inspector, AwareGenerator $awareGenerator ) {

        parent::__construct( 'services:aware:dump' );

        $this->inspector = $inspector;
        $this->awareGenerator = $awareGenerator;

    }

}
