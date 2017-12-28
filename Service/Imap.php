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
 * The main service for the bundle. Returns connection objects.
 */

namespace SliteSystems\ImapBundle\Service;

use Ddeboer\Imap\Server;
use Ddeboer\Imap\Connection;
use Ddeboer\Imap\Mailbox;

/**
 * The IMAP service.
 */
class Imap
{
    /**
     * The connection configuration data.
     * 
     * @see \SliteSystems\ImapBundle\DependencyInjection\Configuration
     * 
     * @var array
     */
    protected $config;
    
    /**
     * The estabished connections, keyed by config name,
     * 
     * @var \Ddeboer\Imap\Connection[]
     */
    protected $connections;
    
    /**
     * Constructor
     * 
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * Return the connection for the given configuration name,
     * 
     * @param string $name
     *   - The name of the connection.
     *
     * @return \Ddeboer\Imap\Connection
     * 
     * @throws \Exception
     */
    public function get($name)
    {
        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }
        // else
        if (!isset($this->config[$name]) || empty($this->config[$name])) {
            throw new \InvalidArgumentException($name.' must be defined'); 
        }
        
        $config = $this->config[$name];
        
        $server = new Server($config['hostname'], $config['port'], $config['flags']);
        // Throws an exception on failure.
        $connection = $server->authenticate($config['username'], $config['password']);
    
        $this->connections[$name] = $connection;
        
        return $connection;
    }
    
    /**
     * Return a mailbox for a connection.
     * 
     * @param string $name
     *   - The name of the connection.
     * @param string $mailbox
     *   - The name of the mailbox. Defaults to the value in the configuration.
     * 
     * @return \Ddeboer\Imap\Mailbox
     * 
     * @throws \Exception
     */
    public function getMailBox($name, $mailbox = NULL)
    {
        $connection = $this->get($name);

        
        if (empty($mailbox)) {
            // To have got this far this must exist.
            $config = $this->config[$name];
            
            $mailbox = $config['mailbox'];
        }
        
        return $connection->getMailbox($mailbox);
    }
    
    /**
     * Closes a connection and removes the cached entry.
     * 
     * @param string $name
     *   - The name of the connection.
     * 
     * @return bool
     */
    public function close($name)
    {
        $retval = FALSE;
        
        if (isset($this->connections[$name]))  {
            $retval = $this->connections[$name]->close();
            unset ($this->connections[$name]);
        }
        
        return $retval;
    }
}