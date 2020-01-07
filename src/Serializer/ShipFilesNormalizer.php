<?php

namespace App\Serializer;

use App\Entity\Ship;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ShipFilesNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private NormalizerInterface $decorated;
    private Packages $assetPackages;

    public function __construct(NormalizerInterface $decorated, Packages $assetPackages)
    {
        $this->decorated = $decorated;
        $this->assetPackages = $assetPackages;
    }

    /**
     * @param Ship        $object
     * @param string|null $format
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->decorated->normalize($object, $format, $context);
        if (!is_array($data)) {
            return $data;
        }


        // TODO : add to Ship schema the two fields :


        if ($object instanceof Ship) {
            $data['pictureUri'] = $object->getPicturePath() !== null ? $this->assetPackages->getUrl($object->getPicturePath(), 'ship_pictures') : null;
            $data['thumbnailUri'] = $object->getThumbnailPath() !== null ? $this->assetPackages->getUrl($object->getThumbnailPath(), 'ship_thumbnails') : null;
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}
