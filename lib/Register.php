<?php

namespace Magium\ConfigurationBridge;

use Magium\AbstractTestCase;
use Magium\Configuration\MagiumConfigurationFactory;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\ConfigurationBridge\ConfigurationProvider;
use Magium\Util\Configuration\ConfigurationProviderInterface;
use Magium\Util\Configuration\ConfigurationReader;
use Magium\Util\TestCase\RegistrationCallbackInterface;

class Register
{

    public function register(AbstractTestCase $testCase, MagiumConfigurationFactoryInterface $factory)
    {

        $testCase->setTypePreference(
            MagiumConfigurationFactoryInterface::class,
            get_class($factory)
        );

        $testCase->getDi()->instanceManager()->addSharedInstance($factory, get_class($factory));
        $testCase->setTypePreference(
            ConfigurationProviderInterface::class,
            ConfigurationProvider::class
        );
        $provider = $testCase->get(ConfigurationProvider::class);
        if ($provider instanceof ConfigurationProvider) {
            $provider->configureDi($testCase->getDi());
        }
    }

}
