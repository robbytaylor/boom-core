<?php defined('SYSPATH') or die('No direct script access.');

class Migration_Boom_20140204130300 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
		$db->query(NULL, "alter table page_urls drop redirect");
	}

	public function down(Kohana_Database $db)
	{
	}
}