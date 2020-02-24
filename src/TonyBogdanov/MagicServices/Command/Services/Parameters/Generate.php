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
use TonyBogdanov\MagicServices\Util\Generator;

/**
 * Class Generate
 *
 * @package TonyBogdanov\MagicServices\Command\Services\Parameters
 */
class Generate extends Command {

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var ParameterInspector
     */
    protected $parameterInspector;

    protected function configure() {

        $this->setDescription( "Generates aware interfaces for container parameters matching the configured" .
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

        $ui->progressStart( count( $parameters ) );

        foreach ( $parameters as $parameter ) {

            $this->generator->dumpInterface( $parameter->getInterface() );
            $ui->progressAdvance();

        }

        $ui->progressFinish();

    }

    /**
     * Generate constructor.
     *
     * @param Generator $generator
     * @param ParameterInspector $parameterInspector
     */
    public function __construct( Generator $generator, ParameterInspector $parameterInspector ) {

        parent::__construct( 'services:parameters:generate' );

        $this->generator = $generator;
        $this->parameterInspector = $parameterInspector;

    }

}
