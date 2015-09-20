<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Event\Post\PostContentWasChanged;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\DomainMessageHandler;

class BlogPostContentWasChangedHandler implements DomainMessageHandler
{
    /**
     * @param DomainMessage $message
     * @return array
     */
    public function serialize(DomainMessage $message)
    {
        if (!($message instanceof PostContentWasChanged)) {
            throw new \InvalidArgumentException();
        }

        return [
            'id' => ((string) $message->postId()),
            'content' => $message->postContent()
        ];
    }

    /**
     * @param array $data
     * @return DomainMessage
     */
    public function deserialize(array $data)
    {
        if (!is_array($data) || !isset($data['id'], $data['content'])) {
            throw new \LogicException();
        }
        return new PostContentWasChanged(PostId::create($data['id']), $data['content']);
    }
}
