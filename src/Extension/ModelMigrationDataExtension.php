<?php

namespace Dynamic\BaseMigrator\Extension;

use SilverStripe\ORM\DataExtension;

class ModelMigrationDataExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        'GlobalConfigID' => 'Int',
    ];
}
