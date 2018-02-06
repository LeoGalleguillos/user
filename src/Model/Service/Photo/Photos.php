<?php
namespace LeoGalleguillos\User\Model\Service\Photo;

use ArrayObject;
use Generator;
use LeoGalleguillos\Photo\Model\Entity as PhotoEntity;
use LeoGalleguillos\User\Model\Entity as UserEntity;
use LeoGalleguillos\User\Model\Table as UserTable;

class Photos
{
    /**
     * Construct.
     *
     * @param UserTable\Photo $photoTable
     */
    public function __construct(
        UserTable\Photo $photoTable
    ) {
        $this->photoTable = $photoTable;
    }

    /**
     * Get newest photos.
     *
     * @return Generator
     */
    public function getNewestPhotos() : Generator
    {
        foreach ($this->photoTable->selectOrderByCreatedDesc() as $arrayObject) {
            $photo    = new UserEntity\Photo();
            $original = new ImageEntity\Image();
            $original->setRootRelativeUrl(
                '/uploads/photos/'
                . $arrayObject['photo_id']
                . '/original.'
                . $arrayObject['extension']
            );
            $photo->setOriginal($original);
            yield $photo;
        }
    }
}