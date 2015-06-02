<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Type;

use Piwik\Cache;
use Piwik\CacheId;
use Piwik\Plugin;
use Piwik\Plugin\Manager as PluginManager;
use Piwik\Property\PropertySettings;

class Type
{
    const ID = '';
    protected $name = 'General_Property';
    protected $namePlural = 'General_Properties';
    protected $description = 'Default type';
    protected $howToSetupUrl = '';

    public function isType($typeId)
    {
        // here we should add some point also check whether id matches any extended ID. Eg if
        // MetaSites extends Websites, then we expected $metaSite->isType('website') to be true (maybe)
        return $this->getId() === $typeId;
    }

    public function getId()
    {
        $id = static::ID;

        if (empty($id)) {
            $message = 'Type %s does not define an ID. Set the ID constant to fix this issue';;
            throw new \Exception(sprintf($message, get_called_class()));
        }

        return $id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamePlural()
    {
        return $this->namePlural;
    }

    public function getHowToSetupUrl()
    {
        return $this->howToSetupUrl;
    }

    public function configurePropertySettings(PropertySettings $settings)
    {
    }

    /**
     * @return Type[]
     */
    public static function getAllTypes()
    {
        $cache    = Cache::getTransientCache();
        $cacheKey = CacheId::pluginAware('Type_AllTypes');

        if ($cache->contains($cacheKey)) {
            $types = $cache->fetch($cacheKey);
        } else {
            $types = PluginManager::getInstance()->findComponents('Type', '\\Piwik\\Type\\Type');
            $cache->save($cacheKey, $types);
        }

        return $types;
    }

    /**
     * @param string $typeId
     * @return Type|null
     */
    public static function getType($typeId)
    {
        foreach (self::getAllTypes() as $type) {
            if ($type->getId() === $typeId) {
                return $type;
            }
        }

        return new Type();
    }
}

