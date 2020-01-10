<?php

namespace App\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueFieldValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UniqueField $constraint
     * @param mixed       $value
     */
    public function validate($value, Constraint $constraint): void
    {
        $repo = $this->entityManager->getRepository($constraint->entityClass);
        /** @var UserInterface $foundUser */
        $entity = $repo->findOneBy([$constraint->field => $value]);
        if ($entity === null) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
