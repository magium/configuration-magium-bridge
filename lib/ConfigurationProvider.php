<?php

namespace Magium\ConfigurationBridge;

use Magium\Util\Configuration\ClassConfigurationReader;
use Magium\Util\Configuration\EnvironmentConfigurationReader;
use Magium\Util\Configuration\StandardConfigurationProvider;

class ConfigurationProvider extends StandardConfigurationProvider
{

    public function __construct(
        ConfigurationReader $configurationReader,
        ClassConfigurationReader $classConfigurationReader,
        EnvironmentConfigurationReader
        $environmentConfigurationReader,
        $configurationDir = null)
    {
        parent::__construct(
            $configurationReader,
            $classConfigurationReader,
            $environmentConfigurationReader,
            $configurationDir
        );
    }

}
