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
use Gorka\Blog\Infrastructure\Exception\Data\DataAccessException;
use Gorka\Blog\Infrastructure\Exception\Data\DataNotFoundException;

/**
 * Interface EventStore
 */
interface EventStore
{
    /**
     * @param AggregateHistory $history
     * @throws DataNotFoundException
     */
    public function commit(AggregateHistory $history);

    /**
     * @param AggregateId $id
     * @return DomainEvent[]
     * @throws DataNotFoundException
     * @throws DataAccessException
     */
    public function events(AggregateId $id);
}
