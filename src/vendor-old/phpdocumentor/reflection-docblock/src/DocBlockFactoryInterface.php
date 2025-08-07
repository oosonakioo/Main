<?php

namespace phpDocumentor\Reflection;

interface DocBlockFactoryInterface
{
    /**
     * Factory method for easy instantiation.
     *
     * @param  string[]  $additionalTags
     * @return DocBlockFactory
     */
    public static function createInstance(array $additionalTags = []);

    /**
     * @param  string  $docblock
     * @return DocBlock
     */
    public function create($docblock, ?Types\Context $context = null, ?Location $location = null);
}
