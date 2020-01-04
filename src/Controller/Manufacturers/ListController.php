<?php

namespace App\Controller\Manufacturers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @Route("/manufacturers", name="manufacturers_list", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        return $this->render('manufacturers/list.html.twig');
    }
}
