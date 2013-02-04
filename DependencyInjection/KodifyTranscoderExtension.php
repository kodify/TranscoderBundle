<?php

namespace Kodify\TranscoderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KodifyTranscoderExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->validateConfigExists($config, 'driver');

        $driver = strtolower($config['driver']);

        $container->setParameter('kodify_transcoder.service.driver', $config['driver']);

        $this->validateConfigExists($config, "{$driver}_access_key");
        $this->validateConfigExists($config, "{$driver}_secret_key");
        $this->validateConfigExists($config, "{$driver}_api_url");
        $this->validateConfigExists($config, "{$driver}_cloud_id");

        $container->setParameter('kodify_transcoder.service.accessKey', $config["{$driver}_access_key"]);
        $container->setParameter('kodify_transcoder.service.secretKey', $config["{$driver}_secret_key"]);
        $container->setParameter('kodify_transcoder.service.apiUrl', $config["{$driver}_api_url"]);
        $container->setParameter('kodify_transcoder.service.cloudId', $config["{$driver}_cloud_id"]);
    }

    protected function validateConfigExists(array $config, $key)
    {
        if (!isset($config[$key])) {
            throw new InvalidConfigurationException("The '{$key}' option must be set");
        }

    }

}
