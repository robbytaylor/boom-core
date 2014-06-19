<?php

namespace Boom\Page\Command\Delete;

use \DB as DB;

class FromFeatureBoxes extends \Boom\Page\Command
{
	public function execute(\Boom\Page $page)
	{
		DB::delete('chunk_features')
			->where('target_page_id', '=', $page->getId())
			->execute();
	}
}