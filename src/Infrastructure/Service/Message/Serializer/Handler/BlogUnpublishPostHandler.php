<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer\Handler;

use Gorka\Blog\Domain\Command\Post\UnpublishPost;
use Gorka\Blog\Domain\Model\DomainMessage;
use Gorka\Blog\Domain\Model\Post\PostId;
use Gorka\Blog\Infrastructure\Service\Message\Serializer\DomainMessageHandler;

class BlogUnpublishPostHandler implements DomainMessageHandler
{

    /**
     * @param DomainMessage $message
     * @return array
     */
    public function serialize(DomainMessage $message)
    {
        if (!($message instanceof UnpublishPost)) {
            throw new \InvalidArgumentException();
        }

        return [
            'id' => ((string) $message->postId())
        ];
    }

    /**
     * @param array $data
     * @return DomainMessage
     */
    public function deserialize(array $data)
    {
        if (!is_array($data) || !isset($data['id'])) {
            throw new \LogicException();
        }
        return new UnpublishPost(PostId::create($data['id']));
    }
}
