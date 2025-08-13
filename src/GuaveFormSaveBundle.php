<?php

declare(strict_types=1);

namespace Guave\FormSaveBundle;

use Guave\FormSaveBundle\Config\Config;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class GuaveFormSaveBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $builder->getDefinition(Config::class)->setArgument(0, $config);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('quote')->defaultValue('"')->end()
            ->scalarNode('separator')->defaultValue(',')->end()
            ->end()
        ;
    }
}
