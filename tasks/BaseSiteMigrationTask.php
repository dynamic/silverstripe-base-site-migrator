<?php

namespace Dynamic\BaseMigrator\Task;

use Dynamic\Base\Model\CompanyAddress;
use Dynamic\Base\Model\SocialLink;
use Dynamic\CompanyConfig\Model\CompanyConfigSetting;
use Dynamic\Locator\Location;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\BuildTask;
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
        $this->updateSocialLinks();
        $this->updateCompanyConfig();
    }

    /**
     *
     */
    public function updateSocialLinks()
    {
        $links = SocialLink::get();
        $ct = 0;
        foreach ($links as $link) {
            if ($link->ConfigID === 0) {
                $link->ConfigID = $link->GlobalConfigID;
                $link->write();
                $ct++;
            }
        }
        echo $ct . " social links updated";
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
            echo "Company name updated";
        }
        if ($primary = Location::get()->filter('IsPrimary', 1)->first()) {
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
            echo "Primary Address updated";
        }
    }
}
