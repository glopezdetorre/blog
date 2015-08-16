<?php

namespace Gorka\Blog\Domain\Model\Post;

use Gorka\Blog\Domain\Model\AggregateHistory;

interface PostRepository
{
    /**
     * @param AggregateHistory $history
     */
    public function commit(AggregateHistory $history);

    /**
     * @param PostId $id
     * @return AggregateHistory
     */
    public function history(PostId $id);
}