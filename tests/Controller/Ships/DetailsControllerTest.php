<?php

namespace App\Tests\Controller\Ships;

use App\Entity\User;
use App\Tests\WebTestCase;

class DetailsControllerTest extends WebTestCase
{
    /**
     * @group functional
     * @group ships
     */
    public function testDetails(): void
    {
        $crawler = $this->client->request('GET', '/ships/details/aurora-es');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('dt:contains("Name") + dd:contains("Aurora ES")'), 'ship name not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Chassis") + dd:contains("Aurora")'), 'ship chassis not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Manufacturer") + dd:contains("Roberts Space Industries")'), 'ship manufacturer not found.');
        $this->assertCount(1, $crawler->filterXPath('//dt[contains(text(), "Holded ships")]//following-sibling::dd//a[@href="/ships/details/aurora-ln" and contains(text(), "Aurora LN")]//following-sibling::span[contains(text(), "x1")]'), 'ship holded ships not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Height") + dd:contains("4.00")'), 'ship height not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Length") + dd:contains("18.00")'), 'ship length not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Beam") + dd:contains("8.00")'), 'ship beam not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Min crew") + dd:contains("1")'), 'ship min crew not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Max crew") + dd:contains("1")'), 'ship max crew not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Size") + dd:contains("Small")'), 'ship size not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Cargo capacity (SCU)") + dd:contains("2")'), 'ship cargo capacity not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Pledge cost") + dd:contains("50.00")'), 'ship pledge cost not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Ready status") + dd:contains("Flight ready")'), 'ship ready status not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Career") + dd:contains("Combat")'), 'ship career not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Roles") + dd:contains("Medical")'), 'ship role not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Roles") + dd:contains("Pathfinding")'), 'ship role not found.');
        $this->assertCount(1, $crawler->filter('dt:contains("Pledge URL") + dd:contains("https://robertsspaceindustries.com/pledge/ships/rsi-aurora/Aurora-ES")'), 'ship pledge URL not found.');

        $this->assertCount(0, $crawler->filter('a.btn[href="/ships/edit/aurora-es"]:contains("Edit")'), 'Edit button not found.');
    }

    /**
     * @group functional
     * @group ships
     */
    public function testDetailsModo(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'Moderator1']); // ROLE_MODERATOR
        $this->logIn($user);
        $crawler = $this->client->request('GET', '/ships/details/aurora-es');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertCount(2, $crawler->filter('a.btn[href="/ships/edit/aurora-es"]:contains("Edit")'), 'Edit button not found.');
    }
}
