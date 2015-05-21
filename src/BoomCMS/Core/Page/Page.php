<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Core\Person;
use BoomCMS\Core\Tag;
use BoomCMS\Core\Template;
use BoomCMS\Core\URL\URL;

use \DateTime;

class Page
{
    /**
     *
     * @var Page\Version
     */
    private $currentVersion;

    /**
	 *
	 * @var array
	 */
    protected $data;

    /**
	 *
	 * @var URL
	 */
    protected $primaryUrl;

    /**
     *
     * @var Template\Template
     */
    private $template;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function addTag(Tag\Tag $tag)
    {
        DB::insert('pages_tags', ['page_id', 'tag_id'])
            ->values([$this->getId(), $tag->getId()])
            ->execute();

        return $this;
    }

    public function allowsExternalIndexing()
    {
        return $this->get('external_indexing') == true;
    }

    public function allowsInternalIndexing()
    {
        return $this->get('internal_indexing') == true;
    }

    public function childrenAreVisibleInNav()
    {
        return $this->get('children_visible_in_nav') == true;
    }

    public function childrenAreVisibleInCmsNav()
    {
        return $this->get('children_visible_in_nav_cms') == true;
    }

    public function createVersion($current = null, array $values = null)
    {
        // Get the current version
        if ($current === null) {
            $current = $this->getCurrentVersion();
        }

        // Create a new version with the same values as the current version.
        $new_version = ORM::factory('Page_Version')
            ->values($current->object());

        // Update the new version with any update values.
        if ( ! empty($values)) {
            $new_version
                ->values($values, array_keys($values));
        }

        // Return the new version
        return $new_version;
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function getChildOrderingPolicy()
    {
        return new ChildOrderingPolicy($this->get('children_ordering_policy'));
    }

    public function getChildPageUrlPrefix()
    {
        return $this->get('children_url_prefix');
    }

    public function getCreatedBy()
    {
        // TODO: this needs to return a Person object.
        return $this->get('created_by');
    }

    /**
	 *
	 * @return DateTime
	 */
    public function getCreatedTime()
    {
        return new DateTime('@' . $this->get('created_time'));
    }

    public function getCurrentVersion()
    {
        if ($this->currentVersion === null) {

        }

        return $this->currentVersion;
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
        $description = ($this->get('description') != null) ? $this->get('description') : \Chunk::factory('text', 'standfirst', $this)->text();

        return \strip_tags($description);
    }

    public function getDefaultChildTemplateId()
    {
        if ($templateId = $this->get('children_template_id')) {
            return $templateId;
        }

        $parent = $this->getParent();

        return ($parent->getGrandchildTemplateId() != 0) ? $parent->getGrandchildTemplateId() : $this->getTemplateId();
    }

    public function getFeatureImage()
    {
        // TODO:: return an asset instance
        return \Boom\Asset\Factory::fromModel($this->getFeatureImageId());
    }

    public function getFeatureImageId()
    {
        return $this->get('feature_image_id');
    }

    public function getGrandchildTemplateId()
    {
        return $this->get('grandchild_template_id');
    }

    public function getGroupedTags()
    {
        $tags = $this->getTags();
        $grouped = [];

        foreach ($tags as $tag) {
            $grouped[$tag->getGroup()][] = $tag;
        }

        return $grouped;
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getInternalName()
    {
        return $this->get('internal_name');
    }

    /**
	 *
	 * @return Keywords
	 */
    public function getKeywords()
    {
        return $this->get('keywords');
    }

    /**
     *
     * @return DateTime
     */
    public function getLastModified()
    {
        return new DateTime('@' . $this->getCurrentVersion()->edited_time);
    }

    public function getManualOrderPosition()
    {
        return $this->get('sequence');
    }

    public function getParent()
    {
        return Factory::byId($this->getParentId());
    }

    public function getParentId()
    {
        return $this->get('parent_id');
    }

    public function getTemplate()
    {
        if ($this->template === null) {
            $provider = new Template\Provider();
            $this->template = $provider->findById($this->getTemplateId());
        }

        return $this->template;
    }

    public function getTemplateId()
    {
        return $this->getCurrentVersion()->template_id;
    }

    public function getTitle()
    {
        return $this->getCurrentVersion()->title;
    }

    /**
	 *
	 * @return DateTime
	 */
    public function getVisibleFrom()
    {
        $timestamp = $this->get('visible_from') ?: time();

        return new DateTime('@' . $timestamp);
    }

    /**
	 *
	 * @return DateTime
	 */
    public function getVisibleTo()
    {
        return $this->get('visible_to') == 0 ? null : new DateTime('@' . $this->get('visible_to'));
    }

    public function hasFeatureImage()
    {
        return $this->getFeatureImageId() != 0;
    }

    public function isDeleted()
    {
        return $this->get('deleted') === false;
    }

    public function isRoot()
    {
        return $this->getParentId() == null;
    }

    public function isVisible()
    {
        return $this->isVisibleAtTime(time());
    }

    public function isVisibleAtAnyTime()
    {
        return $this->get('visible') == true;
    }

    /**
	 *
	 * @param int $unixTimestamp
	 * @return boolean
	 */
    public function isVisibleAtTime($unixTimestamp)
    {
        return ($this->isVisibleAtAnyTime() &&
            $this->getVisibleFrom()->getTimestamp() <= $unixTimestamp &&
            ($this->getVisibleTo() === null || $this->getVisibleTo()->getTimestamp() >= $unixTimestamp)
        );
    }

    public function isVisibleInCmsNav()
    {
        return $this->get('visible_in_nav_cms') == true;
    }

    public function isVisibleInNav()
    {
        return $this->get('visible_in_nav') == true;
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    public function removeTag(Tag\Tag $tag)
    {
        DB::delete('pages_tags')
            ->where('page_id', '=', $this->getId())
            ->where('tag_id', '=', $tag->getId())
            ->execute();

        return $this;
    }

    public function setChildTemplateId($id)
    {
        $this->data['children_template_id'] = $id;

        return $this;
    }

    /**
	 *
	 * @param	string	$column
	 * @param	string	$direction
	 */
    public function setChildOrderingPolicy($column, $direction)
    {
        $ordering_policy = new \Boom\Page\ChildOrderingPolicy($column, $direction);
        $this->data['children_ordering_policy'] = $ordering_policy->asInt();

        return $this;
    }

    /**
     *
     * @param  string          $prefix
     * @return \Boom\Page\Page
     */
    public function setChildrenUrlPrefix($prefix)
    {
        $this->data['children_url_prefix'] = $prefix;

        return $this;
    }

    /**
     *
     * @param  boolean         $visible
     * @return \Boom\Page\Page
     */
    public function setChildrenVisibleInNav($visible)
    {
        $this->data['children_visible_in_nav'] = $visible;

        return $this;
    }

    /**
     *
     * @param  boolean         $visible
     * @return \Boom\Page\Page
     */
    public function setChildrenVisibleInNavCMS($visible)
    {
        $this->data['children_visible_in_nav_cms'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param string $description
	 * @return \Boom\Page\Page
	 */
    public function setDescription($description)
    {
        $this->data['description'] = $description;

        return $this;
    }

    /**
	 *
	 * @param boolean $indexing
	 * @return \Boom\Page\Page
	 */
    public function setExternalIndexing($indexing)
    {
        $this->data['external_indexing'] = $indexing;

        return $this;
    }

    /**
	 *
	 * @param int $featureImageId
	 * @return \Boom\Page\Page
	 */
    public function setFeatureImageId($featureImageId)
    {
        $this->data['feature_image_id'] = $featureImageId > 0 ? $featureImageId : null;

        return $this;
    }

    /**
     *
     * @param  int             $templateId
     * @return \Boom\Page\Page
     */
    public function setGrandchildTemplateId($templateId)
    {
        $this->data['grandchild_template_id'] = $templateId;

        return $this;
    }

    /**
	 *
	 * @param boolean $indexing
	 * @return \Boom\Page\Page
	 */
    public function setInternalIndexing($indexing)
    {
        $this->data['internal_indexing'] = $indexing;

        return $this;
    }

    /**
	 *
	 * @param string $name
	 * @return \Boom\Page\Page
	 */
    public function setInternalName($name)
    {
        $this->data['internal_name'] = $name;

        return $this;
    }

    /**
	 *
	 * @param string $keywords
	 * @return \Boom\Page\Page
	 */
    public function setKeywords($keywords)
    {
        $this->data['keywords'] = $keywords;

        return $this;
    }

    /**
	 *
	 * @param int $parentId
	 * @return \Boom\Page\Page
	 */
    public function setParentPageId($parentId)
    {
        $this->data['parent_id'] = $parentId;

        return $this;
    }

    /**
	 *
	 * @param boolean $visible
	 * @return \Boom\Page\Page
	 */
    public function setVisibleAtAnyTime($visible)
    {
        $this->data['visible'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param DateTime $time
	 * @return \Boom\Page\Page
	 */
    public function setVisibleFrom(DateTime $time)
    {
        $this->data['visible_from'] = $time->getTimestamp();

        return $this;
    }

    /**
	 *
	 * @param boolean $visible
	 * @return \Boom\Page\Page
	 */
    public function setVisibleInCmsNav($visible)
    {
        $this->data['visible_in_nav_cms'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param boolean $visible
	 * @return \Boom\Page\Page
	 */
    public function setVisibleInNav($visible)
    {
        $this->data['visible_in_nav'] = $visible;

        return $this;
    }

    /**
	 *
	 * @param DateTime $time
	 * @return \Boom\Page\Page
	 */
    public function setVisibleTo(DateTime $time = null)
    {
        $this->data['visible_to'] = $time ? $time->getTimestamp() : null;

        return $this;
    }

    public function updateChildSequences(array $sequences)
    {
        foreach ($sequences as $sequence => $pageId) {
            $mptt = new \Model_Page_Mptt($pageId);

            // Only update the sequence of pages which are children of this page.
            if ($mptt->scope == $this->model->mptt->scope && $mptt->parent_id == $this->getId()) {
                \DB::update('pages')
                    ->set(['sequence' => $sequence])
                    ->where('id', '=', $pageId)
                    ->execute();
            }
        }

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
        if ($this->primaryUrl === null) {
            $this->primaryUrl = new URL([
                'page' => $this,
                'location' => $this->get('primary_uri'),
                'is_primary' => true
            ]);
        }

        return $this->primaryUrl;
    }

    /**
	 *
	 * @return \Boom\Page
	 */
    public function parent()
    {throw new \Exception('TODO');
        return ($this->model->mptt->is_root()) ? $this : \Boom\Page\Factory::byId($this->model->mptt->parent_id);
    }

    public function wasCreatedBy(Person\Person $person)
    {
        return $this->getCreatedBy() === $person->getId();
    }
}
