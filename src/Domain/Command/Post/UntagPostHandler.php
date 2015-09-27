<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Command\DomainCommand;
use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Service\Slugifier;
use SimpleBus\Message\Bus\MessageBus;

class UntagPostHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var MessageBus
     */
    private $eventBus;

    public function __construct(EventStore $eventStore, MessageBus $eventBus)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    public function handle(UntagPost $command)
    {
        $aggregateEvents = $this->eventStore->events($command->postId());
        $post = Post::reconstituteFromEvents(new AggregateHistory($command->postId(), $aggregateEvents));
        $post->removeTag($command->tagName());

        $this->eventStore->commit(new AggregateHistory($post->id(), $post->recordedEvents()));
        foreach ($post->recordedEvents() as $event) {
            $this->eventBus->handle($event);
        }
    }
}
