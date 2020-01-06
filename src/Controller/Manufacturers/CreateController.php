<?php

namespace App\Controller\Manufacturers;

use App\Entity\Manufacturer;
use App\Form\Dto\ManufacturerDto;
use App\Form\Type\ManufacturerForm;
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
     * @Route("/manufacturers/create", name="manufacturers_create", methods={"GET","POST"})
     * @Security("is_granted('ROLE_MODERATOR')")
     */
    public function __invoke(Request $request): Response
    {
        $manufacturerDto = new ManufacturerDto();
        $form = $this->createForm(ManufacturerForm::class, $manufacturerDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manufacturer = (new Manufacturer(Uuid::uuid4()))
                ->setName($manufacturerDto->name)
                ->setCode($manufacturerDto->code);

            $this->entityManager->persist($manufacturer);
            $this->entityManager->flush();

            $this->addFlash('success', 'The manufacturer has been successfully created.');

            return $this->redirectToRoute('manufacturers_edit', ['slug' => $manufacturer->getSlug()]);
        }

        return $this->render('manufacturers/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
