<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Helpers;

class Search extends Controller
{
    public function getPages()
    {
        $results = [];
        $pages = Helpers::getPages($this->request->input());

        foreach ($pages as $p) {
            $results[] = [
                'id'           => $p->getId(),
                'title'        => $p->getTitle(),
                'url'          => (string) $p->url(),
                'visible'      => (int) $p->isVisible(),
                'has_children' => (int) $p->hasChildren(),
            ];
        }

        return $results;
    }
}
