<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Event\Post\PostWasCreated;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\DomainMessageHandler;

class BlogPostWasCreatedHandler implements DomainMessageHandler
{

    /**
     * @param DomainMessage $message
     * @return array
     */
    public function serialize(DomainMessage $message)
    {
        if (!($message instanceof PostWasCreated)) {
            throw new \InvalidArgumentException();
        }

        return [
            'id' => ((string) $message->postId()),
            'title' => $message->postTitle(),
            'slug' => $message->postSlug(),
            'content' => $message->postContent()
        ];
    }

    /**
     * @param array $data
     * @return DomainMessage
     */
    public function deserialize(array $data)
    {
        if (!is_array($data) || !isset($data['id'], $data['title'], $data['content'])) {
            throw new \LogicException();
        }
        return new PostWasCreated(PostId::create($data['id']), $data['title'], $data['slug'], $data['content']);
    }
}
