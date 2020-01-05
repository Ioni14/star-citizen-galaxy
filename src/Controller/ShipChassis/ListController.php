<?php

namespace App\Controller\ShipChassis;

use App\Repository\ShipChassisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    private ShipChassisRepository $chassisRepository;

    public function __construct(ShipChassisRepository $chassisRepository)
    {
        $this->chassisRepository = $chassisRepository;
    }

    /**
     * @Route("/ship-chassis", name="ship_chassis_list", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $chassis = $this->chassisRepository->findChassisJoinedManufacturer();

        return $this->render('ship_chassis/list.html.twig', [
            'shipChassis' => $chassis,
        ]);
    }
}
