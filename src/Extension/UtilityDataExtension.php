<?php

namespace Dynamic\TemplateConfig\ORM;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\GridFieldArchiveAction;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class UtilityDataExtension
 * @package Dynamic\TemplateConfig\ORM
 *
 * @method \SilverStripe\ORM\ManyManyList UtilityLinks()
 */
class UtilityDataExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $many_many = array(
        'UtilityLinks' => SiteTree::class,
    );

    /**
     * @var array
     */
    private static $many_many_extraFields = array(
        'UtilityLinks' => array(
            'SortOrder' => 'Int',
        ),
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ID) {
            $config = GridFieldConfig_RelationEditor::create()
                ->removeComponentsByType([
                    GridFieldAddNewButton::class,
                    GridFieldAddExistingAutocompleter::class,
                    GridFieldEditButton::class,
                    GridFieldArchiveAction::class,
                ])->addComponents(
                    new GridFieldOrderableRows('SortOrder'),
                    new GridFieldAddExistingSearchButton()
                );
            $promos = $this->owner->UtilityLinks()->sort('SortOrder');
            $linksField = GridField::create(
                'UtilityLinks',
                'Links',
                $promos,
                $config
            );

            $fields->addFieldsToTab('Root.Utility', array(
                HeaderField::create('UtilityHD', 'Utility Navigation', 3),
                LiteralField::create(
                    'UtilityDescrip',
                    '<p>Add links to the utility navigation area of your template.</p>'
                ),
                $linksField
                    ->setDescription('Add links to the utility navigation area'),
            ));
        }
    }
}
