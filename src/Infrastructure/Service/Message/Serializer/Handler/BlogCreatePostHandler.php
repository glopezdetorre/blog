<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Command\Post\CreatePost;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\DomainMessageHandler;

class BlogCreatePostHandler implements DomainMessageHandler
{

    /**
     * @param DomainMessage $message
     * @return array
     */
    public function serialize(DomainMessage $message)
    {
        if (!($message instanceof CreatePost)) {
            throw new \InvalidArgumentException();
        }

        return
            [
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

        return new CreatePost(PostId::create($data['id']), $data['title'], $data['slug'], $data['content']);
    }
}
