<?php

namespace BoomCMS\Core\Asset\Finder;

use BoomCMS\Foundation\Finder\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder;

class TitleContains extends BaseFilter
{
    protected $title;

    public function __construct($title = null)
    {
        $this->title = trim($title);
    }

    public function build(Builder $query)
    {
        $text = $this->title;

        return $query
            ->whereNested(function ($query) use ($text) {
                return $query
                    ->where('title', 'like', "%$text%")
                    ->orWhere('description', 'like', "%$text%");
            });
    }

    public function shouldBeApplied()
    {
        return $this->title == null ? false : true;
    }
}
