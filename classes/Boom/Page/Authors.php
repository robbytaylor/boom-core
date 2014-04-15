<?php

class Boom_Page_Authors
{
	protected $_authorTagPrefix = 'Author/';

	/**
	 *
	 * @var array
	 */
	protected $_authors;

	/**
	 *
	 * @var Model_Page
	 */
	protected $_page;

	public function __construct(Model_Page $page)
	{
		$this->_page = $page;
		$this->_authors = $page->get_tags_with_name_like($this->_authorTagPrefix . "%");
	}

	public function getNames()
	{
		$authors = array();

		if ( ! empty($this->_authors))
		{
			foreach ($this->_authors as $author)
			{
				$authors[] = htmlentities(str_ireplace($this->_authorTagPrefix, '', $author->name), ENT_QUOTES);
			}
		}

		return $authors;
	}

	public function __toString()
	{
		return implode(', ', $this->getNames());
	}
}