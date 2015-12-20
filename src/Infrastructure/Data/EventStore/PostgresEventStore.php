<?php

namespace Gorka\Blog\Infrastructure\Data\EventStore;

use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Infrastructure\Exception\Data\DataAccessException;
use Gorka\Blog\Infrastructure\Exception\Data\DataNotFoundException;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Serializer;

class PostgresEventStore implements EventStore
{
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var \PDO
     */
    private $connection;

    public function __construct(Serializer $serializer, \PDO $connection)
    {
        $this->serializer = $serializer;
        $this->connection = $connection;
    }

    /**
     * @param AggregateHistory $history
     * @throws DataAccessException
     */
    public function commit(AggregateHistory $history)
    {
        $stmt = $this->connection->prepare("INSERT INTO post_events (data) VALUES (:message)");

        $this->connection->beginTransaction();
        foreach ($history->events() as $event) {
            if (!$stmt->execute([':message' => $this->serializer->serialize($event)])) {
                $this->connection->rollBack();
                throw new DataAccessException();
            }
        }

        if (!$this->connection->commit()) {
            $this->connection->rollBack();
            throw new DataAccessException();
        }
    }

    /**
     * @param AggregateId $id
     * @return DomainEvent[]
     * @throws DataNotFoundException
     * @throws DataAccessException
     */
    public function events(AggregateId $id)
    {
        $stmt = $this->connection
            ->prepare("SELECT data FROM post_events WHERE data->'message'->'payload'->>'id' = :id")
        ;

        if (!$stmt->execute(['id' => (string) $id->id()])) {
            throw new DataAccessException();
        }

        $serializedEvents = $stmt->fetchAll();
        if (empty($serializedEvents)) {
            throw  new DataNotFoundException('No events found for given aggregate');
        }

        $events = [];
        foreach ($serializedEvents as $serializedEvent) {
            try {
                $event = $this->serializer->deserialize($serializedEvent);
            } catch (\Exception $e) {
                throw new DataAccessException();
            }
            $events[] = $event;
        }
        return $events;
    }
}
