<?php

namespace App\Tests\Controller\Ships;

use App\Entity\Ship;
use App\Entity\User;
use App\Tests\WebTestCase;

class DeleteControllerTest extends WebTestCase
{
    /**
     * @group functional
     * @group ships
     */
    public function testDeleteNotAuth(): void
    {
        $this->client->request('POST', '/ships/delete/62994bb6-f94f-4226-b144-d0e21c85b04e');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode()); // redirect homepage

        /** @var Ship $ship */
        $ship = $this->doctrine->getRepository(Ship::class)->findOneBy(['id' => '62994bb6-f94f-4226-b144-d0e21c85b04e']);
        $this->assertNotNull($ship);
    }

    /**
     * @group functional
     * @group ships
     */
    public function testDeleteUserAuth(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'User1']); // ROLE_USER
        $this->logIn($user);
        $this->client->request('POST', '/ships/delete/62994bb6-f94f-4226-b144-d0e21c85b04e');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group functional
     * @group ships
     */
    public function testDeleteModoAuth(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'Moderator1']); // ROLE_USER
        $this->logIn($user);
        $this->client->request('POST', '/ships/delete/62994bb6-f94f-4226-b144-d0e21c85b04e');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group functional
     * @group ships
     */
    public function testUpdateModo(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'Admin1']); // ROLE_ADMIN
        $this->logIn($user);
        $this->client->request('POST', '/ships/delete/62994bb6-f94f-4226-b144-d0e21c85b04e');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        /** @var Ship $ship */
        $ship = $this->doctrine->getRepository(Ship::class)->findOneBy(['id' => '62994bb6-f94f-4226-b144-d0e21c85b04e']);
        $this->assertNull($ship);
    }
}
