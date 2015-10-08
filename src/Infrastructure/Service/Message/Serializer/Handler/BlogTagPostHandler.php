<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Command\Post\TagPost;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\DomainMessageHandler;

class BlogTagPostHandler implements DomainMessageHandler
{

    /**
     * @param DomainMessage $message
     * @return array
     */
    public function serialize(DomainMessage $message)
    {
        if (!($message instanceof TagPost)) {
            throw new \InvalidArgumentException();
        }

        return [
            'id' => $message->postId()->id(),
            'tag' => [
                'name' => $message->tagName()
            ]
        ];
    }

    /**
     * @param array $data
     * @return DomainMessage
     */
    public function deserialize(array $data)
    {
        if (!is_array($data) || !isset($data['id'], $data['tag'], $data['tag']['name'])) {
            throw new \LogicException();
        }

        return new TagPost(PostId::create($data['id']), $data['tag']['name']);
    }
}
