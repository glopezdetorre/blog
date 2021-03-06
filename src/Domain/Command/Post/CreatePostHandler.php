<?php

namespace Gorka\Blog\Domain\Command\Post;

use Gorka\Blog\Domain\Model\AggregateHistory;
use Gorka\Blog\Domain\Model\EventStore;
use Gorka\Blog\Domain\Model\Post\Post;
use Gorka\Blog\Domain\Service\Slugifier;
use SimpleBus\Message\Bus\MessageBus;

class CreatePostHandler
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var MessageBus
     */
    private $eventBus;

    /**
     * @var Slugifier
     */
    private $slugifier;

    /**
     * @param EventStore $eventStore
     * @param MessageBus $eventBus
     * @param Slugifier $slugifier
     */
    public function __construct(EventStore $eventStore, MessageBus $eventBus, Slugifier $slugifier)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
        $this->slugifier = $slugifier;
    }

    public function handle(CreatePost $command)
    {
        $slug = $command->postSlug();
        if (null == $slug) {
            $slug = $this->slugifier->slugify($command->postTitle());
        }

        $post = Post::create($command->postId(), $command->postTitle(), $slug, $command->postContent());
        $this->eventStore->commit(new AggregateHistory($post->id(), $post->recordedEvents()));
        foreach ($post->recordedEvents() as $event) {
            $this->eventBus->handle($event);
        }
    }
}
