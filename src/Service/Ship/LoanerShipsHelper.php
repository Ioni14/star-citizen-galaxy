<?php

namespace App\Service\Ship;

use App\Entity\LoanerShip;
use App\Entity\Ship;
use App\Form\Dto\ShipDto;
use Doctrine\ORM\EntityManagerInterface;

class LoanerShipsHelper
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function computeLoanerShips(Ship $ship, ShipDto $shipDto): void
    {
        // remove and change quantities
        foreach ($ship->getLoanerShips() as $loanerShip) {
            $found = false;
            foreach ($shipDto->loanerShips as $loanerShipDto) {
                if ($loanerShipDto->ship->getId()->equals($loanerShip->getLoaned()->getId())) {
                    $found = true;
                    $loanerShip->setQuantity($loanerShipDto->quantity);
                    break;
                }
            }
            if (!$found) {
                $this->entityManager->remove($loanerShip);
                $ship->removeLoaner($loanerShip);
            }
        }
        // add new loaner ships
        foreach ($shipDto->loanerShips as $loanerShipDto) {
            $found = false;
            foreach ($ship->getLoanerShips() as $loanerShip) {
                if ($loanerShipDto->ship->getId()->equals($loanerShip->getLoaned()->getId())) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $ship->addLoaner(new LoanerShip($ship, $loanerShipDto->ship, $loanerShipDto->quantity));
            }
        }
    }
}
