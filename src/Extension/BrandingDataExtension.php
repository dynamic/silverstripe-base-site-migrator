<?php

namespace Dynamic\TemplateConfig\ORM;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;

/**
 * Class TemplateConfig.
 *
 * @property string $TitleLogo
 * @property string $Title
 * @property string $TagLine
 *
 * @property int $LogoID
 * @property int $LogoRetinaID
 * @property int $FooterLogoID
 * @property int $FooterLogoRetinaID
 * @property int $FavIconID
 * @property int $AppleTouchIcon180ID
 * @property int $AppleTouchIcon152ID
 * @property int $AppleTouchIcon114ID
 * @property int $AppleTouchIcon72ID
 * @property int $AppleTouchIcon57ID
 *
 * @method Image Logo()
 * @method Image LogoRetina()
 * @method Image FooterLogo()
 * @method Image FooterLogoRetina()
 * @method Image FavIcon()
 * @method Image AppleTouchIcon180()
 * @method Image AppleTouchIcon152()
 * @method Image AppleTouchIcon114()
 * @method Image AppleTouchIcon72()
 * @method Image AppleTouchIcon57()
 */
class BrandingDataExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = array(
        'TitleLogo' => "Enum(array('Logo', 'Title'))",
        "Title" => "Varchar(255)",
        "Tagline" => "Varchar(255)",
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Logo' => Image::class,
        'LogoRetina' => Image::class,
        'FooterLogo' => Image::class,
        'FooterLogoRetina' => Image::class,
        'FavIcon' => File::class,
        'AppleTouchIcon180' => File::class,
        'AppleTouchIcon152' => File::class,
        'AppleTouchIcon114' => File::class,
        'AppleTouchIcon72' => File::class,
        'AppleTouchIcon57' => File::class,
    );

    /**
     * @var array
     */
    private static $defaults = array(
        'TitleLogo' => 'Title',
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $logoTypes = array('jpg', 'jpeg', 'png', 'gif', 'svg');
        $iconTypes = array('ico');
        $appleTouchTypes = array('png');


        // options for logo or title display
        $logoOptions = array(
            'Logo' => 'Display Logo',
            'Title' => 'Display Site Title and Slogan',
        );

        $fields->removeByName([
            'TitleLogo',
            'Title',
            'Tagline',
            'Logo',
            'LogoRetina',
            'FooterLogo',
            'FooterLogoRetina',
            'FavIcon',
            'AppleTouchIcon180',
            'AppleTouchIcon152',
            'AppleTouchIcon114',
            'AppleTouchIcon72',
            'AppleTouchIcon57',
        ]);

        $icons = array();
        $appleIcons = array();

        $fields->addFieldsToTab('Root.Main', array(
            HeaderField::create('BrandingHD', 'Branding', 3),
            LiteralField::create('HeaderDescrip', '<p>Adjust the settings of your template header.</p>'),
            $titlelogo = OptionsetField::create('TitleLogo', 'Branding', $logoOptions),
            $title = TextField::create("Title", _t(SiteConfig::class . '.SITETITLE', "Site title")),
            $tagline = TextField::create("Tagline", _t(SiteConfig::class . '.SITETAGLINE', "Site Tagline/Slogan")),
            // normal logos
            $logo = UploadField::create('Logo', 'Logo'),
            $retinaLogo = UploadField::create('LogoRetina', 'Retina Logo'),
            // footer logos
            ToggleCompositeField::create('FooterLogos', 'Footer', [
                $icons[] = UploadField::create('FooterLogo', 'Footer Logo'),
                $icons[] = UploadField::create('FooterLogoRetina', 'Retina Footer Logo'),
            ]),
            // icons
            ToggleCompositeField::create('Icons', 'Icons', [
                $favIcon = UploadField::create(
                    'FavIcon',
                    'Favicon, in .ico format, dimensions of 16x16, 32x32, or 48x48'
                ),
                $appleIcons[] = UploadField::create(
                    'AppleTouchIcon180',
                    'Apple Touch Web Clip and Windows 8 Tile Icon (dimensions of 180x180, PNG format)'
                ),
                $appleIcons[] = UploadField::create(
                    'AppleTouchIcon152',
                    'Apple Touch Web Clip and Windows 8 Tile Icon (dimensions of 152x152, PNG format)'
                ),
                $appleIcons[] = UploadField::create(
                    'AppleTouchIcon114',
                    'Apple Touch Web Clip and Windows 8 Tile Icon (dimensions of 114x114, PNG format)'
                ),
                $appleIcons[] = UploadField::create(
                    'AppleTouchIcon72',
                    'Apple Touch Web Clip and Windows 8 Tile Icon (dimensions of 72x72, PNG format)'
                ),
                $appleIcons[] = UploadField::create(
                    'AppleTouchIcon57',
                    'Apple Touch Web Clip and Windows 8 Tile Icon (dimensions of 57x57, PNG format)'
                ),
            ]),
        ));

        $title->hideUnless($titlelogo->getName())->isEqualTo('Title');
        $tagline->hideUnless($titlelogo->getName())->isEqualTo('Title');

        $icons[] = $logo;
        $logo->hideUnless($titlelogo->getName())->isEqualTo('Logo');
        $icons[] = $retinaLogo;
        $retinaLogo->hideUnless($titlelogo->getName())->isEqualTo('Logo');

        foreach ($icons as $icon) {
            $icon->getValidator()->setAllowedExtensions($logoTypes);
        }

        $favIcon->getValidator()->setAllowedExtensions($iconTypes);

        foreach ($appleIcons as $icon) {
            $icon->getValidator()->setAllowedExtensions($appleTouchTypes);
        }
    }

    /**
     * Because the owner object may not be versioned
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->owner->Logo()->exists()) {
            $this->owner->Logo()->publishRecursive();
        }

        if ($this->owner->LogoRetina()->exists()) {
            $this->owner->LogoRetina()->publishRecursive();
        }

        if ($this->owner->FooterLogo()->exists()) {
            $this->owner->FooterLogo()->publishRecursive();
        }

        if ($this->owner->FooterLogoRetina()->exists()) {
            $this->owner->FooterLogoRetina()->publishRecursive();
        }

        if ($this->owner->FavIcon()->exists()) {
            $this->owner->FavIcon()->publishRecursive();
        }

        if ($this->owner->AppleTouchIcon180()->exists()) {
            $this->owner->AppleTouchIcon180()->publishRecursive();
        }

        if ($this->owner->AppleTouchIcon152()->exists()) {
            $this->owner->AppleTouchIcon152()->publishRecursive();
        }

        if ($this->owner->AppleTouchIcon114()->exists()) {
            $this->owner->AppleTouchIcon114()->publishRecursive();
        }

        if ($this->owner->AppleTouchIcon72()->exists()) {
            $this->owner->AppleTouchIcon72()->publishRecursive();
        }

        if ($this->owner->AppleTouchIcon57()->exists()) {
            $this->owner->AppleTouchIcon57()->publishRecursive();
        }
    }

    /**
     * @return mixed
     */
    public function getSiteLogo()
    {
        return ($this->owner->Logo()) ? $this->owner->Logo() : false;
    }

    /**
     * @return mixed
     */
    public function getFooterLinkList()
    {
        return ($this->owner->FooterLinks()
            ->exists()) ? $this->owner->FooterLinks()
            ->sort('SortOrder') : false;
    }
}
