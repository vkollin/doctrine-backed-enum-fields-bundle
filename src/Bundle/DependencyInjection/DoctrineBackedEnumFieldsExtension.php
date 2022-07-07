<?php declare(strict_types=1);

namespace VKollin\Doctrine\BackedEnumFields\Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use VKollin\Doctrine\BackedEnumFields\EventListener\EnumTypeListener;

/**
 * @codeCoverageIgnore
 */
final class DoctrineBackedEnumFieldsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter('doctrine_backed_enum_fields.enum_types', $config['enum_types']);
        $container->autowire(EnumTypeListener::class)
                  ->addTag('doctrine.event_listener', ['event' => 'postGenerateSchema']);
    }
}
