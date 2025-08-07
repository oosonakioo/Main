<?php

namespace Intervention\Image\Imagick\Commands;

class InvertCommand extends \Intervention\Image\Commands\AbstractCommand
{
    /**
     * Inverts colors of an image
     *
     * @param  \Intervention\Image\Image  $image
     * @return bool
     */
    public function execute($image)
    {
        return $image->getCore()->negateImage(false);
    }
}
