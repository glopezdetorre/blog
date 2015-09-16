<?php

namespace Gorka\Blog\Infrastructure\Service\Serializer;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Handler\HandlerRegistry;
use Gorka\Blog\Infrastructure\Service\Serializer\Handler\DateTimeImmutableHandler;

class Serializer
{
    public static function serialize($object)
    {
        $serializer = SerializerBuilder::create()
            ->configureHandlers(
                function (HandlerRegistry $registry) {
                    $registry->registerSubscribingHandler(new DateTimeImmutableHandler());
                }
            )->build()
        ;
        return $serializer->serialize($object, 'json');
    }

    public static function deserialize($data, $type)
    {
        $serializer = SerializerBuilder::create()
            ->configureHandlers(
                function (HandlerRegistry $registry) {
                    $registry->registerSubscribingHandler(new DateTimeImmutableHandler());
                }
            )->build()
        ;
        return $serializer->deserialize($data, $type, 'json');
    }
}
