<?php

namespace Magium\ConfigurationBridge\Tests;

use Magium\Configuration\Config\Builder;
use Magium\Configuration\Config\ConfigInterface;
use Magium\Configuration\Config\ConfigurationRepository;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\Configuration\Manager\Manager;
use Magium\ConfigurationBridge\ConfigurationProvider as BridgeConfigurationProvider;
use Magium\ConfigurationBridge\ConfigurationReader;
use Magium\ConfigurationBridge\Register;
use Magium\Util\Configuration\ConfigurationCollector\Property;
use Magium\Util\Configuration\ConfigurationProviderInterface;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{

    public function testGetBridgeReader()
    {
        (new Register())->register();
        $factory = $this->createMock(MagiumConfigurationFactoryInterface::class);
        $test = new NotATestObject('testVoid');

        $builder = $this->createMock(Builder::class);
        $manager = $this->createMock(Manager::class);
        $manager->method('getConfiguration')->willReturn($this->createMock(ConfigInterface::class));
        $factory->method('getBuilder')->willReturn($builder);
        $factory->method('getManager')->willReturn($manager);
        $test->runBare();

        // Does the switcheroo happen?  (from Register)
        $instance = $test->get(ConfigurationProviderInterface::class);
        self::assertInstanceOf(BridgeConfigurationProvider::class, $instance);
    }

    public function testNonElementIsNotMatched()
    {
        $config = $this->createMock(ConfigInterface::class);

        $configurationReader = $this->getMockBuilder(ConfigurationReader::class)->disableOriginalConstructor()->setMethods([
            'getConfiguration'
        ])->getMock();

        $configurationReader->expects(self::once())->method('getConfiguration')->willReturn($config);

        $instance = $this->getMockBuilder(Element::class)->disableOriginalConstructor()->setMethods([
            'getDeclaredOptions'
        ])->getMock();
        $instance->expects(self::once())->method('getDeclaredOptions')->willReturn([new Property('test', 'value')]);
        $config->expects(self::never())->method('getValue');

        $configurationReader->configure($instance);

        self::assertInstanceOf(Element::class, $instance);
        self::assertEquals(null, $instance->test);
    }

    public function testConfigurationGetter()
    {
        $configuration = new ConfigurationRepository('<config />');
        $builder = $this->createMock(Builder::class);
        $manager = $this->createMock(Manager::class);
        $manager->expects(self::once())->method('getConfiguration')->willReturn($configuration);

        $reader = new ConfigurationReader(
            $manager,
            $builder
        );

        $instance = $reader->getConfiguration();
        self::assertInstanceOf(ConfigurationRepository::class, $instance);
    }

    public function testReader()
    {

        $configurationReader = $this->getMockBuilder(ConfigurationReader::class)->disableOriginalConstructor()->setMethods([
            'getConfiguration'
        ])->getMock();

        $instance = $this->getMockBuilder(Element::class)->disableOriginalConstructor()->setMethods([
            'getDeclaredOptions'
        ])->getMock();
        $instanceType = get_class($instance);
        $instance->expects(self::once())->method('getDeclaredOptions')->willReturn([new Property('test', 'value')]);
        $config = new ConfigurationRepository(<<<XML
<config>
    <magium><selenium><{$instanceType}_test>result</{$instanceType}_test></selenium></magium>
</config>
XML
);

        $configurationReader->expects(self::once())->method('getConfiguration')->willReturn($config);

        $configurationReader->configure($instance);

        self::assertInstanceOf(Element::class, $instance);
        self::assertEquals('result', $instance->test);
    }

}
