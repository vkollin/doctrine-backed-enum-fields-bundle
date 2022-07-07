<?php declare(strict_types=1);

namespace VKollin\Doctrine\BackedEnumFields\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @codeCoverageIgnore
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('doctrine_backed_enum_fields');
        $treeBuilder->getRootNode()
                    ->children()
                    ->arrayNode('enum_types')
                    ->useAttributeAsKey('enum_class')
                    ->scalarPrototype()
                    ->end();

        return $treeBuilder;
    }
}
