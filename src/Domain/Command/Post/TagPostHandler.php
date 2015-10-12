<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Model\Post\Tag;
use Gorka\Blog\Domain\Service\Slugifier;
use Prooph\ServiceBus\EventBus;

class TagPostHandler
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
     * @var Slugifier
     */
    private $slugifier;

    public function __construct(EventStore $eventStore, EventBus $eventBus, Slugifier $slugifier)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
        $this->slugifier = $slugifier;
    }

    public function handle(TagPost $command)
    {
        $aggregateEvents = $this->eventStore->events($command->postId());
        $post = Post::reconstituteFromEvents(new AggregateHistory($command->postId(), $aggregateEvents));
        $post->addTag(Tag::create($command->tagName(), $this->slugifier->slugify($command->tagName())));

        $this->eventStore->commit(new AggregateHistory($post->id(), $post->recordedEvents()));
        foreach ($post->recordedEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
