<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class WebTestCase extends BaseWebTestCase
{
    use ReloadDatabaseTrait;

    protected KernelBrowser $client;
    protected ManagerRegistry $doctrine;

    protected static function createClient(array $options = [], array $server = [])
    {
        static::bootKernel($options);

        $client = static::$kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->doctrine = static::$container->get(ManagerRegistry::class);
        $this->doctrine->getManager()->clear();
    }

    protected function logIn(User $user): void
    {
        $session = static::$container->get('session');

        $token = new OAuthToken(md5(mt_rand()), $user->getRoles());
        $token->setResourceOwnerName('discord');
        $token->setUser($user);
        $token->setAuthenticated(true);
        $tokenStorage = static::$container->get('security.token_storage');
        $tokenStorage->setToken($token);
        $session->set('_security_main', serialize($token));
        $session->save();

        $this->client->getCookieJar()->expire($session->getName());
        $this->client->getCookieJar()->flushExpiredCookies();
        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }

    protected function debugHtml(): void
    {
        file_put_contents(__DIR__.'/../var/debug.html', $this->client->getResponse()->getContent());
    }
}
