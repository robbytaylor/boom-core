<?php

namespace BoomCMS\Core\Asset;

use BoomCMS\Foundation\Query as BaseQuery;

class Query extends BaseQuery
{
    protected $filterAliases = [
        'tag'                        => Finder\Tag::class,
        'title'                      => Finder\TitleContains::class,
        'titleordescriptioncontains' => Finder\TitleOrDescriptionContains::class,
        'type'                       => Finder\Type::class,
    ];

    public function count()
    {
        $finder = $this->addFilters(new Finder\Finder(), $this->params);

        return $finder->count();
    }

    public function getResults()
    {
        $finder = $this->addFilters(new Finder\Finder(), $this->params);
        $finder = $this->configurePagination($finder, $this->params);

        return $finder->findAll();
    }
}
