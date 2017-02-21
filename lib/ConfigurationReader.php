<?php

namespace Magium\ConfigurationBridge;

use Magium\Configuration\Config\Config;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\Util\Configuration\ConfigurableObjectInterface;
use Magium\Util\Configuration\ConfigurationCollector\Property;

class ConfigurationReader extends \Magium\Util\Configuration\ConfigurationReader
{

    protected $factory;
    protected $context = Config::CONTEXT_DEFAULT;

    public function __construct(MagiumConfigurationFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function configure(ConfigurableObjectInterface $config)
    {
        $manager = $this->factory->getManager();
        $configuration = $manager->getConfiguration($this->context);
        $options = $config->getDeclaredOptions();
        $keyBase = get_class($config);
        $keyBase = str_replace('\\', '_', $keyBase);
        foreach ($options as $param) {
            if ($param instanceof Property) {
                $param = $param->getName();
            }
            $path = sprintf('magium/selenium/%s_%s', $keyBase, $param);
            $value = $configuration->getValue($path);
            $config->set($param, $value);
        }
    }

}
