<?php

namespace Magium\ConfigurationBridge\Tests;

use Magium\AbstractTestCase;
use Magium\Configuration\Config\ConfigInterface;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\Configuration\Manager\ManagerInterface;
use Magium\ConfigurationBridge\ConfigurationProvider;
use Magium\ConfigurationBridge\ConfigurationProvider as BridgeConfigurationProvider;
use Magium\ConfigurationBridge\Register;
use Magium\TestCase\Initializer;
use Magium\Util\Configuration\ConfigurationProviderInterface;
use Magium\Util\Configuration\ConfigurationReader;
use PHPUnit\Framework\TestCase;

class BasicTest extends AbstractTestCase
{

    public function testGetBridgeReader()
    {
        $factory = $this->createMock(MagiumConfigurationFactoryInterface::class);
        (new Register())->register($this, $factory);
        // Does the switcheroo happen?
        $instance = $this->get(ConfigurationProviderInterface::class);
        self::assertInstanceOf(BridgeConfigurationProvider::class, $instance);
    }

    public function testReader()
    {
        $config = $this->createMock(ConfigInterface::class);
        $config->expects(self::once())->method('getValue')->with(
            self::equalTo('magium/selenium/Magium_ConfigurationBridge_Tests_Element_test')
        )->willReturn('result');

        $manager = $this->createMock(ManagerInterface::class);
        $manager->expects(self::once())->method('getConfiguration')->willReturn($config);
        $factory = $this->createMock(MagiumConfigurationFactoryInterface::class);
        $factory->expects(self::once())->method('getManager')->willReturn($manager);
        (new Register())->register($this, $factory);

        $instance = $this->get(Element::class);
        self::assertInstanceOf(Element::class, $instance);
        self::assertEquals('result', $instance->test);
    }

}
