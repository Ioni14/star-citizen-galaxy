<?php

namespace App\Controller\Ships;

use App\Entity\Ship;
use App\Form\Dto\ShipDto;
use App\Form\Type\ShipForm;
use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    private ShipRepository $shipRepository;

    public function __construct(ShipRepository $shipRepository)
    {
        $this->shipRepository = $shipRepository;
    }

    /**
     * @Route("/ships/edit/{slug}", name="ships_edit", methods={"GET","POST"})
     */
    public function __invoke(Request $request, string $slug): Response
    {
        /** @var Ship $ship */
        $ship = $this->shipRepository->findOneBy(['slug' => $slug]);
        if ($ship === null) {
            throw new NotFoundHttpException('Ship not found.');
        }

        $shipDto = new ShipDto();
        $form = $this->createForm(ShipForm::class, $shipDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            return $this->redirectToRoute('ships_show', ['slug' => $ship->getSlug()]);
        }

        return $this->render('ships/edit.html.twig', [
            'ship' => $ship,
            'form' => $form->createView(),
        ]);
    }
}
