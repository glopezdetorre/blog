<?php

namespace Gorka\Blog\Infrastructure\Data\EventStore;

use Assert\Assertion;
use Gorka\Blog\Domain\Event\DomainEvent;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Infrastructure\Exception\Data\DataAccessException;
use Gorka\Blog\Infrastructure\Exception\Data\DataNotFoundException;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Serializer;

class MongoEventStore implements EventStore
{

    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var \MongoClient
     */
    private $mongoClient;

    /**
     * @var string
     */
    private $databaseName;

    /**
     * @var string
     */
    private $collectionName;

    public function __construct(Serializer $serializer, \MongoClient $mongoClient, $databaseName, $collectionName)
    {
        $this->serializer = $serializer;
        $this->mongoClient = $mongoClient;
        $this->setDatabaseName($databaseName);
        $this->setCollectionName($collectionName);
    }

    /**
     * @param AggregateHistory $history
     * @throws DataAccessException
     */
    public function commit(AggregateHistory $history)
    {
        try {
            $eventBatch = $this->prepareEventBatch($history->events());
            $result = $this->mongoClient
                ->selectCollection($this->databaseName, $this->collectionName)
                ->batchInsert($eventBatch);
            ;
        } catch (\Exception $e) {
            throw new DataAccessException($e->getMessage());
        }

        if (!$result) {
            throw new DataAccessException('Unable to persist events');
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
        try {
            $serializedEvents = $this->mongoClient
                ->selectCollection($this->databaseName, $this->collectionName)
                ->find(['message.payload.id' => $id->id()])
                ->sort(['creation_time' => 1])
            ;
        } catch (\Exception $e) {
            throw new DataAccessException($e->getMessage());
        }


        if (!$serializedEvents || $serializedEvents->count() == 0) {
            throw new DataNotFoundException('No events found for the given ID');
        }

        try {
            $events = [];
            foreach ($serializedEvents as $serializedEvent) {
                $events[] = $this->serializer->deserialize(json_encode($serializedEvent));
            }
        } catch (\Exception $e) {
            throw new DataAccessException($e->getMessage());
        }

        return $events;
    }

    /**
     * @param $databaseName
     */
    private function setDatabaseName($databaseName)
    {
        Assertion::string($databaseName);
        Assertion::notBlank(trim($databaseName));
        $this->databaseName = $databaseName;
    }

    /**
     * @param $collectionName
     */
    private function setCollectionName($collectionName)
    {
        Assertion::string($collectionName);
        Assertion::notBlank(trim($collectionName));
        $this->collectionName = $collectionName;
    }

    private function prepareEventBatch($events)
    {
        $serializedEvents = [];
        foreach ($events as $event) {
            $serializedEvents[] = json_decode($this->serializer->serialize($event), true);
        }
        return $serializedEvents;
    }
}
