<?php

class Boom_Controller_Cms_Page_Urls_Save extends Controller_Cms_Page_Urls
{
	public function before()
	{
		parent::before();

		$this->_csrf_check();
	}

	public function action_add()
	{
		$location = URL::title(trim($this->request->post('location')));

		$this->page_url->where('location', '=', $location)->find();

		if ($this->page_url->loaded() AND $this->page_url->page_id !== $this->page->id)
		{
			// Url is being used for a different page.
			// Notify that the url is already in use so that the JS can load a prompt to move the url.
			$this->response->body(json_encode(array('existing_url_id' => $this->page_url->id)));
		}
		elseif ( ! $this->page_url->loaded())
		{
			//  It's not an old URL, so create a new one.
			$this->page_url
				->values(array(
					'location'		=>	$location,
					'page_id'		=>	$this->page->id,
					'is_primary'	=>	FALSE,
				))
				->create();

			$this->log("Added secondary url $location to page " . $this->page->version()->title . "(ID: " . $this->page->id . ")");
		}
	}

	public function action_delete()
	{
		if ( ! $this->page_url->is_primary)
		{
			$this->page_url->delete();
		}
	}

	public function action_make_primary()
	{
		$this->page_url->make_primary();
	}

	public function action_move()
	{
		$this->page_url->values(array(
			'page_id'		=>	$this->page->id,
			'is_primary'	=>	FALSE, // Make sure that it's only a secondary url for the this page.
		))
		->update();
	}
}