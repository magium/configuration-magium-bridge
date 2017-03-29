<?php

namespace Magium\ConfigurationBridge;

use Magium\AbstractTestCase;
use Magium\Configuration\Config\BuilderInterface;
use Magium\Configuration\Config\ConfigInterface;
use Magium\Configuration\MagiumConfigurationFactory;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\Configuration\Manager\ManagerInterface;
use Magium\Util\Configuration\ConfigurationProviderInterface;
use Zend\Di\Di;
use Zend\Di\InstanceManager;

class Initializer extends \Magium\TestCase\Initializer
{

    public function setConfigurationProvider(AbstractTestCase $testCase)
    {
        $di = self::getInitializationDependencyInjectionContainer();
        $instanceManager = $di->instanceManager();

        $factory = $this->getFactory($di);
        $builder = $factory->getBuilder();
        $manager = $factory->getManager();
        $config = $manager->getConfiguration();

        $this->setSharedObjects($instanceManager, $factory, $builder, $manager, $config);
        $provider = $di->get(ConfigurationProvider::class);
        $instanceManager->addSharedInstance($provider, ConfigurationProviderInterface::class);

        $instanceManager = $testCase->getDi()->instanceManager();
        $this->setSharedObjects($instanceManager, $factory, $builder, $manager, $config);
        $instanceManager->addSharedInstance($provider, ConfigurationProviderInterface::class);

        $this->configurationProvider->configureDi($testCase->getDi());
    }

    protected function setSharedObjects(
        InstanceManager $instanceManager,
        MagiumConfigurationFactoryInterface $factory,
        BuilderInterface $builder,
        ManagerInterface $manager,
        ConfigInterface $config
    )
    {
        $instanceManager->addSharedInstance($factory, MagiumConfigurationFactoryInterface::class);
        $instanceManager->addSharedInstance($builder, BuilderInterface::class);
        $instanceManager->addSharedInstance($manager, ManagerInterface::class);
        $instanceManager->addSharedInstance($config, ConfigInterface::class);

    }

    /**
     * @param Di $di
     * @return MagiumConfigurationFactoryInterface
     */

    protected function getFactory(Di $di)
    {
        $instanceManager = $di->instanceManager();
        if (!$instanceManager->hasTypePreferences(MagiumConfigurationFactoryInterface::class)) {
            $instanceManager->addTypePreference(
                MagiumConfigurationFactoryInterface::class,
                MagiumConfigurationFactory::class
            );
        }

        $pref = $instanceManager->getTypePreferences(MagiumConfigurationFactoryInterface::class);
        $pref = array_shift($pref);
        return $di->get($pref);
    }

}
