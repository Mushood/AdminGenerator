<?php

namespace App\Library;

use \Intervention\Image\Image as InterventionImage;

class InterventionWrapperImage
{
    /**
     * REFERENCE:
     * https://stackoverflow.com/questions/33660956/how-do-i-resize-image-using-intervention-before-storing-to-s3
     * Intervention image instance.
     *
     * @var \Intervention\Image\Image
     */
    private $image;

    public function __construct(InterventionImage $image)
    {
        $this->image = $image;
    }

    public function getRealPath()
    {
        return $this->image->basePath();
    }
}
