<?php

namespace Gorka\Blog\Infrastructure\Service\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Handler\SubscribingHandlerInterface;

/**
 * Class DateTimeImmutableHandler
 *
 * JMSSerializer does not support \DateTimeImmutable, so we blatantly copy DateHandler
 */
class DateTimeImmutableHandler implements SubscribingHandlerInterface
{
    private $defaultFormat;
    private $defaultTimezone;
    private $xmlCData;

    public static function getSubscribingMethods()
    {
        $methods = array();

        foreach (array('json', 'xml', 'yml') as $format) {
            $methods[] = array(
                'type' => 'DateTimeImmutable',
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => $format,
            );

            $methods[] = array(
                'type' => 'DateTimeImmutable',
                'format' => $format,
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'method' => 'serializeDateTimeImmutable',
            );
        }

        return $methods;
    }

    public function __construct($defaultFormat = \DateTime::ISO8601, $defaultTimezone = 'UTC', $xmlCData = true)
    {
        $this->defaultFormat = $defaultFormat;
        $this->defaultTimezone = new \DateTimeZone($defaultTimezone);
        $this->xmlCData = $xmlCData;
    }

    public function serializeDateTimeImmutable(
        VisitorInterface $visitor,
        \DateTimeImmutable $date,
        array $type,
        Context $context
    ) {
        if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
            return $visitor->visitSimpleString($date->format($this->getFormat($type)), $type, $context);
        }
        return $visitor->visitString($date->format($this->getFormat($type)), $type, $context);
    }

    public function deserializeDateTimeImmutableFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        $attributes = $data->attributes('xsi', true);
        if (isset($attributes['nil'][0]) && (string) $attributes['nil'][0] === 'true') {
            return null;
        }

        return $this->parseDateTime($data, $type);
    }

    public function deserializeDateTimeImmutableFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if (null === $data) {
            return null;
        }

        return $this->parseDateTime($data, $type);
    }

    private function parseDateTime($data, array $type)
    {
        $timezone = isset($type['params'][1]) ? new \DateTimeZone($type['params'][1]) : $this->defaultTimezone;
        $format = $this->getFormat($type);
        $datetime = \DateTimeImmutable::createFromFormat($format, (string) $data, $timezone);
        if (false === $datetime) {
            throw new RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
        }

        return $datetime;
    }

    /**
     * @return string
     * @param array $type
     */
    private function getFormat(array $type)
    {
        return isset($type['params'][0]) ? $type['params'][0] : $this->defaultFormat;
    }

    /**
     * @param \DateInterval $dateInterval
     * @return string
     */
    public function format(\DateInterval $dateInterval)
    {
        $format = 'P';

        if (0 < $dateInterval->y) {
            $format .= $dateInterval->y.'Y';
        }

        if (0 < $dateInterval->m) {
            $format .= $dateInterval->m.'M';
        }

        if (0 < $dateInterval->d) {
            $format .= $dateInterval->d.'D';
        }

        if (0 < $dateInterval->h || 0 < $dateInterval->i || 0 < $dateInterval->s) {
            $format .= 'T';
        }

        if (0 < $dateInterval->h) {
            $format .= $dateInterval->h.'H';
        }

        if (0 < $dateInterval->i) {
            $format .= $dateInterval->i.'M';
        }

        if (0 < $dateInterval->s) {
            $format .= $dateInterval->s.'S';
        }

        return $format;
    }
}
