<?php

namespace BoomCMS\Jobs;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\URL;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class ReassignURL extends Command implements SelfHandling
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @var URL
     */
    protected $url;

    public function __construct(URL $url, Page $page)
    {
        $this->url = $url;
        $this->page = $page;
    }

    public function handle()
    {
        $this->url
            ->setPageId($this->page->getId())
            ->setIsPrimary(false);

        URLFacade::save($this->url);
    }
}
