<?php

namespace Dynamic\BaseMigrator\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class ModelMigrationDataExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        'GlobalConfigID' => 'Int',
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName([
            'GlobalConfigID',
        ]);
    }
}
