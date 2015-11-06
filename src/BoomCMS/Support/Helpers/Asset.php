<?php

namespace BoomCMS\Support\Helpers;

use BoomCMS\Core\Asset\Asset as A;
use Illuminate\Support\Facades\Config;

abstract class Asset
{
    /**
     * Return the controller to be used to display an asset.
     * 
     * @param Asset $asset
     * @return string
     */
    public static function controller(A $asset)
    {
        $namespace = 'BoomCMS\Http\Controllers\Asset\\';

        if (!$asset->loaded()) {
            return null;
        }

        $byExtension = $namespace.ucfirst($asset->getExtension());
        if (class_exists($byExtension)) {
            return $byExtension;
        }

        $byType = $namespace.ucfirst($asset->getType());
        if (class_exists($byType)) {
            return $byType;
        }

        return $namespace.'BaseController';
    }

    /**
     * 
     * @param string $mime
     * @return string
     */
    public static function typeFromMimetype($mime)
    {
        foreach (static::types() as $type => $mimetypes) {
            if (array_search($mime, $mimetypes) !== false) {
                return $type;
            }
        }
    }

    public static function types()
    {
        return Config::get('boomcms.assets.types');
    }
}