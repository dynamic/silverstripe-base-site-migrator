<?php

namespace Dynamic\TemplateConfig\Model;

use Dynamic\TemplateConfig\Admin\TemplateConfigAdmin;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Class TemplateConfigSetting
 * @package Dynamic\TemplateConfig\Model
 */
class TemplateConfigSetting extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    /**
     * @var string
     */
    private static $singular_name = 'Custom Template Setting';

    /**
     * @var string
     */
    private static $plural_name = 'Custom Template Settings';
    /**
     *
     * @var string
     */
    private static $description = 'Settings to customize a template';

    /**
     * @var string
     */
    private static $table_name = 'TemplateConfigSetting';

    /**
     * Default permission to check for 'LoggedInUsers' to create or edit pages.
     *
     * @var array
     * @config
     */
    private static $required_permission = ['CMS_ACCESS_CMSMain', 'THEME_CONFIG_PERMISSION'];

    /**
     * Add $TemplateConfig to all SSViewers.
     */
    public static function get_template_global_variables()
    {
        return [
            'TemplateConfig' => 'current_template_config',
        ];
    }

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->fieldByName('Root')->fieldByName('Main')
                ->setTitle('Branding');

            $fields->removeByName([
                'NavigationColumns',
                'UtilityLinks',
                'SocialLinks',
                'LinkTracking',
                'FileTracking',
            ]);
        });
        return parent::getCMSFields();
    }

    /**
     * Get the actions that are sent to the CMS. In
     * your extensions: updateEditFormActions($actions).
     *
     * @return FieldList
     */
    public function getCMSActions()
    {
        if (Permission::check('ADMIN') || Permission::check('THEME_CONFIG_PERMISSION')) {
            $actions = new FieldList(
                FormAction::create('save_templateconfig', _t('TemplateConfig.SAVE', 'Save'))
                    ->addExtraClass('btn-primary font-icon-save')
            );
        } else {
            $actions = FieldList::create();
        }
        $this->extend('updateCMSActions', $actions);

        return $actions;
    }

    /**
     * @throws ValidationException
     * @throws null
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $config = self::current_template_config();
        if (!$config) {
            self::make_template_config();
            DB::alteration_message('Added default template config', 'created');
        }
    }

    /**
     * Get the current sites {@link GlobalSiteSetting}, and creates a new one
     * through {@link make_template_config()} if none is found.
     *
     * @return GlobalSiteSetting|DataObject
     * @throws ValidationException
     */
    public static function current_template_config()
    {
        if ($config = self::get()->first()) {
            return $config;
        }

        return self::make_template_config();
    }

    /**
     * Create {@link GlobalSiteSetting} with defaults from language file.
     *
     * @return static
     * @throws ValidationException
     */
    public static function make_template_config()
    {
        $config = self::create();
        $config->write();

        return $config;
    }

    /**
     * To duplicate into the site config (so stuff that relies on site config still works)
     */
    public function onBeforeWrite()
    {
        /** @var SiteConfig $siteconfig */
        $siteconfig = SiteConfig::current_site_config();
        if (!$siteconfig->Title) {
            $siteconfig->Title = $this->Title;
            $siteconfig->Tagline = $this->Tagline;
            $siteconfig->write();
        }

        parent::onBeforeWrite();
    }

    /**
     * @return string
     */
    public function CMSEditLink()
    {
        return TemplateConfigAdmin::singleton()->Link();
    }

    /**
     * @param null $member
     *
     * @return bool|int|null
     */
    public function canEdit($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        $extended = $this->extendedCan('canEdit', $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::checkMember($member, 'THEME_CONFIG_PERMISSION');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'TEMPLATE_CONFIG_PERMISSION' => [
                'name' => _t(
                    'Dynamic\\TemplateConfig\\Model\\TemplateConfig.TEMPLATE_CONFIG_PERMISSION',
                    "Access to '{title}' section",
                    ['title' => TemplateConfigAdmin::menu_title()]
                ),
                'category' => _t(
                    'SilverStripe\\Security\\Permission.CMS_ACCESS_CATEGORY',
                    'CMS Access'
                ),
                'help' => _t(
                    'Dynamic\\TemplateConfig\\Model\\TemplateConfig.TEMPLATE_CONFIG_PERMISSION_HELP',
                    'Ability to edit template colors.'
                ),
                'sort' => 400,
            ],
        ];
    }
}
