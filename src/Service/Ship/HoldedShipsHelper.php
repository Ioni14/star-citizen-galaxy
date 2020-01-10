<?php

namespace App\Service\Ship;

use App\Entity\HoldedShip;
use App\Entity\Ship;
use App\Form\Dto\ShipDto;
use Doctrine\ORM\EntityManagerInterface;

class HoldedShipsHelper
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function computeHoldedShips(Ship $ship, ShipDto $shipDto): void
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
