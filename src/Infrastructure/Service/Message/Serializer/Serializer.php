<?php

namespace Gorka\Blog\Infrastructure\Service\Message\Serializer;

use Gorka\Blog\Domain\Model\DomainMessage;

class Serializer
{
    /**
     * @var Wrapper
     */
    private $wrapper;

    /**
     * @param Wrapper $wrapper
     */
    public function __construct(Wrapper $wrapper)
    {
        $this->wrapper = $wrapper;
    }

    /**
     * @param DomainMessage $domainMessage
     * @return string
     */
    public function serialize(DomainMessage $domainMessage)
    {
        $wrappedMessage = $this->wrapper->wrap($domainMessage);
        $serializer = $this->getSerializerForMessage($domainMessage);

        $data = [
            'message' => [
                'type' => $domainMessage->messageName(),
                'payload' => $serializer->serialize($domainMessage)
            ],
            'creation_time' => $wrappedMessage->creationTime()->format(\DateTime::ISO8601)
        ];
        return json_encode($data);
    }

    /**
     * @param $data
     * @return DomainMessage
     */
    public function deserialize($data)
    {
        try {
            $data = json_decode($data, true);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Malformed message');
        }

        if (!is_array($data) || !isset($data['message'], $data['message']['type'], $data['message']['payload'])) {
            throw new \InvalidArgumentException('Malformed message');
        }

        $serializer = $this->getSerializerForMessageType($data['message']['type']);
        return $serializer->deserialize($data['message']['payload']);
    }

    /**
     * @param DomainMessage $domainMessage
     * @return DomainMessageHandler
     */
    private function getSerializerForMessage(DomainMessage $domainMessage)
    {
        return $this->getSerializerForMessageType($domainMessage->messageName());
    }

    /**
     * @param string $type
     * @return DomainMessageHandler
     */
    private function getSerializerForMessageType($type)
    {
        $camelCasedName = preg_replace_callback(
            '/[-_:\s](.?)/',
            function ($matches) {
                return ucfirst($matches[1]);
            },
            $type
        );

        $serializerFqn = preg_replace(
            '/\\\([^\\\]+)$/',
            sprintf('\Handler\%sHandler', ucfirst($camelCasedName)),
            get_class($this)
        );

        return new $serializerFqn;
    }
}
