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
use TonyBogdanov\MagicServices\Annotation\MagicService;
use TonyBogdanov\MagicServices\Command\Traits\DebugContainerTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator\AwareGeneratorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator\AwareGeneratorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Singleton\ContainerBuilderSingleton;
use TonyBogdanov\MagicServices\Object\AwareObject;

/**
 * Class Generate
 *
 * @package TonyBogdanov\MagicServices\Command\Aware
 *
 * @MagicService(tags={"console.command"})
 */
class Generate extends Command implements
    InspectorAwareInterface,
    AwareGeneratorAwareInterface
{

    use DebugContainerTrait;
    use InspectorAwareTrait;
    use AwareGeneratorAwareTrait;

    /**
     * @return string|null
     */
    public static function getDefaultName() {

        return 'services:aware:generate';

    }

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

            $this->getAwareGenerator()->generateInterface( $object );
            $this->getAwareGenerator()->generateTrait( $object );

        }

        $ui->progressFinish();
        return $this;

    }

    protected function configure() {

        $this->setDescription( 'Generates interfaces & traits for detected <comment>aware</comment> objects based' .
            ' on the configured parameters & services.' )
             ->addOption( 'parameters', 'p', InputOption::VALUE_NONE, 'Generate for parameters.' )
             ->addOption( 'tags', 't', InputOption::VALUE_NONE, 'Generate for tags.' )
             ->addOption( 'services', 's', InputOption::VALUE_NONE, 'Generate for services.' );;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute( InputInterface $input, OutputInterface $output ): int {

        ContainerBuilderSingleton::setContainerBuilder( $this->getContainerBuilder() );

        $ui = new SymfonyStyle( $input, $output );

        $generateParameters = $input->getOption( 'parameters' );
        $generateTags = $input->getOption( 'tags' );
        $generateServices = $input->getOption( 'services' );

        if ( ! $generateParameters && ! $generateTags && ! $generateServices ) {

            $ui->error( 'Nothing to generate. Please call the command with --parameters, --tags or --services.' );
            return 1;

        }

        $generateParameters && $ui->writeln( 'Scanning aware parameters' );
        $parameters = $generateParameters ? $this->getInspector()->resolveAwareParameters() : [];

        $generateTags && $ui->writeln( 'Scanning aware tags' );
        $tags = $generateTags ? $this->getInspector()->resolveAwareTags() : [];

        $generateServices && $ui->writeln( 'Scanning aware services' );
        $services = $generateServices ? $this->getInspector()->resolveAwareServices() : [];

        if ( $generateParameters && 0 === count( $parameters ) ) {

            $ui->warning( 'The magic_services.aware.parameters configuration matches no parameters, nothing' .
                ' will be generated.' );

        }

        if ( $generateTags && 0 === count( $tags ) ) {

            $ui->warning( 'The magic_services.aware.tags configuration matches no tags, nothing will be generated.' );

        }

        if ( $generateServices && 0 === count( $services ) ) {

            $ui->warning( 'The magic_services.aware.services configuration matches no services, nothing' .
                ' will be generated.' );

        }

        if ( $generateParameters && 0 < count( $parameters ) ) {

            $this->generate( $ui, 'parameters', $parameters );

        }

        if ( $generateTags && 0 < count( $tags ) ) {

            $this->generate( $ui, 'tags', $tags );

        }

        if ( $generateServices && 0 < count( $services ) ) {

            $this->generate( $ui, 'services', $services );

        }

        return 0;

    }

    /**
     * Generate constructor.
     */
    public function __construct() {

        parent::__construct();

    }

}
