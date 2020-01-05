<?php

namespace App\Controller\ShipChassis;

use App\Repository\ShipChassisRepository;
use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailsController extends AbstractController
{
    private ShipChassisRepository $shipChassisRepository;
    private ShipRepository $shipRepository;

    public function __construct(
        ShipChassisRepository $shipChassisRepository,
        ShipRepository $shipRepository
    ) {
        $this->shipChassisRepository = $shipChassisRepository;
        $this->shipRepository = $shipRepository;
    }

    /**
     * @Route("/ship-chassis/details/{slug}", name="ship_chassis_details", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $shipChassis = $this->shipChassisRepository->findOneBy(['slug' => $slug]);
        if ($shipChassis === null) {
            throw new NotFoundHttpException('Manufacturer not found.');
        }
        $ships = $this->shipRepository->findBy(['chassis' => $shipChassis]);

        return $this->render('ship_chassis/details.html.twig', [
            'chassis' => $shipChassis,
            'ships' => $ships,
        ]);
    }
}
