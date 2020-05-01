<?php

namespace App\Tests\Controller\Ships;

use App\Entity\Ship;
use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CreateControllerTest extends WebTestCase
{
    /**
     * @group functional
     * @group ships
     */
    public function testCreateNotAuth(): void
    {
        $this->client->request('GET', '/ships/create');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode()); // redirect homepage
    }

    /**
     * @group functional
     * @group ships
     */
    public function testCreateUserAuth(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'User1']); // ROLE_USER
        $this->logIn($user);
        $this->client->request('GET', '/ships/create');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group functional
     * @group ships
     */
    public function testCreateModo(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'Moderator1']); // ROLE_MODERATOR
        $this->logIn($user);
        $crawler = $this->client->request('GET', '/ships/create');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Create')->form([
            'ship_form[name]' => 'MyShip',
            'ship_form[chassis]' => '62994bb6-f94f-4226-b144-d0e21c85b04e', // Aurora
            'ship_form[height]' => '2.337',
            'ship_form[length]' => '57.54',
            'ship_form[beam]' => '8.00',
            'ship_form[minCrew]' => '1',
            'ship_form[maxCrew]' => '3',
            'ship_form[size]' => Ship::SIZE_SMALL,
            'ship_form[cargoCapacity]' => '1.75',
            'ship_form[pledgeCost]' => '55.55',
            'ship_form[readyStatus]' => Ship::READY_STATUS_FLIGHT_READY,
            'ship_form[career]' => '4deb2e25-5ac4-40b7-8bf1-34679bd2352d', // Combat
            'ship_form[pledgeUrl]' => 'https://robertsspaceindustries.com/pledges/ships/rsi-aurora/Aurora-MR',
        ]);
        $values = $form->getPhpValues();
        $values['ship_form']['holdedShips'][0]['ship'] = '62994bb6-f94f-4226-b144-d0e21c85b04e'; // Aurora ES
        $values['ship_form']['holdedShips'][0]['quantity'] = '2'; // Aurora ES
        $values['ship_form']['loanerShips'][0]['ship'] = '63646ba8-1354-4279-be6a-364aa21c87a1'; // Aurora LN
        $values['ship_form']['loanerShips'][0]['quantity'] = '1'; // Aurora ES
        $values['ship_form']['roles'][0] = 'c3a73970-16d0-4809-a02f-a2ba58ae2beb'; // Medical
        $values['ship_form']['roles'][1] = 'd70a9024-b835-4707-b6f0-5e7bf58d999d'; // Pathfinding

        $finder = new Finder();
        /** @var SplFileInfo $file */
        foreach ($finder->in(__DIR__.'/../../../public/uploads/ships/')->files()->name('myship.*') as $file) {
            @unlink($file->getRealPath());
        }
        $form['ship_form[picture]']->upload(__DIR__.'/dependencies/uploads/my-ship_picture.jpeg');
        $form['ship_form[thumbnail]']->upload(__DIR__.'/dependencies/uploads/my-ship_thumbnail.jpeg');

        $fs = new Filesystem();
        $this->assertFalse($fs->exists(__DIR__.'/../../../public/uploads/ships/thumbnails/myship.d934ae6f.jpeg'), 'The thumbnail exists.');
        $this->assertFalse($fs->exists(__DIR__.'/../../../public/uploads/ships/pictures/myship.e05fa786.jpeg'), 'The picture exists.');

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('The ship has been successfully created.', $crawler->filter('.alert.alert-success')->text(null, false));

        $this->assertTrue($fs->exists(__DIR__.'/../../../public/uploads/ships/thumbnails/myship.d934ae6f.jpeg'), 'The thumbnail does not exist.');
        $this->assertTrue($fs->exists(__DIR__.'/../../../public/uploads/ships/pictures/myship.e05fa786.jpeg'), 'The picture does not exist.');

        /** @var Ship $ship */
        $ship = $this->doctrine->getRepository(Ship::class)->findOneBy(['slug' => 'myship']);
        $this->assertNotNull($ship);
        $this->assertSame('MyShip', $ship->getName());
        $this->assertSame('62994bb6-f94f-4226-b144-d0e21c85b04e', $ship->getChassis()->getId()->toString());
        $this->assertCount(1, $ship->getHoldedShips());
        $this->assertSame('62994bb6-f94f-4226-b144-d0e21c85b04e', $ship->getHoldedShips()[0]->getHolded()->getId()->toString());
        $this->assertSame(2, $ship->getHoldedShips()[0]->getQuantity());
        $this->assertSame('63646ba8-1354-4279-be6a-364aa21c87a1', $ship->getLoanerShips()[0]->getLoaned()->getId()->toString());
        $this->assertSame(1, $ship->getLoanerShips()[0]->getQuantity());
        $this->assertSame(2.34, $ship->getHeight());
        $this->assertSame(57.54, $ship->getLength());
        $this->assertSame(8.0, $ship->getBeam());
        $this->assertSame(Ship::SIZE_SMALL, $ship->getSize());
        $this->assertSame(1.75, $ship->getCargoCapacity());
        $this->assertSame(5555, $ship->getPledgeCost());
        $this->assertSame(Ship::READY_STATUS_FLIGHT_READY, $ship->getReadyStatus());
        $this->assertSame('4deb2e25-5ac4-40b7-8bf1-34679bd2352d', $ship->getCareer()->getId()->toString());
        $this->assertCount(2, $ship->getRoles());
        $this->assertSame('c3a73970-16d0-4809-a02f-a2ba58ae2beb', $ship->getRoles()[0]->getId()->toString());
        $this->assertSame('d70a9024-b835-4707-b6f0-5e7bf58d999d', $ship->getRoles()[1]->getId()->toString());
        $this->assertSame('https://robertsspaceindustries.com/pledges/ships/rsi-aurora/Aurora-MR', $ship->getPledgeUrl());
        $this->assertSame('myship.e05fa786.jpeg', $ship->getPicturePath());
        $this->assertSame('myship.d934ae6f.jpeg', $ship->getThumbnailPath());
    }
}
