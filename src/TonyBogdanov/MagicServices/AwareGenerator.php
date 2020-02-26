<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices;

use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Filesystem\Filesystem;
use TonyBogdanov\MagicServices\Object\AwareObject;

/**
 * Class AwareGenerator
 *
 * @package TonyBogdanov\MagicServices
 */
class AwareGenerator {

    /**
     * @var CodeGenerator
     */
    protected $codeGenerator;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * AwareGenerator constructor.
     *
     * @param CodeGenerator $codeGenerator
     * @param string $path
     * @param string $namespace
     */
    public function __construct( CodeGenerator $codeGenerator, string $path, string $namespace ) {

        $this->codeGenerator = $codeGenerator;

        $this->path = $path;
        $this->namespace = $namespace;

    }

    /**
     * @param AwareObject $object
     *
     * @return string
     */
    public function getNamespaceName( AwareObject $object ): string {

        return $this->namespace . '\\' . $object->getName();

    }

    /**
     * @param AwareObject $object
     *
     * @return string
     */
    public function getInterfaceClassName( AwareObject $object ): string {

        return $this->namespace . '\\' . $object->getName() . '\\' . $object->getName() . 'AwareInterface';

    }

    /**
     * @param AwareObject $object
     *
     * @return string
     */
    public function getTraitClassName( AwareObject $object ): string {

        return $this->namespace . '\\' . $object->getName() . '\\' . $object->getName() . 'AwareTrait';

    }

    /**
     * @param AwareObject $object
     *
     * @return bool
     */
    public function isInterfaceExist( AwareObject $object ): bool {

        return interface_exists( $this->getInterfaceClassName( $object ) );

    }

    /**
     * @param AwareObject $object
     *
     * @return bool
     */
    public function isTraitExist( AwareObject $object ): bool {

        return trait_exists( $this->getTraitClassName( $object ) );

    }

    /**
     * @param AwareObject $object
     *
     * @return string
     */
    public function getInterfacePath( AwareObject $object ): string {

        return
            $this->path . DIRECTORY_SEPARATOR .
            $object->getName() . DIRECTORY_SEPARATOR .
            $object->getName() . 'AwareInterface.php';

    }


    /**
     * @param AwareObject $object
     *
     * @return string
     */
    public function getTraitPath( AwareObject $object ): string {

        return
            $this->path . DIRECTORY_SEPARATOR .
            $object->getName() . DIRECTORY_SEPARATOR .
            $object->getName() . 'AwareTrait.php';

    }

    /**
     * @param AwareObject $object
     *
     * @return $this
     */
    public function generateInterface( AwareObject $object ) {

        $printer = new PsrPrinter();
        $file = $this->codeGenerator->generate(

            true,
            $this->getInterfaceClassName( $object ),
            $object->getName(),
            $object->getType()

        );

        ( new Filesystem() )->dumpFile( $this->getInterfacePath( $object ), $printer->printFile( $file ) );
        return $this;

    }

    /**
     * @param AwareObject $object
     *
     * @return $this
     */
    public function generateTrait( AwareObject $object ) {

        $printer = new PsrPrinter();
        $file = $this->codeGenerator->generate(

            false,
            $this->getTraitClassName( $object ),
            $object->getName(),
            $object->getType()

        );

        ( new Filesystem() )->dumpFile( $this->getTraitPath( $object ), $printer->printFile( $file ) );
        return $this;

    }

}
