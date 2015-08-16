<?php

namespace Gorka\Blog\Infrastructure\Data\Post;

use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\PostRepository as PostRepositoryInterface;
use Gorka\Blog\Infrastructure\Data\EventStore;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * @param PostId $id
     * @return AggregateHistory
     */
    public function history(PostId $id)
    {
        return $this->eventStore->aggregateHistory($id);
    }

    /**
     * @param AggregateHistory $history
     */
    public function commit(AggregateHistory $history)
    {
        $this->eventStore->commit($history);
    }
}
