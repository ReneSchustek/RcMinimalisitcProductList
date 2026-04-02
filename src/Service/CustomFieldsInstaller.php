<?php declare(strict_types=1);

namespace Ruhrcoder\RcMinimalisticProductList\Service;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\CustomField\CustomFieldTypes;

final class CustomFieldsInstaller
{
    private const CUSTOM_FIELDSET_NAME = 'rc_show_minimalistic_productlist_category_bool';

    private const CUSTOM_FIELDSET = [
        'name' => self::CUSTOM_FIELDSET_NAME,
        'config' => [
            'label' => [
                'en-GB' => 'Show minimalistic productlist',
                'de-DE' => 'Minimalistische Produktliste anzeigen',
                Defaults::LANGUAGE_SYSTEM => 'Show minimalistic productlist'
            ],
            'translated' => true,
        ],
        'allow_customer_write' => false,
        'allow_cart_expose' => false,
        'store_api_aware' => false,
        'active' => true,
        'global' => true,
        'customFields' => [
            [
                'name' => 'rc_show_minimalistic_productlist',
                'type' => CustomFieldTypes::BOOL,
                'config' => [
                    'componentName' => 'sw-field',    
                    'type' => 'checkbox',
                    'customFieldType' => 'checkbox',                    
                    'label' => [
                        'en-GB' => 'Show a minimalistic productlist',
                        'de-DE' => 'Eine minimalistische Produktliste anzeigen',
                        Defaults::LANGUAGE_SYSTEM => 'Show a minimalistic productlist'
                    ], 
                    'helpText' => [
                        'en-GB' => 'If activated, the productlist for the category only contains picture, title and price.', 
                        'de-DE' => 'Wenn aktiviert, enthält die Produktliste nur das Bild, den Titel und den Preis.'
                    ],
                    'customFieldPosition' => 1
                ],
                'active' => true
            ]
        ]        
    ];

    public function __construct(
        private readonly EntityRepository $customFieldSetRepository,
        private readonly EntityRepository $customFieldSetRelationRepository
    ) {
    }

    public function install(Context $context): void
    {
        $this->customFieldSetRepository->upsert([
            self::CUSTOM_FIELDSET
        ], $context);
    }

    public function addRelations(Context $context): void
    {
        $this->customFieldSetRelationRepository->upsert(array_map(function (string $customFieldSetId) {
            return [
                'customFieldSetId' => $customFieldSetId,
                'entityName' => 'category',
            ];
        }, $this->getCustomFieldSetIds($context)), $context);
    }

    /**
     * @return list<string>
     */
    private function getCustomFieldSetIds(Context $context): array
    {
        $criteria = new Criteria();

        $criteria->addFilter(new EqualsFilter('name', self::CUSTOM_FIELDSET_NAME));

        return $this->customFieldSetRepository->searchIds($criteria, $context)->getIds();
    }
}