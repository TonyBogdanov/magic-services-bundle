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
 * Class Generate
 *
 * @package TonyBogdanov\MagicServices\Command\Aware
 */
class Generate extends Command {

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
     * @param string $type
     * @param AwareObject[] $objects
     *
     * @return $this
     */
    protected function generate( SymfonyStyle $ui, string $type, array $objects ) {

        $ui->writeln( sprintf( 'Generating <info>%1$d</info> %2$s.', count( $objects ), $type ) );
        $ui->progressStart( count( $objects ) );

        foreach ( $objects as $object ) {

            $this->awareGenerator->generateInterface( $object );
            $this->awareGenerator->generateTrait( $object );

        }

        $ui->progressFinish();
        return $this;

    }

    protected function configure() {

        $this->setDescription( 'Generates interfaces & traits for detected *aware* objects based on the configured' .
            ' parameters & services.' )
            ->addOption( 'parameters', 'p', InputOption::VALUE_NONE, 'Generate for parameters.' )
            ->addOption( 'services', 's', InputOption::VALUE_NONE, 'Generate for services.' );;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute( InputInterface $input, OutputInterface $output ) {

        $ui = new SymfonyStyle( $input, $output );

        $generateParameters = $input->getOption( 'parameters' );
        $generateServices = $input->getOption( 'services' );

        if ( ! $generateParameters && ! $generateServices ) {

            $ui->error( 'Nothing to generate. Please call the command with --parameters or --services.' );
            return;

        }

        try {

            $parameters = $generateParameters ? $this->inspector->resolveParameters() : [];
            $services = $generateServices ? $this->inspector->resolveServices() : [];

            if ( $generateParameters && 0 === count( $parameters ) ) {

                $ui->warning( 'The magic_services.aware.parameters configuration matches no parameters, nothing' .
                    ' will be generated.' );

            }

            if ( $generateServices && 0 === count( $services ) ) {

                $ui->warning( 'The magic_services.aware.services configuration matches no services, nothing' .
                    ' will be generated.' );

            }

            if ( $generateParameters && 0 < count( $parameters ) ) {

                $this->generate( $ui, 'parameters', $parameters );

            }

            if ( $generateServices && 0 < count( $services ) ) {

                $this->generate( $ui, 'services', $services );

            }

        } catch ( \RuntimeException $e ) {

            $ui->error( $e->getMessage() );

        }

    }

    /**
     * Generate constructor.
     *
     * @param Inspector $inspector
     * @param AwareGenerator $awareGenerator
     */
    public function __construct( Inspector $inspector, AwareGenerator $awareGenerator ) {

        parent::__construct( 'services:aware:generate' );

        $this->inspector = $inspector;
        $this->awareGenerator = $awareGenerator;

    }

}
