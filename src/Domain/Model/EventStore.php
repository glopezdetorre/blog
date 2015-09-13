<?php
/**
 * Project: blog
 * File: EventStore.php
 *
 * User: gorka
 * Date: 5/9/15
 */

namespace Gorka\Blog\Domain\Model;

use Gorka\Blog\Domain\Event\DomainEvent;

/**
 * Interface EventStore
 */
interface EventStore
{
    /**
     * @param AggregateHistory $history
     */
    public function commit(AggregateHistory $history);

    /**
     * @param AggregateId $id
     * @return DomainEvent[]
     */
    public function events(AggregateId $id);
}
