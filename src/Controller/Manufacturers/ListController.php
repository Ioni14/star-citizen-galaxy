<?php

namespace App\Controller\Manufacturers;

use App\Repository\ManufacturerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    private ManufacturerRepository $manufacturerRepository;

    public function __construct(ManufacturerRepository $manufacturerRepository)
    {
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * @Route("/manufacturers", name="manufacturers_list", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $manufacturers = $this->manufacturerRepository->findAll();

        return $this->render('manufacturers/list.html.twig', [
            'manufacturers' => $manufacturers,
        ]);
    }
}
