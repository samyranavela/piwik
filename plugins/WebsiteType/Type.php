<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\WebsiteType;

use Piwik\Property\PropertySettings;

class Type extends \Piwik\Type\Type
{
    const ID = 'website';
    protected $name = 'Referrers_ColumnWebsite'; // we will use new key of WebsiteType_ once we have them
    protected $namePlural = 'SitesManager_Sites'; // translated into more languages
    protected $description = 'WebsiteType_WebsiteDescription';
    protected $howToSetupUrl = '?module=CoreAdminHome&action=trackingCodeGenerator';

    public function configurePropertySettings(PropertySettings $settings)
    {
    }
}

