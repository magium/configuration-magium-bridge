<?php

namespace Magium\ConfigurationBridge\Tests;

use Magium\AbstractTestCase;
use Magium\ConfigurationBridge\ConfigurationProvider;
use Magium\TestCase\Initializer;
use Magium\Util\Configuration\ConfigurationProviderInterface;

class RegistrationTest extends AbstractTestCase
{

    public function testRegistrationProperlyWiresInitializerDi()
    {
        $preference = Initializer::getInitializationDependencyInjectionContainer()->get(
            ConfigurationProviderInterface::class
        );
        self::assertInstanceOf(ConfigurationProvider::class, $preference);

        $preference = $this->get(ConfigurationProviderInterface::class);
        self::assertInstanceOf(ConfigurationProvider::class, $preference);
    }
}
