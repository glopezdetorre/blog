<?php

namespace Gorka\Blog\Infrastructure\Service;

use Cocur\Slugify\Slugify;

class Slugifier implements \Gorka\Blog\Domain\Service\Slugifier
{
    /**
     * @var Slugify
     */
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function slugify($string, $separator = '-')
    {
        return $this->slugify->slugify($string, $separator);
    }
}
