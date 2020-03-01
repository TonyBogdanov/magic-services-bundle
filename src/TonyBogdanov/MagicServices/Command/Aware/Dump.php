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
use TonyBogdanov\MagicServices\Util\Normalizer;

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

        $padding = max( ...array_map( function ( AwareObject $object ): int {

            return strlen( $object->getName() );

        }, $objects ) );

        $ui->title( $title );
        $ui->table( [

            str_pad( 'Name', $padding ) . ' ' .
            str_pad( 'Property', $padding, ' ', STR_PAD_LEFT ),
            'Type',
            'Dependency',
            'IT',

        ], array_map( function ( AwareObject $object ) use ( $padding ): array {

            return [

                str_pad( $object->getName(), $padding ) . ' ' .
                str_pad( Normalizer::normalizeParameterName( $object->getName() ), $padding, ' ', STR_PAD_LEFT ),
                $object->getType(),
                $object->getDependency(),

                ( $this->awareGenerator->isInterfaceExist( $object ) ? '<info>E</info>' : '<error>M</error>' ) .
                ( $this->awareGenerator->isTraitExist( $object ) ? '<info>E</info>' : '<error>M</error>' ),

            ];

        }, $objects ) );

        return $this;

    }

    protected function configure() {

        $this
            ->setDescription( 'Dumps a list of detected <comment>aware</comment> objects based on the configured' .
                ' parameters & services.' )
            ->addOption( 'parameters', 'p', InputOption::VALUE_NONE, 'Dump parameters.' )
            ->addOption( 'services', 's', InputOption::VALUE_NONE, 'Dump services.' );

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute( InputInterface $input, OutputInterface $output ): int {

        $ui = new SymfonyStyle( $input, $output );

        $dumpParameters = $input->getOption( 'parameters' );
        $dumpServices = $input->getOption( 'services' );

        if ( ! $dumpParameters && ! $dumpServices ) {

            $ui->warning( 'Nothing to dump. Please call the command with --parameters or --services.' );
            return 1;

        }

        $dumpParameters && $ui->writeln( 'Scanning aware parameters' );
        $parameters = $dumpParameters ? $this->inspector->resolveAwareParameters() : [];

        $dumpServices && $ui->writeln( 'Scanning aware services' );
        $services = $dumpServices ? $this->inspector->resolveAwareServices() : [];

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

        return 0;

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
