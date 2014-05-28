<?php

namespace Boom\Finder\Page\Filter;

use \Boom\Finder as Finder;

class ParentPage extends Finder\Filter
{
	protected $parent;

	public function __construct($parent)
	{
		$this->parent = $parent;
	}

	public function execute(\ORM $query)
	{
		$order = $this->parent->getChildOrderingPolicy();

		return $query
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->where('page_mptt.parent_id', '=', $this->parent->getId())
			->order_by($order->getColumn(), $order->getDirection());
	}
}