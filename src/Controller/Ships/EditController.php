<?php

namespace App\Controller\Ships;

use App\Entity\HoldedShip;
use App\Entity\Ship;
use App\Form\Dto\HoldedShipDto;
use App\Form\Dto\ShipDto;
use App\Form\Type\ShipForm;
use App\Repository\ShipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EditController extends AbstractController
{
    private ShipRepository $shipRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ShipRepository $shipRepository, EntityManagerInterface $entityManager)
    {
        $this->shipRepository = $shipRepository;
        $this->entityManager = $entityManager;
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

        $shipDto = new ShipDto(
            $ship->getName(),
            $ship->getChassis(),
            array_map(static function (HoldedShip $holdedShip): HoldedShipDto {
                return new HoldedShipDto($holdedShip->getHolded(), $holdedShip->getQuantity());
            }, $ship->getHoldedShips()->toArray()),
            $ship->getHeight(),
            $ship->getLength(),
            $ship->getMinCrew(),
            $ship->getMaxCrew(),
            $ship->getSize(),
            $ship->getReadyStatus(),
            $ship->getFocus(),
            $ship->getPledgeUrl(),
            $ship->getPictureUri(),
            $ship->getThumbnailUri(),
            $ship->getPrice(),
        );
        $form = $this->createForm(ShipForm::class, $shipDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ship
                ->setName($shipDto->name)
                ->setChassis($shipDto->chassis)
                ->setHeight($shipDto->height)
                ->setLength($shipDto->length)
                ->setMinCrew($shipDto->minCrew)
                ->setMaxCrew($shipDto->maxCrew)
                ->setSize($shipDto->size)
                ->setReadyStatus($shipDto->readyStatus)
                ->setFocus($shipDto->focus)
                ->setPledgeUrl($shipDto->pledgeUrl)
                ->setPrice($shipDto->price);

//            $ship->setPictureUri();

            $this->computeHoldedShips($ship, $shipDto);

            $this->entityManager->flush();

//            return $this->redirectToRoute('ships_list', ['slug' => $ship->getSlug()]);
        }

        return $this->render('ships/edit.html.twig', [
            'ship' => $ship,
            'form' => $form->createView(),
        ]);
    }

    private function computeHoldedShips(Ship $ship, ShipDto $shipDto): void
    {
        // remove and change quantities
        foreach ($ship->getHoldedShips() as $holdedShip) {
            $found = false;
            foreach ($shipDto->holdedShips as $holdedShipDto) {
                if ($holdedShipDto->ship->getId()->equals($holdedShip->getHolded()->getId())) {
                    $found = true;
                    $holdedShip->setQuantity($holdedShipDto->quantity);
                    break;
                }
            }
            if (!$found) {
                $this->entityManager->remove($holdedShip);
                $ship->removeHoldedShip($holdedShip);
            }
        }
        // add new holded ships
        foreach ($shipDto->holdedShips as $holdedShipDto) {
            $found = false;
            foreach ($ship->getHoldedShips() as $holdedShip) {
                if ($holdedShipDto->ship->getId()->equals($holdedShip->getHolded()->getId())) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $ship->addHoldedShip(new HoldedShip($ship, $holdedShipDto->ship, $holdedShipDto->quantity));
            }
        }
    }
}
