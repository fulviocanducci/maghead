<?php

namespace Maghead\Sharding;

use Maghead\Manager\DataSourceManager;

class Chunk
{
    public $index;

    public $from;

    public $shardId;

    private $dataSourceManager;

    /**
     * @param nubmer $index the chunk index
     * @param nubmer $from the index starts from. This number is less than $index.
     * @param string $shard the shard ID
     */
    public function __construct($index, $from, $shardId, DataSourceManager $dataSourceManager)
    {
        $this->index  = $index;
        $this->from   = $from;
        $this->shardId  = $shardId;
        $this->dataSourceManager = $dataSourceManager;
    }

    /**
     * @param number $index hashed index
     */
    public function contains($index)
    {
        // echo "key($k) -> index($index): {$this->from} < {$index} && {$index} <= {$this->index} \n";
        return $this->from < $index && $index <= $this->index;
    }

    public function loadShard()
    {
        return new Shard($this->shardId, $this->dataSourceManager);
    }

    /**
     * Returns the shard ID
     */
    public function getShardId()
    {
        return $this->shardId;
    }
}
