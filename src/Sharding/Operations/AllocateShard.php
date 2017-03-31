<?php

namespace Maghead\Sharding\Operations;

use Maghead\Sharding\ShardDispatcher;
use Maghead\Sharding\ShardMapping;
use Maghead\Sharding\Shard;
use Maghead\Sharding\ShardCollection;
use Maghead\Manager\ConnectionManager;
use Maghead\Config;

/**
 * Given an instance ID:
 * 1. Connect to the instance
 * 2. Create a database
 * 3. Initialize the db schema
 */
class AllocateShard
{
    protected $config;

    protected $connectionManager;

    public function __construct(Config $config, ConnectionManager $connectionManager)
    {
        $this->config = $config;
        $this->connectionManager = $connectionManager;
    }
}
