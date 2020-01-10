<?php

namespace App\Controller\ShipChassis;

use App\Entity\ShipChassis;
use App\Form\Dto\ShipChassisDto;
use App\Form\Type\ShipChassisForm;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/ship-chassis/create", name="ship_chassis_create", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request): Response
    {
        $shipChassisDto = new ShipChassisDto();
        $form = $this->createForm(ShipChassisForm::class, $shipChassisDto, [
            'mode' => ShipChassisForm::MODE_CREATE,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $shipChassis = (new ShipChassis(Uuid::uuid4()))
                ->setName($shipChassisDto->name)
                ->setManufacturer($shipChassisDto->manufacturer);

            $this->entityManager->persist($shipChassis);
            $this->entityManager->flush();

            $this->addFlash('success', 'The Ship chassis has been successfully created.');

            return $this->redirectToRoute('ship_chassis_details', ['slug' => $shipChassis->getSlug()]);
        }

        return $this->render('ship_chassis/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
