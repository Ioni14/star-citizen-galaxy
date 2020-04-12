<?php

namespace App\Controller;

use App\Entity\Manufacturer;
use App\Entity\Ship;
use App\Entity\ShipChassis;
use App\Repository\ManufacturerRepository;
use App\Repository\ShipChassisRepository;
use App\Repository\ShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class SitemapController extends AbstractController
{
    private ShipRepository $shipRepository;
    private ShipChassisRepository $shipChassisRepository;
    private ManufacturerRepository $manufacturerRepository;
    private CacheInterface $cache;
    private UrlGeneratorInterface $urlGenerator;
    private SerializerInterface $serializer;

    public function __construct(
        ShipRepository $shipRepository,
        ShipChassisRepository $shipChassisRepository,
        ManufacturerRepository $manufacturerRepository,
        CacheInterface $cache,
        UrlGeneratorInterface $urlGenerator,
        SerializerInterface $serializer
    ) {
        $this->shipRepository = $shipRepository;
        $this->shipChassisRepository = $shipChassisRepository;
        $this->manufacturerRepository = $manufacturerRepository;
        $this->cache = $cache;
        $this->urlGenerator = $urlGenerator;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/sitemap.xml", name="sitemap", methods={"GET"})
     */
    public function __invoke(Request $request): Response
    {
        $xmlSitemap = $this->cache->get('sitemap', function (CacheItem $cacheItem) {
            $cacheItem->expiresAfter(3600);

            $sitemap = [
                '@xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
                'url' => [
                    [
                        'loc' => $this->urlGenerator->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                    [
                        'loc' => $this->urlGenerator->generate('ships_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'changefreq' => 'weekly',
                    ],
                    [
                        'loc' => $this->urlGenerator->generate('ship_chassis_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'changefreq' => 'weekly',
                    ],
                    [
                        'loc' => $this->urlGenerator->generate('manufacturers_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
                        'changefreq' => 'weekly',
                    ],
                ],
            ];

            /** @var Ship[] $ships */
            $ships = $this->shipRepository->findAll();
            foreach ($ships as $ship) {
                $sitemap['url'][] = [
                    'loc' => $this->urlGenerator->generate('ships_details', ['slug' => $ship->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'changefreq' => 'weekly',
                    'lastmod' => $ship->getUpdatedAt()->format('c'),
                ];
            }
            /** @var ShipChassis[] $chassis */
            $chassis = $this->shipChassisRepository->findAll();
            foreach ($chassis as $chassi) {
                $sitemap['url'][] = [
                    'loc' => $this->urlGenerator->generate('ship_chassis_details', ['slug' => $chassi->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'changefreq' => 'monthly',
                    'lastmod' => $chassi->getUpdatedAt()->format('c'),
                ];
            }
            /** @var Manufacturer[] $manufacturers */
            $manufacturers = $this->manufacturerRepository->findAll();
            foreach ($manufacturers as $manufacturer) {
                $sitemap['url'][] = [
                    'loc' => $this->urlGenerator->generate('manufacturers_details', ['slug' => $manufacturer->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'changefreq' => 'monthly',
                    'lastmod' => $manufacturer->getUpdatedAt()->format('c'),
                ];
            }

            return $this->serializer->serialize($sitemap, 'xml', ['xml_root_node_name' => 'urlset']);
        });

        return new Response($xmlSitemap, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
