<?php

$repo = \Magium\Configuration\File\Configuration\ConfigurationFileRepository::getInstance();
$repo->addSecureBase(realpath(__DIR__ . '/etc'));
$repo->registerConfigurationFile(new \Magium\Configuration\File\Configuration\XmlFile(__DIR__ . '/etc/settings.xml'));

(new \Magium\ConfigurationBridge\Register())->register();
