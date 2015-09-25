<?php

namespace spec\Gorka\Blog\Infrastructure\Data\EventStore;

use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Data\EventStore\MongoEventStore;
use Gorka\Blog\Infrastructure\Exception\Data\DataAccessException;
use Gorka\Blog\Infrastructure\Exception\Data\DataNotFoundException;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Serializer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MongoEventStoreSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const POST_TITLE = 'Test title';
    const POST_CONTENT = 'Test content';
    const TEST_DB = 'testDB';
    const TEST_COLLECTION = 'testCollection';

    function let(Serializer $serializer, \MongoClient $mongoClient)
    {
        $this->beConstructedWith($serializer, $mongoClient, self::TEST_DB, self::TEST_COLLECTION);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MongoEventStore::class);
    }

    function it_should_store_events_to_mongo_on_commit_call(
        Serializer $serializer,
        \MongoClient $mongoClient,
        \MongoCollection $mongoCollection,
        AggregateHistory $aggregateHistory
    ) {
        $postId = PostId::create(self::POST_ID);
        $events = $this->buildTestEvents($postId);

        $aggregateHistory->events()->willReturn(array_column($events, 'event'));

        $serializedEvents = [];
        foreach ($events as $event) {
            $serializedEvent = $event['array'];
            $serializedEvents[] = $serializedEvent;
            $serializer->serialize($event['event'])->willReturn(json_encode($serializedEvent));
        }

        $mongoClient->selectCollection(self::TEST_DB, self::TEST_COLLECTION)->willReturn($mongoCollection);
        $mongoCollection->batchInsert($serializedEvents)->willReturn(true);

        $this->commit($aggregateHistory);
    }

    function it_should_throw_exception_on_commit_failure(\MongoClient $mongoClient, AggregateHistory $aggregateHistory)
    {
        $mongoClient->selectCollection(self::TEST_DB, self::TEST_COLLECTION)->willThrow(new \Exception());
        $this->shouldThrow(DataAccessException::class)->during('commit', [$aggregateHistory]);
    }

    function it_should_throw_exception_on_events_call_failure(\MongoClient $mongoClient, AggregateId $aggregateId)
    {
        $mongoClient->selectCollection()->willThrow(new \Exception());
        $this->shouldThrow(DataAccessException::class)->during('events', [$aggregateId]);
    }

    function it_should_retrieve_events_from_mongo_on_events_call(
        AggregateId $aggregateId,
        Serializer $serializer,
        \MongoClient $mongoClient,
        \MongoCollection $mongoCollection,
        \MongoCursor $mongoCursor
    ) {
        $postId = PostId::create(self::POST_ID);
        $aggregateId->id()->willReturn($postId);
        $events = $this->buildTestEvents($postId);

        $mongoClient->selectCollection(self::TEST_DB, self::TEST_COLLECTION)->willReturn($mongoCollection);
        $mongoCollection->find(['message.payload.id' => self::POST_ID])->willReturn($mongoCursor);
        $mongoCursor->sort(['creation_time' => 1])->willReturn(new \ArrayObject(array_column($events, 'array')));

        foreach ($events as $event) {
            $serializer->deserialize(json_encode($event['array'], true))->willReturn($event['event']);
        }

        $this->events($aggregateId)->shouldReturn(array_column($events, 'event'));
    }

    function it_should_throw_data_not_found_exception_on_empty_events(
        AggregateId $aggregateId,
        \MongoClient $mongoClient,
        \MongoCollection $mongoCollection,
        \MongoCursor $mongoCursor
    ) {
        $postId = 'abcdef';
        $aggregateId->id()->willReturn($postId);

        $mongoClient->selectCollection(self::TEST_DB, self::TEST_COLLECTION)->willReturn($mongoCollection);
        $mongoCollection->find(['message.payload.id' => $postId])->willReturn($mongoCursor);
        $mongoCursor->sort(['creation_time' => 1])->willReturn([]);

        $this->shouldThrow(DataNotFoundException::class)->during('events', [$aggregateId]);
    }

    function it_should_throw_data_access_exception_on_deserialization_failure(
        AggregateId $aggregateId,
        Serializer $serializer,
        \MongoClient $mongoClient,
        \MongoCollection $mongoCollection,
        \MongoCursor $mongoCursor
    ) {
        $postId = PostId::create(self::POST_ID);
        $aggregateId->id()->willReturn($postId);
        $events = ['asdasdasd', ['asdasd' => 'asdasd']];

        $mongoClient->selectCollection(self::TEST_DB, self::TEST_COLLECTION)->willReturn($mongoCollection);
        $mongoCollection->find(['message.payload.id' => self::POST_ID])->willReturn($mongoCursor);
        $mongoCursor->sort(['creation_time' => 1])->willReturn(new \ArrayObject($events));

        $serializer->deserialize(Argument::any())->willThrow(\Exception::class);

        $this->shouldThrow(DataAccessException::class)->during('events', [$aggregateId]);
    }

    function it_should_throw_data_access_exception_on_mongo_write_failure(
        Serializer $serializer,
        \MongoClient $mongoClient,
        \MongoCollection $mongoCollection,
        AggregateHistory $aggregateHistory
    ) {
        $postId = PostId::create(self::POST_ID);
        $events = $this->buildTestEvents($postId);

        $aggregateHistory->events()->willReturn(array_column($events, 'event'));

        $serializedEvents = [];
        foreach ($events as $event) {
            $serializedEvent = $event['array'];
            $serializedEvents[] = $serializedEvent;
            $serializer->serialize($event['event'])->willReturn(json_encode($serializedEvent));
        }

        $mongoClient->selectCollection(self::TEST_DB, self::TEST_COLLECTION)->willReturn($mongoCollection);
        $mongoCollection->batchInsert($serializedEvents)->willReturn(false);

        $this->shouldThrow(DataAccessException::class)->during('commit', [$aggregateHistory]);
    }

    /**
     * @param $postId
     * @return array
     */
    private function buildTestEvents($postId)
    {
        $eventCreate = new PostWasCreated(
            $postId,
            self::POST_TITLE,
            self::POST_CONTENT
        );
        $eventCreateArray = [
            'message' => [
                'type' => 'blog:post_was_created',
                'payload' => [
                    'id' => self::POST_ID,
                    'title' => self::POST_TITLE,
                    'content' => self::POST_CONTENT
                ]
            ],
            'creation_time' => '2015-02-03T12:34:56+0100'
        ];

        $eventPublish = new PostWasPublished($postId);
        $eventPublishArray = [
            'message' => [
                'type' => 'blog:post_was_published',
                'payload' => [
                    'id' => self::POST_ID
                ]
            ],
            'creation_time' => '2015-02-03T12:34:58+0100'
        ];

        return [
            [
                'event' => $eventCreate,
                'array' => $eventCreateArray
            ],
            [
                'event' => $eventPublish,
                'array' => $eventPublishArray
            ]
        ];
    }
}
