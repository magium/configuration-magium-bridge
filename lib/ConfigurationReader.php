<?php

namespace Magium\ConfigurationBridge;

use Magium\Configuration\Config\BuilderInterface;
use Magium\Configuration\Config\ConfigurationRepository;
use Magium\Configuration\Manager\ManagerInterface;
use Magium\Util\Configuration\ConfigurableObjectInterface;
use Magium\Util\Configuration\ConfigurationCollector\Property;

class ConfigurationReader extends \Magium\Util\Configuration\ConfigurationReader
{

    protected $manager;
    protected $builder;
    protected $context = ConfigurationRepository::CONTEXT_DEFAULT;

    public function __construct(ManagerInterface $manager, BuilderInterface $builder)
    {
        $this->manager = $manager;
        $this->builder = $builder;
    }

    public function getConfiguration()
    {
        return $this->manager->getConfiguration($this->context);
    }

    public function configure(ConfigurableObjectInterface $config)
    {

        $configuration = $this->getConfiguration();
        $options = $config->getDeclaredOptions();
        $keyBase = get_class($config);
        $keyBase = str_replace('\\', '_', $keyBase);
        foreach ($options as $param) {
            if ($param instanceof Property) {
                $param = $param->getName();
            }
            $path = sprintf('magium/selenium/%s_%s', $keyBase, $param);

            if ($configuration->hasValue($path)) {
                $value = $configuration->getValue($path);
                $config->set($param, $value);
            }
        }
    }

}
