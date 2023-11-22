<?php

namespace App\EventListener;

use App\Repository\CategoryRepository;
use App\Repository\MineralRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapSubscriber implements EventSubscriberInterface
{

    private $mineralRepository;

    private $categoryRepository;

    public function __construct(MineralRepository $mineralRepository, CategoryRepository $categoryRepository)
    {
        $this->mineralRepository = $mineralRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    /**
     * @param SitemapPopulateEvent $event
     */
    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerMineralPagesUrls($event->getUrlContainer(), $event->getUrlGenerator());
        $this->registerCategoriesUrls($event->getUrlContainer(), $event->getUrlGenerator());
    }

    /**
     * @param UrlContainerInterface $urls
     * @param UrlGeneratorInterface $router
     */
    public function registerMineralPagesUrls(
        UrlContainerInterface $urls, 
        UrlGeneratorInterface $router
        ): void
    {
        $minerals = $this->mineralRepository->findAll();

        foreach ($minerals as $mineral) {
            $urls->addUrl(
                new UrlConcrete(
                    $router->generate(
                        'show_mineral',
                        ['slug' => $mineral->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'mineral'
            );
        }
    }

    /**
     * @param UrlContainerInterface $urls
     * @param UrlGeneratorInterface $router
     */
    public function registerCategoriesUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        $categories = $this->categoryRepository->findAll();

        foreach ($categories as $category) {
            $urls->addUrl(
                new UrlConcrete(
                    $router->generate(
                        'show_category',
                        ['slug' => $category->getSlug()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'category'
            );
        }
    }
}