<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Event\Post\PostTitleWasChanged;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\DomainMessageHandler;

class BlogPostTitleWasChangedHandler implements DomainMessageHandler
{

    /**
     * @param DomainMessage $message
     * @return string
     */
    public function serialize(DomainMessage $message)
    {
        if (!($message instanceof PostTitleWasChanged)) {
            throw new \InvalidArgumentException();
        }

        return [
            'id' => ((string) $message->postId()),
            'title' => $message->postTitle()
        ];
    }

    /**
     * @param array $data
     * @return DomainMessage
     */
    public function deserialize(array $data)
    {
        if (!is_array($data) || !isset($data['id'], $data['title'])) {
            throw new \LogicException();
        }
        return new PostTitleWasChanged(PostId::create($data['id']), $data['title']);
    }
}
