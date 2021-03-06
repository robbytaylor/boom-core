<?php

namespace BoomCMS\Tests\Stubs;

use BoomCMS\ServiceProviders\AssetServiceProvider;
use BoomCMS\ServiceProviders\AuthServiceProvider;
use BoomCMS\ServiceProviders\BoomCMSServiceProvider as BaseServiceProvider;
use BoomCMS\ServiceProviders\ChunkServiceProvider;
use BoomCMS\ServiceProviders\EditorServiceProvider;
use BoomCMS\ServiceProviders\EventServiceProvider;
use BoomCMS\ServiceProviders\PageServiceProvider;
use BoomCMS\ServiceProviders\PersonServiceProvider;
use BoomCMS\ServiceProviders\TagServiceProvider;
use BoomCMS\ServiceProviders\URLServiceProvider;
use Illuminate\Html\HtmlServiceProvider;

class BoomCMSServiceProvider extends BaseServiceProvider
{
    protected $serviceProviders = [
        AssetServiceProvider::class,
        PersonServiceProvider::class,
        AuthServiceProvider::class,
        EditorServiceProvider::class,
        PageServiceProvider::class,
        ChunkServiceProvider::class,
        URLServiceProvider::class,
        TagServiceProvider::class,
        EventServiceProvider::class,
        HtmlServiceProvider::class,
    ];
}
