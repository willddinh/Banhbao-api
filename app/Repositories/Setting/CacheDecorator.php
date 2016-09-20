<?php

namespace App\Repositories\Setting;

use App\Services\Cache\CacheInterface;

/**
 * Class CacheDecorator.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class CacheDecorator extends AbstractSettingDecorator
{
    /**
     * @var \App\Services\Cache\CacheInterface
     */
    protected $cache;

    /**
     * Cache key.
     *
     * @var string
     */
    protected $cacheKey = 'setting';

    /**
     * @param SettingInterface $setting
     * @param CacheInterface   $cache
     */
    public function __construct(SettingInterface $setting, CacheInterface $cache)
    {
        parent::__construct($setting);
        $this->cache = $cache;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getSettings()
    {
        $key = md5(getLang().$this->cacheKey.'settings');

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $setting = $this->setting->getSettings();

        $this->cache->put($key, $setting);

        return $setting;
    }
}
