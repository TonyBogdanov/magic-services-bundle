<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\Command\Aware;

use Symfony\Bundle\FrameworkBundle\Command\BuildDebugContainerTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TonyBogdanov\MagicServices\Annotation\MagicService;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator\AwareGeneratorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\AwareGenerator\AwareGeneratorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareInterface;
use TonyBogdanov\MagicServices\DependencyInjection\Aware\Inspector\InspectorAwareTrait;
use TonyBogdanov\MagicServices\DependencyInjection\Singleton\ContainerBuilderSingleton;
use TonyBogdanov\MagicServices\Object\AwareObject;
use TonyBogdanov\MagicServices\Util\Normalizer;

/**
 * Class Dump
 *
 * @package TonyBogdanov\MagicServices\Command\Aware
 *
 * @MagicService(tags={"console.command"})
 */
class Dump extends Command implements
    InspectorAwareInterface,
    AwareGeneratorAwareInterface
{

    use BuildDebugContainerTrait;
    use InspectorAwareTrait;
    use AwareGeneratorAwareTrait;

    /**
     * @return string|null
     */
    public static function getDefaultName() {

        return 'services:aware:dump';

    }

    /**
     * @param SymfonyStyle $ui
     * @param string $title
     * @param array $objects
     *
     * @return $this
     */
    protected function listObjects( SymfonyStyle $ui, string $title, array $objects ) {

        $padding = array_map( function ( AwareObject $object ): int {

            return strlen( $object->getName() );

        }, $objects );

        $padding = 1 < count( $padding ) ? max( ...$padding ) : $padding[0];

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

                ( $this->getAwareGenerator()->isInterfaceExist( $object ) ? '<info>E</info>' : '<error>M</error>' ) .
                ( $this->getAwareGenerator()->isTraitExist( $object ) ? '<info>E</info>' : '<error>M</error>' ),

            ];

        }, $objects ) );

        return $this;

    }

    protected function configure() {

        $this
            ->setDescription( 'Dumps a list of detected <comment>aware</comment> objects based on the configured' .
                ' parameters & services.' )
            ->addOption( 'parameters', 'p', InputOption::VALUE_NONE, 'Dump parameters.' )
            ->addOption( 'tags', 't', InputOption::VALUE_NONE, 'Dump tags.' )
            ->addOption( 'services', 's', InputOption::VALUE_NONE, 'Dump services.' );

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

        $dumpParameters = $input->getOption( 'parameters' );
        $dumpTags = $input->getOption( 'tags' );
        $dumpServices = $input->getOption( 'services' );

        if ( ! $dumpParameters && ! $dumpTags && ! $dumpServices ) {

            $ui->warning( 'Nothing to dump. Please call the command with --parameters, --tags or --services.' );
            return 0;

        }

        $dumpParameters && $ui->writeln( 'Scanning aware parameters' );
        $parameters = $dumpParameters ? $this->getInspector()->resolveAwareParameters() : [];

        $dumpTags && $ui->writeln( 'Scanning aware tags' );
        $tags = $dumpTags ? $this->getInspector()->resolveAwareTags() : [];

        $dumpServices && $ui->writeln( 'Scanning aware services' );
        $services = $dumpServices ? $this->getInspector()->resolveAwareServices() : [];

        if ( $dumpParameters && 0 === count( $parameters ) ) {

            $ui->warning( 'The magic_services.aware.parameters configuration matches no parameters, nothing' .
                ' can be detected.' );

        }

        if ( $dumpTags && 0 === count( $tags ) ) {

            $ui->warning( 'The magic_services.aware.tags configuration matches no tags, nothing can be detected.' );

        }

        if ( $dumpServices && 0 === count( $services ) ) {

            $ui->warning( 'The magic_services.aware.services configuration matches no services, nothing' .
                ' can be detected.' );

        }

        if ( $dumpParameters && 0 < count( $parameters ) ) {

            $this->listObjects( $ui, 'Parameters', $parameters );

        }

        if ( $dumpTags && 0 < count( $tags ) ) {

            $this->listObjects( $ui, 'Tags', $tags );

        }

        if ( $dumpServices && 0 < count( $services ) ) {

            $this->listObjects( $ui, 'Services', $services );

        }

        return 0;

    }

    /**
     * Dump constructor.
     */
    public function __construct() {

        parent::__construct();

    }

}
