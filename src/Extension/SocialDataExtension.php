<?php

namespace Dynamic\TemplateConfig\ORM;

use Dynamic\TemplateConfig\Model\SocialLink;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class SocialDataExtension
 * @package Dynamic\TemplateConfig\ORM
 *
 * @method \SilverStripe\ORM\HasManyList SocialLinks()
 */
class SocialDataExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_many = array(
        'SocialLinks' => SocialLink::class,
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $config = GridFieldConfig_RecordEditor::create();
        $config->addComponent(new GridFieldOrderableRows('SortOrder'));

        $socialLinks = GridField::create(
            'SocialLinks',
            '',
            $this->owner->SocialLinks(),
            $config
        );

        $fields->addFieldsToTab('Root.Social', array(
            HeaderField::create('SocialHD', 'Social Properties', 3),
            LiteralField::create('SocialDescrip', '<p>Add links to your social media properties</p>'),
            HeaderField::create('SociallinkHD', 'Links', 2),
            $socialLinks,
        ));
    }
}
