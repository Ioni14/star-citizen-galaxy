<?php

namespace App\Controller\Ships;

use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    private ShipRepository $shipRepository;

    public function __construct(ShipRepository $shipRepository)
    {
        $this->shipRepository = $shipRepository;
    }

    /**
     * @Route("/ships", name="ships_list", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $ships = $this->shipRepository->findShipJoinedChassis();

        return $this->render('ships/list.html.twig', [
            'ships' => $ships,
        ]);
    }
}
