<?php

declare(strict_types=1);

namespace Ruhrcoder\RcMinimalisticProductList\Tests\Unit\Subscriber;

use PHPUnit\Framework\TestCase;
use Ruhrcoder\RcMinimalisticProductList\Subscriber\ListingLayoutSubscriber;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\HttpFoundation\Request;

final class ListingLayoutSubscriberTest extends TestCase
{
    public function testGetSubscribedEventsReturnsCorrectMapping(): void
    {
        $events = ListingLayoutSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(ProductListingResultEvent::class, $events);
        self::assertSame('onListingResult', $events[ProductListingResultEvent::class]);
    }

    public function testOnListingResultWithoutNavigationIdDoesNothing(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->never())->method('search');

        $subscriber = new ListingLayoutSubscriber($repository);
        $event = $this->createEvent(null);

        $subscriber->onListingResult($event);

        self::assertFalse($event->getResult()->hasExtension('rcMinimalisticLayout'));
    }

    public function testOnListingResultWithNonStringNavigationIdDoesNothing(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects($this->never())->method('search');

        $subscriber = new ListingLayoutSubscriber($repository);

        $request = new Request();
        $request->attributes->set('navigationId', 12345);
        $event = $this->createEventWithRequest($request);

        $subscriber->onListingResult($event);

        self::assertFalse($event->getResult()->hasExtension('rcMinimalisticLayout'));
    }

    public function testOnListingResultWithUnknownCategoryDoesNothing(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('search')->willReturn(
            $this->createSearchResult(null)
        );

        $subscriber = new ListingLayoutSubscriber($repository);
        $event = $this->createEvent('category-id-123');

        $subscriber->onListingResult($event);

        self::assertFalse($event->getResult()->hasExtension('rcMinimalisticLayout'));
    }

    public function testOnListingResultWithActiveCustomFieldSetsExtension(): void
    {
        $category = new CategoryEntity();
        $category->setId('category-id-123');
        $category->setCustomFields(['rc_show_minimalistic_productlist' => true]);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('search')->willReturn(
            $this->createSearchResult($category)
        );

        $subscriber = new ListingLayoutSubscriber($repository);
        $event = $this->createEvent('category-id-123');

        $subscriber->onListingResult($event);

        self::assertTrue($event->getResult()->hasExtension('rcMinimalisticLayout'));

        /** @var ArrayStruct $extension */
        $extension = $event->getResult()->getExtension('rcMinimalisticLayout');
        self::assertTrue($extension->get('active'));
    }

    public function testOnListingResultWithInactiveCustomFieldSetsExtensionFalse(): void
    {
        $category = new CategoryEntity();
        $category->setId('category-id-123');
        $category->setCustomFields(['rc_show_minimalistic_productlist' => false]);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('search')->willReturn(
            $this->createSearchResult($category)
        );

        $subscriber = new ListingLayoutSubscriber($repository);
        $event = $this->createEvent('category-id-123');

        $subscriber->onListingResult($event);

        /** @var ArrayStruct $extension */
        $extension = $event->getResult()->getExtension('rcMinimalisticLayout');
        self::assertFalse($extension->get('active'));
    }

    public function testOnListingResultWithMissingCustomFieldSetsExtensionFalse(): void
    {
        $category = new CategoryEntity();
        $category->setId('category-id-123');
        $category->setCustomFields([]);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('search')->willReturn(
            $this->createSearchResult($category)
        );

        $subscriber = new ListingLayoutSubscriber($repository);
        $event = $this->createEvent('category-id-123');

        $subscriber->onListingResult($event);

        /** @var ArrayStruct $extension */
        $extension = $event->getResult()->getExtension('rcMinimalisticLayout');
        self::assertFalse($extension->get('active'));
    }

    private function createEvent(?string $navigationId): ProductListingResultEvent
    {
        $request = new Request();
        if ($navigationId !== null) {
            $request->attributes->set('navigationId', $navigationId);
        }

        return $this->createEventWithRequest($request);
    }

    private function createEventWithRequest(Request $request): ProductListingResultEvent
    {
        $result = new ProductListingResult(
            'product',
            0,
            new ProductCollection(),
            null,
            null,
            Context::createDefaultContext(),
        );

        return new ProductListingResultEvent(
            $request,
            $result,
            Context::createDefaultContext(),
        );
    }

    private function createSearchResult(?CategoryEntity $category): EntitySearchResult
    {
        $entities = $category !== null ? [$category] : [];

        return new EntitySearchResult(
            'category',
            \count($entities),
            new \Shopware\Core\Framework\DataAbstractionLayer\EntityCollection($entities),
            null,
            new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria(),
            Context::createDefaultContext(),
        );
    }
}
