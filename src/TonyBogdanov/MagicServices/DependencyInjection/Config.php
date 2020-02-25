<?php

/**
 * Copyright (c) Tony Bogdanov <support@tonybogdanov.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TonyBogdanov\MagicServices\DependencyInjection;

/**
 * Class Config
 *
 * @package TonyBogdanov\MagicServices\DependencyInjection
 */
class Config {

    /**
     * @var string
     */
    protected $awarePath;

    /**
     * @var string
     */
    protected $awareNamespace;

    /**
     * @var string
     */
    protected $configPath;

    /**
     * @var string[]
     */
    protected $parameters = [];

    /**
     * @var string[]
     */
    protected $interfaces = [];

    /**
     * @return string
     */
    public function getAwarePath(): string {

        return $this->awarePath;

    }

    /**
     * @param string $awarePath
     *
     * @return Config
     */
    public function setAwarePath( string $awarePath ): Config {

        $this->awarePath = $awarePath;
        return $this;

    }

    /**
     * @return string
     */
    public function getAwareNamespace(): string {

        return $this->awareNamespace;

    }

    /**
     * @param string $awareNamespace
     *
     * @return Config
     */
    public function setAwareNamespace( string $awareNamespace ): Config {

        $this->awareNamespace = $awareNamespace;
        return $this;

    }

    /**
     * @return string
     */
    public function getConfigPath(): string {

        return $this->configPath;

    }

    /**
     * @param string $configPath
     *
     * @return Config
     */
    public function setConfigPath( string $configPath ): Config {

        $this->configPath = $configPath;
        return $this;

    }

    /**
     * @return string[]
     */
    public function getParameters(): array {

        return $this->parameters;

    }

    /**
     * @param string[] $parameters
     *
     * @return Config
     */
    public function setParameters( array $parameters ): Config {

        $this->parameters = $parameters;
        return $this;

    }

    /**
     * @return string[]
     */
    public function getInterfaces(): array {

        return $this->interfaces;

    }

    /**
     * @param string[] $interfaces
     *
     * @return Config
     */
    public function setInterfaces( array $interfaces ): Config {

        $this->interfaces = $interfaces;
        return $this;

    }

}
