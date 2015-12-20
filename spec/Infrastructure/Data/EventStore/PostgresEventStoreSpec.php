<?php

namespace spec\Gorka\Blog\Infrastructure\Data\EventStore;

use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Event\Post\PostWasPublished;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\AggregateId;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Data\EventStore\PostgresEventStore;
use Gorka\Blog\Infrastructure\Exception\Data\DataAccessException;
use Gorka\Blog\Infrastructure\Exception\Data\DataNotFoundException;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\Serializer;
use PDOStatement;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/** @mixin PostgresEventStore */
class PostgresEventStoreSpec extends ObjectBehavior
{
    const POST_ID = '25769c6c-d34d-4bfe-ba98-e0ee856f3e7a';
    const POST_TITLE = 'Test title';
    const POST_SLUG = 'test-title';
    const POST_CONTENT = 'Test content';
    const TEST_DB = 'testDB';
    const TEST_COLLECTION = 'testCollection';

    function let(Serializer $serializer, \PDO $connection)
    {
        $this->beConstructedWith($serializer, $connection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PostgresEventStore::class);
    }

    function it_is_an_event_store()
    {
        $this->shouldBeAnInstanceOf(EventStore::class);
    }

    function it_should_store_events_to_postgres_on_commit_call(
        Serializer $serializer,
        \PDO $connection,
        \PDOStatement $stmt,
        AggregateHistory $aggregateHistory
    ) {
        $postId = PostId::create(self::POST_ID);
        $events = $this->buildTestEvents($postId);

        $aggregateHistory->events()->willReturn(array_column($events, 'event'));

        $connection->beginTransaction()->shouldBeCalled();
        $connection->prepare("INSERT INTO post_events (data) VALUES (:message)")->willReturn($stmt);
        foreach ($events as $event) {
            $serializedEvent = $event['array'];
            $serializer->serialize($event['event'])->willReturn(json_encode($serializedEvent));
            $stmt->execute([':message' => json_encode($serializedEvent)])->willReturn(true);
        }
        $connection->commit()->willReturn(true);

        $this->commit($aggregateHistory);
    }

    function it_should_throw_exception_on_execute_failure(
        AggregateHistory $aggregateHistory,
        \PDO $connection,
        \PDOStatement $stmt
    ) {
        $postId = PostId::create(self::POST_ID);
        $events = $this->buildTestEvents($postId);

        $aggregateHistory->events()->willReturn(array_column($events, 'event'));

        $connection->beginTransaction()->shouldBeCalled();
        $connection->prepare("INSERT INTO post_events (data) VALUES (:message)")->willReturn($stmt);
        $connection->rollBack()->shouldBeCalled();
        $stmt->execute(Argument::any())->willReturn(false);
        $this->shouldThrow(DataAccessException::class)->during('commit', [$aggregateHistory]);
    }

    function it_should_throw_exception_on_commit_failure(
        AggregateHistory $aggregateHistory,
        \PDO $connection,
        \PDOStatement $stmt
    ) {
        $postId = PostId::create(self::POST_ID);
        $events = $this->buildTestEvents($postId);

        $aggregateHistory->events()->willReturn(array_column($events, 'event'));

        $connection->beginTransaction()->shouldBeCalled();
        $connection->prepare("INSERT INTO post_events (data) VALUES (:message)")->willReturn($stmt);
        $connection->rollBack()->shouldBeCalled();
        $stmt->execute(Argument::any())->willReturn(true);
        $connection->commit(Argument::any())->willReturn(false);
        $this->shouldThrow(DataAccessException::class)->during('commit', [$aggregateHistory]);
    }

    function it_should_throw_exception_on_events_call_failure(
        \PDO $connection,
        AggregateId $aggregateId,
        \PDOStatement $stmt
    ) {
        $connection
            ->prepare("SELECT data FROM post_events WHERE data->'message'->'payload'->>'id' = :id")
            ->willReturn($stmt);

        $stmt->execute(Argument::any())->willReturn(false);
        $this->shouldThrow(DataAccessException::class)->during('events', [$aggregateId]);
    }

    function it_should_retrieve_events_from_postgres_on_events_call(
        AggregateId $aggregateId,
        Serializer $serializer,
        \PDO $connection,
        \PDOStatement $stmt
    ) {
        $postId = PostId::create(self::POST_ID);
        $aggregateId->id()->willReturn($postId);
        $events = $this->buildTestEvents($postId);

        $connection
            ->prepare("SELECT data FROM post_events WHERE data->'message'->'payload'->>'id' = :id")
            ->willReturn($stmt);

        $stmt->execute(Argument::any())
            ->willReturn(true);

        $stmt->fetchAll()->willReturn(
            array_map(
                function ($data) {
                    return json_encode($data);
                },
                array_column($events, 'array')
            )
        );

        foreach ($events as $event) {
            $serializer->deserialize(json_encode($event['array'], true))->willReturn($event['event']);
        }

        $this->events($aggregateId)->shouldReturn(array_column($events, 'event'));
    }

    function it_should_throw_data_not_found_exception_on_empty_events(
        AggregateId $aggregateId,
        \PDO $connection,
        \PDOStatement $stmt
    ) {
        $postId = 'abcdef';
        $aggregateId->id()->willReturn($postId);

        $connection
            ->prepare("SELECT data FROM post_events WHERE data->'message'->'payload'->>'id' = :id")
            ->willReturn($stmt);

        $stmt->execute(Argument::any())
            ->willReturn(true);

        $stmt->fetchAll()->willReturn([]);

        $this->shouldThrow(DataNotFoundException::class)->during('events', [$aggregateId]);
    }

    function it_should_throw_data_access_exception_on_deserialization_failure(
        AggregateId $aggregateId,
        Serializer $serializer,
        \PDO $connection,
        \PDOStatement $stmt
    ) {
        $postId = PostId::create(self::POST_ID);
        $aggregateId->id()->willReturn($postId);
        $events = ['asdasdasd', ['asdasd' => 'asdasd']];

        $connection->prepare(Argument::any())->willReturn($stmt);
        $stmt->execute(Argument::any())->willReturn(true);

        $stmt->fetchAll()->willReturn(
            array_map(
                function ($data) {
                    return json_encode($data);
                },
                $events
            )
        );

        $serializer->deserialize(Argument::any())->willThrow(\Exception::class);

        $this->shouldThrow(DataAccessException::class)->during('events', [$aggregateId]);
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
            self::POST_SLUG,
            self::POST_CONTENT
        );
        $eventCreateArray = [
            'message' => [
                'type' => 'blog:post_was_created',
                'payload' => [
                    'id' => self::POST_ID,
                    'title' => self::POST_TITLE,
                    'slug' => self::POST_SLUG,
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
