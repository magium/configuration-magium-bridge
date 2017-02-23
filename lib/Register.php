<?php

namespace Magium\ConfigurationBridge;

use Magium\AbstractTestCase;
use Magium\Configuration\Config\BuilderInterface;
use Magium\Configuration\MagiumConfigurationFactory;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\Configuration\Manager\ManagerInterface;
use Magium\ConfigurationBridge\ConfigurationProvider;
use Magium\Util\Configuration\ConfigurationProviderInterface;
use Magium\Util\Configuration\ConfigurationReader;
use Magium\Util\TestCase\RegistrationCallbackInterface;
use Zend\Di\Definition\ClassDefinition;

class Register
{

    public function register(AbstractTestCase $testCase, MagiumConfigurationFactoryInterface $factory)
    {
        $di = $testCase->getDi();
        $definitions = $di->definitions();
        $testCase->setTypePreference(
            MagiumConfigurationFactoryInterface::class,
            get_class($factory)
        );
        $di->instanceManager()->addSharedInstance($factory, get_class($factory));

        $builderDefinition = new ClassDefinition(BuilderInterface::class);
        $builderDefinition->setInstantiator([$factory, 'getBuilder']);
        $definitions->addDefinition($builderDefinition);

        $managerDefinition = new ClassDefinition(ManagerInterface::class);
        $managerDefinition->setInstantiator([$factory, 'getManager']);
        $definitions->addDefinition($managerDefinition);

        $testCase->setTypePreference(
            ConfigurationProviderInterface::class,
            ConfigurationProvider::class
        );
        $provider = $testCase->get(ConfigurationProvider::class);
        if ($provider instanceof ConfigurationProvider) {
            $provider->configureDi($di);
        }
    }

}
