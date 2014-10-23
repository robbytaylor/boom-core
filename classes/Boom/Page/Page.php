<?php

namespace Boom\Page;

use \Boom\Editor\Editor as Editor;
use \Boom\Taggable as Taggable;
use \Boom\Template\Template as Template;
use \Boom\Person as Person;

use \ORM as ORM;

class Page implements Taggable
{
	/**
	 *
	 * @var \Model_Page
	 */
	protected $model;

	/**
	 *
	 * @var \Model_Page_URL
	 */
	protected $_url;

	public function __construct(\Model_Page $model)
	{
		$this->model = $model;
	}

	public function addTagByName($name)
	{

	}

	public function allowsExternalIndexing()
	{
		return (bool) $this->model->external_indexing;
	}

	public function allowsInternalIndexing()
	{
		return (bool) $this->model->internal_indexing;
	}

	public function createVersion($current = null, array $values = null)
	{
		// Get the current version
		if ($current === null)
		{
			$current = $this->getCurrentVersion();
		}

		// Create a new version with the same values as the current version.
		$new_version = ORM::factory('Page_Version')
			->values($current->object());

		// Update the new version with any update values.
		if ( ! empty($values))
		{
			$new_version
				->values($values, array_keys($values));
		}

		// Return the new version
		return $new_version;
	}

	public function getChildOrderingPolicy()
	{
		return new ChildOrderingPolicy($this->model->children_ordering_policy);
	}

	public function getCreatedBy()
	{
		return $this->model->created_by;
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getCreatedTime()
	{
		return new \DateTime('@' . $this->model->created_time);
	}

	public function getCurrentVersion()
	{
		return $this->model->version();
	}

	/**
	 * Get a description for the page.
	 *
	 * If no description property is set then the standfirst is used instead.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		$description = ($this->model->description != null)? $this->model->description : \Chunk::factory('text', 'standfirst', $this)->text();

		return \strip_tags($description);
	}

	public function getDefaultChildTemplateId()
	{
		if ($this->model->children_template_id)
		{
			return $this->model->children_template_id;
		}

		$parent = $this->model->parent();
		return ($parent->grandchild_template_id != 0)? $parent->grandchild_template_id : $this->model->getTemplateId();
	}

	public function getFeatureImage()
	{
		return \Boom\Asset::factory($this->feature_image);
	}

	public function getFeatureImageId()
	{
		return $this->model->feature_image_id;
	}

	public function getId()
	{
		return $this->model->id;
	}

	public function getInternalName()
	{
		return $this->model->internal_name;
	}

	/**
	 *
	 * @return Keywords
	 */
	public function getKeywords()
	{
		$keywords = explode(',', $this->model->keywords);

		foreach ($keywords as &$keyword) {
			$keyword = trim($keyword);
		}

		return new Keywords($keywords);
	}

	public function getManualOrderPosition()
	{
		return $this->model->sequence;
	}

	public function getMptt()
	{
		return $this->model->mptt;
	}

	public function getTemplate()
	{
		return new Template($this->getCurrentVersion()->template);
	}

	public function getTemplateId()
	{
		return $this->getCurrentVersion()->template_id;
	}

	public function getThumbnail()
	{
		
	}

	public function getTitle()
	{
		return $this->getCurrentVersion()->title;
	}

	public function getUrls()
	{
		return $this->model->urls->order_by('location', 'asc')->find_all();
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getVisibleFrom()
	{
		return new \DateTime('@' . $this->model->visible_from);
	}

	/**
	 *
	 * @return \DateTime
	 */
	public function getVisibleTo()
	{
		return new \DateTime('@' . $this->model->visible_to);
	}

	public function hasChildren()
	{
		return $this->getMptt()->has_children();
	}

	public function hasFeatureImage()
	{
		return $this->getFeatureImageId() != 0;
	}

	public function isDeleted()
	{
		return (bool) $this->model->deleted;
	}

	public function isVisible()
	{
		return $this->isVisibleAtTime(Editor::instance()->getLiveTime());
	}

	public function isVisibleAtAnyTime()
	{
		return (bool) $this->model->visible;
	}

	/**
	 *
	 * @param int $unixTimestamp
	 * @return boolean
	 */
	public function isVisibleAtTime($unixTimestamp)
	{
		return ($this->model->visible && $this->getVisibleFrom()->getTimestamp() <= $unixTimestamp && ($this->getVisibleTo()->getTimestamp() >= $unixTimestamp || $this->getVisibleTo()->getTimestamp() == 0));
	}
	
	public function isVisibleInCmsNav()
	{
		return $this->model->visible_in_nav_cms;
	}

	public function isVisibleInNav()
	{
		return $this->model->visible_in_nav;
	}

	public function loaded()
	{
		return $this->model->loaded();
	}

	public function removeTagByName($name)
	{

	}

	/**
	 *
	 * @param	string	$column
	 * @param	string	$direction
	 */
	public function setChildOrderingPolicy($column, $direction)
	{
		$ordering_policy = new \Boom\Page\ChildOrderingPolicy($column, $direction);
		$this->model->children_ordering_policy = $ordering_policy->asInt();

		return $this;
	}

	/**
	 * Returns the Model_Page_URL object for the page's primary URI
	 *
	 * The URL can be displayed by casting the returned object to a string:
	 *
	 *		(string) $page->url();
	 *
	 *
	 * @return \Model_Page_URL
	 */
	public function url()
	{
		if ($this->_url === null) {
			$this->_url = \ORM::factory('Page_URL')
				->values(array(
					'location'		=>	$this->model->primary_uri,
					'page_id'		=>	$this->model->id,
					'is_primary'	=>	true,
				));
		}

		return $this->_url;
	}

	/**
	 *
	 * @return \Boom\Page
	 */
	public function parent()
	{
		return ($this->model->mptt->is_root())? $this : \Boom\Page\Factory::byId($this->model->mptt->parent_id);
	}

	public function wasCreatedBy(Person $person)
	{
		return $this->getCreatedBy() === $person->getId();
	}
}