<?php

namespace App\Controller\ShipChassis;

use App\Entity\ShipChassis;
use App\Form\Dto\ShipChassisDto;
use App\Form\Type\ShipChassisForm;
use App\Repository\ShipChassisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    private ShipChassisRepository $shipChassisRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ShipChassisRepository $shipChassisRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->shipChassisRepository = $shipChassisRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/ship-chassis/edit/{slug}", name="ship_chassis_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request, string $slug): Response
    {
        /** @var ShipChassis $shipChassis */
        $shipChassis = $this->shipChassisRepository->findOneBy(['slug' => $slug]);
        if ($shipChassis === null) {
            throw new NotFoundHttpException('Ship chassis not found.');
        }

        $shipChassisDto = new ShipChassisDto(
            $shipChassis->getName(),
            $shipChassis->getManufacturer(),
        );
        $form = $this->createForm(ShipChassisForm::class, $shipChassisDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $shipChassis
                ->setName($shipChassisDto->name)
                ->setManufacturer($shipChassisDto->manufacturer);

            $this->entityManager->flush();

            $this->addFlash('success', 'The Ship chassis has been successfully modified.');

            return $this->redirectToRoute('ship_chassis_details', ['slug' => $shipChassis->getSlug()]);
        }

        return $this->render('ship_chassis/edit.html.twig', [
            'chassis' => $shipChassis,
            'form' => $form->createView(),
        ]);
    }
}
