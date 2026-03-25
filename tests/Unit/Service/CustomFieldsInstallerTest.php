<?php

declare(strict_types=1);

namespace Ruhrcoder\RcMinimalisticProductList\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Ruhrcoder\RcMinimalisticProductList\Service\CustomFieldsInstaller;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

final class CustomFieldsInstallerTest extends TestCase
{
    public function testInstallCallsUpsertOnRepository(): void
    {
        $setRepository = $this->createMock(EntityRepository::class);
        $relationRepository = $this->createMock(EntityRepository::class);

        $setRepository->expects($this->once())->method('upsert');

        $installer = new CustomFieldsInstaller($setRepository, $relationRepository);
        $installer->install(Context::createDefaultContext());
    }

    public function testAddRelationsCallsUpsertWhenSetExists(): void
    {
        $setRepository = $this->createMock(EntityRepository::class);
        $relationRepository = $this->createMock(EntityRepository::class);

        $idResult = $this->createMock(IdSearchResult::class);
        $idResult->method('getIds')->willReturn(['test-id-123']);

        $setRepository->method('searchIds')->willReturn($idResult);
        $relationRepository->expects($this->once())->method('upsert');

        $installer = new CustomFieldsInstaller($setRepository, $relationRepository);
        $installer->addRelations(Context::createDefaultContext());
    }

    public function testAddRelationsSkipsUpsertWhenNoSetFound(): void
    {
        $setRepository = $this->createMock(EntityRepository::class);
        $relationRepository = $this->createMock(EntityRepository::class);

        $idResult = $this->createMock(IdSearchResult::class);
        $idResult->method('getIds')->willReturn([]);

        $setRepository->method('searchIds')->willReturn($idResult);
        $relationRepository->expects($this->never())->method('upsert');

        $installer = new CustomFieldsInstaller($setRepository, $relationRepository);
        $installer->addRelations(Context::createDefaultContext());
    }
}
