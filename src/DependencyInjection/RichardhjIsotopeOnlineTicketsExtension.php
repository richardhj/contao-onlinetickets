<?php

namespace Richardhj\IsotopeOnlineTicketsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the Bundle extension.
 */
class RichardhjIsotopeOnlineTicketsExtension extends Extension
{

    /**
     * The files to load
     *
     * @var array
     */
    private $files = [
        'security.yml',
        'services.yml',
    ];

    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception If something went wrong.
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach (static::$files as $file) {
            $loader->load($file);
        }
    }
}
