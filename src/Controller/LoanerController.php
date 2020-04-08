<?php

namespace App\Controller;

use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoanerController extends AbstractController
{
    private ShipRepository $shipRepository;

    public function __construct(
        ShipRepository $shipRepository
    ) {
        $this->shipRepository = $shipRepository;
    }

    /**
     * @Route("/loaners", name="loaners", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $loanerShips = $this->shipRepository->findLoanerShips();

        return $this->render('loaners.html.twig', [
            'loanerShips' => $loanerShips,
        ]);
    }
}
