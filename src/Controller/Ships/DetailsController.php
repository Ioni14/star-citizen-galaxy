<?php

namespace App\Controller\Ships;

use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailsController extends AbstractController
{
    private ShipRepository $shipRepository;

    public function __construct(ShipRepository $shipRepository)
    {
        $this->shipRepository = $shipRepository;
    }

    /**
     * @Route("/ships/details/{slug}", name="ships_details", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $ship = $this->shipRepository->findOneShipJoinedChassis($slug);

        return $this->render('ships/details.html.twig', [
            'ship' => $ship,
        ]);
    }
}
