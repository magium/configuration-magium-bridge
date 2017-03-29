<?php

namespace Magium\ConfigurationBridge;

use Magium\AbstractTestCase;
use Magium\Configuration\Config\BuilderInterface;
use Magium\Configuration\MagiumConfigurationFactory;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\Configuration\Manager\ManagerInterface;
use Magium\ConfigurationBridge\ConfigurationProvider;
use Magium\TestCase\Initializer;
use Magium\Util\Configuration\ConfigurationProviderInterface;
use Magium\Util\Configuration\ConfigurationReader;
use Magium\Util\TestCase\RegistrationCallbackInterface;
use Zend\Db\Sql\Predicate\In;
use Zend\Di\Definition\ClassDefinition;

class Register
{

    public function register()
    {
        $initializerDi = Initializer::getInitializationDependencyInjectionContainer();
        $instanceManager = $initializerDi->instanceManager();
        $instanceManager->unsetTypePreferences(Initializer::class);
        $instanceManager->addTypePreference(
            Initializer::class,
            \Magium\ConfigurationBridge\Initializer::class
        );
    }

    public function register1(AbstractTestCase $testCase, MagiumConfigurationFactoryInterface $factory)
    {
        $initializerDi = Initializer::getInitializationDependencyInjectionContainer();
        $instanceManager = $initializerDi->instanceManager();
        if ($instanceManager->hasSharedInstance(MagiumConfigurationFactoryInterface::class)) {
            return;
        }

        $manager = $factory->getManager();
        $builder = $factory->getBuilder();

        $testCase->setTypePreference(
            ConfigurationProviderInterface::class,
            ConfigurationProvider::class
        );

        $instanceManager->addSharedInstance($factory, MagiumConfigurationFactoryInterface::class);
        $instanceManager->addSharedInstance($manager, ManagerInterface::class);
        $instanceManager->addSharedInstance($builder, BuilderInterface::class);

        $instanceManager->unsetTypePreferences(ConfigurationProviderInterface::class);
        $instanceManager->setTypePreference(
            ConfigurationProviderInterface::class,
            [ConfigurationProvider::class]
        );
        $initializer = $initializerDi->get(Initializer::class);
        /** @var $initializer Initializer */
        $initializer->initialize($testCase);

        $di = $testCase->getDi();
        $di->instanceManager()->addSharedInstance($builder, BuilderInterface::class);
        $di->instanceManager()->addSharedInstance($initializerDi->get(ManagerInterface::class), ManagerInterface::class);
        $testCase->setTypePreference(
            MagiumConfigurationFactoryInterface::class,
            get_class($factory)
        );
        $di->instanceManager()->addSharedInstance($factory, get_class($factory));

        $provider = $testCase->get(ConfigurationProvider::class);

        if ($provider instanceof ConfigurationProvider) {
            $provider->configureDi($di);
        }
    }

}
