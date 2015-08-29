<?php

namespace Gorka\Blog\Infrastructure\Data;

use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;

/**
 * Created by IntelliJ IDEA.
 * User: gorka
 * Date: 15/8/15
 * Time: 8:52
 */
interface EventStore
{
    public function commit(AggregateHistory $history);

    public function aggregateHistory(AggregateId $id);
}
