<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Event\Post\PostWasTagged;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Domain\Model\Post\Tag;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\DomainMessageHandler;

class BlogPostWasTaggedHandler implements DomainMessageHandler
{
    /**
     * @param DomainMessage $message
     * @return array
     */
    public function serialize(DomainMessage $message)
    {
        if (!$message instanceof PostWasTagged) {
            throw new \InvalidArgumentException();
        }

        return [
            'id' => ((string) $message->postId()),
            'tag' => [
                'name' => $message->tag()->name(),
                'slug' => $message->tag()->slug()
            ]
        ];
    }

    /**
     * @param array $data
     * @return DomainMessage
     */
    public function deserialize(array $data)
    {
        if (!is_array($data) || !isset($data['id'], $data['tag'], $data['tag']['name'], $data['tag']['slug'])) {
            throw new \LogicException();
        }

        return new PostWasTagged(
            PostId::create($data['id']),
            Tag::create($data['tag']['name'], $data['tag']['slug'])
        );
    }
}
