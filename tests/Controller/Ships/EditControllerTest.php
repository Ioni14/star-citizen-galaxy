<?php

namespace App\Tests\Controller\Ships;

use App\Entity\Ship;
use App\Entity\User;
use App\Service\LockHelper;
use App\Tests\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class EditControllerTest extends WebTestCase
{
    /**
     * @group functional
     * @group ships
     */
    public function testUpdateNotAuth(): void
    {
        $this->client->request('GET', '/ships/edit/aurora-es');

        $this->assertSame(302, $this->client->getResponse()->getStatusCode()); // redirect homepage
    }

    /**
     * @group functional
     * @group ships
     */
    public function testUpdateUserAuth(): void
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'User1']); // ROLE_USER
        $this->logIn($user);
        $this->client->request('GET', '/ships/edit/aurora-es');

        $this->assertSame(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @group functional
     * @group ships
     */
    public function testUpdateModo(): void
    {
        /** @var Ship $ship */
        $ship = $this->doctrine->getRepository(Ship::class)->findOneBy(['slug' => 'aurora-es']);
        static::$container->get(LockHelper::class)->releaseLock($ship);

        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy(['nickname' => 'Moderator1']); // ROLE_MODERATOR
        $this->logIn($user);
        $crawler = $this->client->request('GET', '/ships/edit/aurora-es');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form([
            'ship_form[name]' => 'MyShip',
            'ship_form[chassis]' => '62994bb6-f94f-4226-b144-d0e21c85b04e', // Aurora
            'ship_form[height]' => '1.00',
            'ship_form[length]' => '2.00',
            'ship_form[beam]' => '3.00',
            'ship_form[minCrew]' => '2',
            'ship_form[maxCrew]' => '3',
            'ship_form[size]' => Ship::SIZE_CAPITAL,
            'ship_form[cargoCapacity]' => '0.75',
            'ship_form[pledgeCost]' => '10.50',
            'ship_form[readyStatus]' => Ship::READY_STATUS_CONCEPT,
            'ship_form[career]' => '4deb2e25-5ac4-40b7-8bf1-34679bd2352d', // Combat
            'ship_form[pledgeUrl]' => 'https://robertsspaceindustries.com/pledges/ships/rsi-aurora/Aurora-MR',
        ]);
        $values = $form->getPhpValues();
        $values['ship_form']['holdedShips'][0]['ship'] = '62994bb6-f94f-4226-b144-d0e21c85b04e'; // Aurora ES
        $values['ship_form']['holdedShips'][0]['quantity'] = '2'; // Aurora ES
        $values['ship_form']['loanerShips'][0]['ship'] = '63646ba8-1354-4279-be6a-364aa21c87a1'; // Aurora LN
        $values['ship_form']['loanerShips'][0]['quantity'] = '1'; // Aurora ES
        $values['ship_form']['roles'][0] = 'c3a73970-16d0-4809-a02f-a2ba58ae2beb'; // Medical
        unset($values['ship_form']['roles'][1]);

        $finder = new Finder();
        /** @var SplFileInfo $file */
        foreach ($finder->in(__DIR__.'/../../../public/uploads/ships/')->files()->name('aurora-es.*') as $file) {
            @unlink($file->getRealPath());
        }
        $form['ship_form[picture]']->upload(__DIR__.'/dependencies/uploads/my-ship_picture.jpeg');
        $form['ship_form[thumbnail]']->upload(__DIR__.'/dependencies/uploads/my-ship_thumbnail.jpeg');

        $fs = new Filesystem();
        $this->assertFalse($fs->exists(__DIR__.'/../../../public/uploads/ships/thumbnails/aurora-es.d934ae6f.jpeg'), 'The thumbnail exists.');
        $this->assertFalse($fs->exists(__DIR__.'/../../../public/uploads/ships/pictures/aurora-es.e05fa786.jpeg'), 'The picture exists.');

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->debugHtml();
        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('The ship has been successfully modified.', $crawler->filter('.alert.alert-success')->text(null, false));

        $this->assertTrue($fs->exists(__DIR__.'/../../../public/uploads/ships/thumbnails/aurora-es.d934ae6f.jpeg'), 'The thumbnail does not exist.');
        $this->assertTrue($fs->exists(__DIR__.'/../../../public/uploads/ships/pictures/aurora-es.e05fa786.jpeg'), 'The picture does not exist.');

        /** @var Ship $ship */
        $ship = $this->doctrine->getRepository(Ship::class)->findOneBy(['slug' => 'aurora-es']);
        $this->assertNotNull($ship);
        $this->assertSame('MyShip', $ship->getName());
        $this->assertSame('62994bb6-f94f-4226-b144-d0e21c85b04e', $ship->getChassis()->getId()->toString());
        $this->assertCount(1, $ship->getHoldedShips());
        $this->assertSame('62994bb6-f94f-4226-b144-d0e21c85b04e', $ship->getHoldedShips()[0]->getHolded()->getId()->toString());
        $this->assertSame(2, $ship->getHoldedShips()[0]->getQuantity());
        $this->assertSame('63646ba8-1354-4279-be6a-364aa21c87a1', $ship->getLoanerShips()[0]->getLoaned()->getId()->toString());
        $this->assertSame(1, $ship->getLoanerShips()[0]->getQuantity());
        $this->assertSame(1.0, $ship->getHeight());
        $this->assertSame(2.0, $ship->getLength());
        $this->assertSame(3.0, $ship->getBeam());
        $this->assertSame(Ship::SIZE_CAPITAL, $ship->getSize());
        $this->assertSame(0.75, $ship->getCargoCapacity());
        $this->assertSame(1050, $ship->getPledgeCost());
        $this->assertSame(Ship::READY_STATUS_CONCEPT, $ship->getReadyStatus());
        $this->assertSame('4deb2e25-5ac4-40b7-8bf1-34679bd2352d', $ship->getCareer()->getId()->toString());
        $this->assertCount(1, $ship->getRoles());
        $this->assertSame('c3a73970-16d0-4809-a02f-a2ba58ae2beb', $ship->getRoles()[0]->getId()->toString());
        $this->assertSame('https://robertsspaceindustries.com/pledges/ships/rsi-aurora/Aurora-MR', $ship->getPledgeUrl());
    }
}
