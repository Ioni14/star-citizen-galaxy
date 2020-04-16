<?php

namespace App\Controller;

use App\Repository\ManufacturerRepository;
use App\Repository\ShipChassisRepository;
use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public const MINIMUM_SEARCH_QUERY_LENGTH = 3;

    private ShipRepository $shipRepository;
    private ShipChassisRepository $shipChassisRepository;
    private ManufacturerRepository $manufacturerRepository;

    public function __construct(
        ShipRepository $shipRepository,
        ShipChassisRepository $shipChassisRepository,
        ManufacturerRepository $manufacturerRepository
    ) {
        $this->shipRepository = $shipRepository;
        $this->shipChassisRepository = $shipChassisRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $searchQuery = $request->query->get('q');
        if (mb_strlen($searchQuery) < self::MINIMUM_SEARCH_QUERY_LENGTH) {
            return $this->render('search.html.twig', [
                'error' => 'too_short_query',
                'searchQuery' => $searchQuery,
            ]);
        }

        $ships = $this->shipRepository->searchByQuery($searchQuery);
        $chassis = $this->shipChassisRepository->searchByQuery($searchQuery);
        $manufacturers = $this->manufacturerRepository->searchByQuery($searchQuery);

        return $this->render('search.html.twig', [
            'ships' => $ships,
            'chassis' => $chassis,
            'manufacturers' => $manufacturers,
            'searchQuery' => $searchQuery,
        ]);
    }
}
