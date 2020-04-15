<?php

namespace App\Tests\Controller\Ships;

use App\Entity\User;
use App\Tests\WebTestCase;

class ListControllerTest extends WebTestCase
{
    /**
     * @group functional
     * @group ships
     */
    public function testList(): void
    {
        $crawler = $this->client->request('GET', '/ships');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter('table#js-ships-datatable a:contains("Aurora ES")'), 'Aurora ES row not found.');
        $this->assertCount(1, $crawler->filter('table#js-ships-datatable a:contains("Aurora LN")'), 'Aurora LN row not found.');

        $this->assertCount(0, $crawler->filter('table#js-ships-datatable a[href="/ships/edit/aurora-es"]:contains("Edit")'), 'Aurora ES Edit row found.');
        $this->assertCount(0, $crawler->filter('table#js-ships-datatable a[href="/ships/edit/aurora-ln"]:contains("Edit")'), 'Aurora ES Edit row found.');
        $this->assertCount(0, $crawler->filter('table#js-ships-datatable form[action="/ships/delete/62994bb6-f94f-4226-b144-d0e21c85b04e"] button:contains("Delete")'), 'Aurora ES Delete row found.');
        $this->assertCount(0, $crawler->filter('table#js-ships-datatable form[action="/ships/delete/63646ba8-1354-4279-be6a-364aa21c87a1"] button:contains("Delete")'), 'Aurora LN Delete row found.');
    }

    /**
     * @group functional
     * @group ships
     */
    public function testListModo(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'Moderator1']); // ROLE_MODERATOR
        $this->logIn($user);
        $crawler = $this->client->request('GET', '/ships');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter('table#js-ships-datatable a[href="/ships/edit/aurora-es"]:contains("Edit")'), 'Aurora ES Edit row not found.');
        $this->assertCount(1, $crawler->filter('table#js-ships-datatable a[href="/ships/edit/aurora-ln"]:contains("Edit")'), 'Aurora LN Edit row not found.');
        $this->assertCount(0, $crawler->filter('table#js-ships-datatable form[action="/ships/delete/62994bb6-f94f-4226-b144-d0e21c85b04e"] button:contains("Delete")'), 'Aurora ES Delete row found.');
        $this->assertCount(0, $crawler->filter('table#js-ships-datatable form[action="/ships/delete/63646ba8-1354-4279-be6a-364aa21c87a1"] button:contains("Delete")'), 'Aurora LN Delete row found.');
    }

    /**
     * @group functional
     * @group ships
     */
    public function testListAdmin(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'Admin1']); // ROLE_ADMIN
        $this->logIn($user);
        $crawler = $this->client->request('GET', '/ships');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter('table#js-ships-datatable a[href="/ships/edit/aurora-es"]:contains("Edit")'), 'Aurora ES Edit row not found.');
        $this->assertCount(1, $crawler->filter('table#js-ships-datatable a[href="/ships/edit/aurora-ln"]:contains("Edit")'), 'Aurora LN Edit row not found.');
        $this->assertCount(1, $crawler->filter('table#js-ships-datatable form[action="/ships/delete/62994bb6-f94f-4226-b144-d0e21c85b04e"] button:contains("Delete")'), 'Aurora ES Delete row not found.');
        $this->assertCount(1, $crawler->filter('table#js-ships-datatable form[action="/ships/delete/63646ba8-1354-4279-be6a-364aa21c87a1"] button:contains("Delete")'), 'Aurora LN Delete row not found.');
    }
}
