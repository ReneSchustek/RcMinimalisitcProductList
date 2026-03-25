<?php declare(strict_types=1);

namespace Ruhrcoder\RcMinimalisticProductList\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Liest die Custom-Field-Einstellung der aktuellen Navigationskategorie
 * und hängt sie als Extension am SearchResult ein.
 * Funktioniert sowohl beim initialen Seitenaufruf (NavigationController)
 * als auch beim AJAX-Listing-Reload via /widgets/cms/navigation/{id}
 * (CmsController::category) – dort ist page.header.navigation.active nicht verfügbar.
 */
final class ListingLayoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository $categoryRepository,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ProductListingResultEvent::class => 'onListingResult',
        ];
    }

    public function onListingResult(ProductListingResultEvent $event): void
    {
        $navigationId = $event->getRequest()->attributes->get('navigationId');
        if (!$navigationId) {
            return;
        }

        $criteria = new Criteria([$navigationId]);
        $category = $this->categoryRepository
            ->search($criteria, $event->getContext())
            ->first();

        if (!$category) {
            return;
        }

        $customFields = $category->getCustomFields() ?? [];
        $active = (bool)($customFields['rc_show_minimalistic_productlist'] ?? false);

        $event->getResult()->addExtension('rcMinimalisticLayout', new ArrayStruct([
            'active' => $active,
        ]));
    }
}
