<?php

namespace App\Controller\ShipsChassis;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    /**
     * @Route("/ships-chassis", name="ships_chassis_list", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        return $this->render('ships_chassis/list.html.twig');
    }
}
