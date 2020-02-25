<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Services\Traits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TonyBogdanov\MagicServices\Inspector\TraitInspector;
use TonyBogdanov\MagicServices\Util\Generator;

/**
 * Class Generate
 *
 * @package TonyBogdanov\MagicServices\Command\Services\Traits
 */
class Generate extends Command {

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var TraitInspector
     */
    protected $traitInspector;

    protected function configure() {

        $this->setDescription( "Generates traits for previously generated aware interfaces." );

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

        $traits = $this->traitInspector->resolveTraits();
        if ( 0 === count( $traits ) ) {

            $ui->error( "There are no generated aware interfaces." );
            return;

        }

        $ui->progressStart( count( $traits ) );

        foreach ( $traits as $trait ) {

            $this->generator->dumpTrait( $trait );
            $ui->progressAdvance();

        }

        $ui->progressFinish();

    }

    /**
     * Generate constructor.
     *
     * @param Generator $generator
     * @param TraitInspector $traitInspector
     */
    public function __construct( Generator $generator, TraitInspector $traitInspector ) {

        parent::__construct( 'services:traits:generate' );

        $this->generator = $generator;
        $this->traitInspector = $traitInspector;

    }

}
