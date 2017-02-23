<?php

namespace Magium\ConfigurationBridge\Tests;

use Magium\AbstractTestCase;
use Magium\Configuration\Config\Builder;
use Magium\Configuration\Config\BuilderInterface;
use Magium\Configuration\Config\ConfigInterface;
use Magium\Configuration\MagiumConfigurationFactoryInterface;
use Magium\Configuration\Manager\Manager;
use Magium\Configuration\Manager\ManagerInterface;
use Magium\ConfigurationBridge\ConfigurationReader;
use Magium\ConfigurationBridge\Register;
use Magium\TestCase\Initializer;
use Magium\Util\Configuration\ConfigurationCollector\Property;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{

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
        $configuration = new \Magium\Configuration\Config\Config('<config />');
        $builder = $this->createMock(Builder::class);
        $manager = $this->createMock(Manager::class);
        $manager->expects(self::once())->method('getConfiguration')->willReturn($configuration);

        $reader = new ConfigurationReader(
            $manager,
            $builder
        );

        $instance = $reader->getConfiguration();
        self::assertInstanceOf(\Magium\Configuration\Config\Config::class, $instance);

    }

    public function testRegisterProperlyWiresDiForTheConfigurationReader()
    {
        $testCase = $this->getMockBuilder(AbstractTestCase::class)->setMethods(null)->getMock();
        $manager = $this->createMock(ManagerInterface::class);
        $builder = $this->createMock(BuilderInterface::class);
        $factory = $this->createMock(MagiumConfigurationFactoryInterface::class);
        $factory->expects(self::once())->method('getManager')->willReturn($manager);
        $factory->expects(self::once())->method('getBuilder')->willReturn($builder);

        $initializer = new Initializer();
        $initializer->initialize($testCase);

        (new Register())->register($testCase, $factory);
        $configurationReader = $testCase->get(\Magium\Util\Configuration\ConfigurationReader::class);

        // It is important to get the configuration configuration reader when requesting the original.
        self::assertInstanceOf(ConfigurationReader::class, $configurationReader);
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
        $config = new \Magium\Configuration\Config\Config(<<<XML
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
