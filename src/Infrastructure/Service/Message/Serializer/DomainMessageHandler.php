<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer;

use Gorka\Blog\Domain\Model\DomainMessage;

interface DomainMessageHandler
{
    /**
     * @param DomainMessage $message
     * @return array
     */
    public function serialize(DomainMessage $message);

    /**
     * @param array $data
     * @return DomainMessage
     */
    public function deserialize(array $data);
}
