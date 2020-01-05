<?php

namespace App\Controller\Manufacturers;

use App\Repository\ManufacturerRepository;
use App\Repository\ShipChassisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DetailsController extends AbstractController
{
    private ManufacturerRepository $manufacturerRepository;
    private ShipChassisRepository $shipChassisRepository;

    public function __construct(
        ManufacturerRepository $manufacturerRepository,
        ShipChassisRepository $shipChassisRepository
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->shipChassisRepository = $shipChassisRepository;
    }

    /**
     * @Route("/manufacturers/details/{slug}", name="manufacturers_details", methods={"GET"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        $manufacturer = $this->manufacturerRepository->findOneBy(['slug' => $slug]);
        if ($manufacturer === null) {
            throw new NotFoundHttpException('Manufacturer not found.');
        }
        $chassis = $this->shipChassisRepository->findBy(['manufacturer' => $manufacturer]);

        return $this->render('manufacturers/details.html.twig', [
            'manufacturer' => $manufacturer,
            'manufacturerChassis' => $chassis,
        ]);
    }
}
