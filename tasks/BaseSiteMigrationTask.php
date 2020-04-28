<?php

namespace Dynamic\BaseMigrator\Task;

use Dynamic\Base\Model\CompanyAddress;
use Dynamic\Base\Model\NavigationColumn;
use Dynamic\Base\Model\NavigationGroup;
use Dynamic\Base\Model\SocialLink;
use Dynamic\CompanyConfig\Model\CompanyConfigSetting;
use Dynamic\Locator\Location;
use Dynamic\TemplateConfig\Model\TemplateConfigSetting;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataList;
use SilverStripe\SiteConfig\SiteConfig;

class BaseSiteMigrationTask extends BuildTask
{
    /**
     * @var string
     */
    protected $title = 'Base Site - Migrate from 3.x to 4.x';

    /**
     * @var string
     */
    private static $segment = 'base-site-migration-task';

    /**
     * @param HTTPRequest $request
     */
    public function run($request)
    {
        $this->updateUtilityLinks();
        $this->updateSocialLinks();
        $this->updateFooterLinks();
        $this->updateCompanyConfig();
    }

    /**
     *
     */
    public function updateUtilityLinks()
    {
        $template_config = TemplateConfigSetting::current_template_config();
        $config = SiteConfig::current_site_config();
        $links = $template_config->UtilityLinks()->sort('SortOrder');
        $ct = 0;
        foreach ($links as $link) {
            $config->UtilityLinks()->add($link);
            $ct++;
        }
        static::write_message("{$ct}  utility links updated.");
    }

    /**
     *
     */
    public function updateSocialLinks()
    {
        $links = SocialLink::get();
        $ct = $this->updateLinks($links);
        static::write_message("{$ct}  social links updated.");
    }

    /**
     *
     */
    public function updateFooterLinks()
    {
        $links = NavigationColumn::get();
        $ct = $this->updateLinks($links);
        static::write_message("{$ct}  navigation columns updated.");
    }

    /**
     * @param DataList $links
     * @return int
     */
    public function updateLinks(DataList $links)
    {
        $ct = 0;
        foreach ($links as $link) {
            if ($link->ConfigID === 0) {
                $link->ConfigID = $link->GlobalConfigID;
                $link->write();
                $ct++;
            }
        }
        return $ct;
    }

    /**
     *
     */
    public function updateCompanyConfig()
    {
        $company_config = CompanyConfigSetting::current_company_config();
        $site_config = SiteConfig::current_site_config();
        if (!$site_config->CompanyName) {
            $site_config->CompanyName = $company_config->CompanyName;
            $site_config->write();
            static::write_message("Company name updated");
        }
        if (CompanyAddress::get()->count() == 0 && $primary = Location::get()->filter('IsPrimary', 1)->first()) {
            CompanyAddress::get()->removeAll();
            $address = CompanyAddress::create();
            $address->Title = $primary->Title;
            $address->IsPrimary = $primary->IsPrimary;
            $address->Address = $primary->Address;
            $address->Address2 = $primary->Address2;
            $address->City = $primary->City;
            $address->State = $primary->State;
            $address->PostalCode = $primary->PostalCode;
            $address->Country = $primary->Country;
            $address->SiteConfigID = $site_config->ID;
            $address->write();
            static::write_message("Primary Address updated");
        }
    }

    /**
     * @param $message
     */
    protected static function write_message($message)
    {
        if (Director::is_cli()) {
            echo "{$message}\n";
        } else {
            echo "{$message}<br><br>";
        }
    }
}
