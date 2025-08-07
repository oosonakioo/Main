<?php

namespace Intervention\Image\Filters;

interface FilterInterface
{
    /**
     * Applies filter to given image
     *
     * @return \Intervention\Image\Image
     */
    public function applyFilter(\Intervention\Image\Image $image);
}
