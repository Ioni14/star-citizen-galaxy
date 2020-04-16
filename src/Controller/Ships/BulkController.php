<?php

namespace App\Controller\Ships;

use App\Repository\ShipRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class BulkController extends AbstractController
{
    private SerializerInterface $serializer;
    private ShipRepository $shipRepository;

    public function __construct(SerializerInterface $serializer, ShipRepository $shipRepository)
    {
        $this->serializer = $serializer;
        $this->shipRepository = $shipRepository;
    }

    /**
     * Route in Ship entity ApiResource annotation.
     */
    public function __invoke(Request $request)
    {
        $filters = $this->serializer->decode($request->getContent(), $request->getContentType());

        $criteria = Criteria::create();
        if (isset($filters['ids']) && is_array($filters['ids'])) {
            $criteria->orWhere(Criteria::expr()->in('id', $filters['ids']));
        }
        if (isset($filters['names']) && is_array($filters['names'])) {
            $criteria->orWhere(Criteria::expr()->in('name', $filters['names']));
        }

        return $this->shipRepository->matching($criteria);
    }
}
