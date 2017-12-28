<?php
/**
 * Copyright 2017- Slite Systems Ltd
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @file
 * Defines the configuration structure for the IMAP Bundle.
 */

namespace SliteSystems\ImapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('slite_systems_imap');
        
        $rootNode
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('username')
                                ->isRequired()
                            ->end()
                            ->scalarNode('password')
                                ->isRequired()
                            ->end()
                            ->scalarNode('hostname')
                                ->isRequired()
                            ->end()
                            ->integerNode('port')
                                ->min(1)
                                ->max(65535)
                                ->defaultValue(993)
                            ->end()
                            ->scalarNode('flags')
                                ->info('The flags to pass to the IMAP code')
                                ->defaultValue('/imap/ssl/validate-cert')
                            ->end()
                            ->scalarNode('mailbox')
                                ->info('The initial mailbox to use')
                                ->defaultValue('INBOX')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return treeBuilder;
    }
}