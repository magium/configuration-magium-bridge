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

}
