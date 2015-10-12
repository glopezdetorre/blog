<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\Post;
use Prooph\ServiceBus\EventBus;

class UnpublishPostHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @param EventStore $eventStore
     * @param EventBus $eventBus
     */
    public function __construct(EventStore $eventStore, EventBus $eventBus)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    public function handle(UnpublishPost $command)
    {
        $aggregateEvents = $this->eventStore->events($command->postId());
        $post = Post::reconstituteFromEvents(new AggregateHistory($command->postId(), $aggregateEvents));
        $post->unpublish();

        $this->eventStore->commit(new AggregateHistory($post->id(), $post->recordedEvents()));
        foreach ($post->recordedEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
