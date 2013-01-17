<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * ##Controller to edit page settings.
 *
 * Each function relates to a view in the boom/editor/page/settings directory.
 *
 * When called as a GET the functions will display the relevant view.
 * When called as a POST request the controller will save the data in that view.
 *
 * The controllers are tied quite strictly to the view so that the controller will only try and save what it expects to be in the view.
 * Therefore when adding a new setting it will need to be added to the view and to the controller.
 * This is done so that the controllers can quickly save their settings without having to check what data has been sent to determine what they need to do.
 * It's more secure as well.
 *
 * This class extends the Controller_Cms_Page class so that it inherits the $page property and the before() function.
 *
 *
 * @todo There's a lot of duplication in here for handing POST requests. We could perhaps add a function to save certain settings and have each controller call that function with the settings it wants updated. The current approach gives more flexibility for settings which don't work in a standard way though. At the moment going to stick with the duplication/flexibility, something to think about later though.
 * @package	BoomCMS
 * @category	Controllers
 */
class Boom_Controller_Cms_Page_Settings extends Controller_Cms_Page
{
	/**
	 * Holds the parent page.
	 * Used for inheriting page settings from the parent.
	 *
	 * @var	Model_Page
	 */
	protected $_parent;

	/**
	 *
	 * @var	string	Directory where views used by this class are stored.
	 */
	protected $_view_directory = 'boom/editor/page/settings';

	/**
	 * **The request method of the current request.**
	 *
	 * The functions in this class do different things depending on whether the request is GET or POST.
	 * GET requests show a form to edit settings, POST requests update the settings.
	 *
	 * This is easy enough to get from [Request::method()] but the functions in this class use:
	 *
	 * 	if (request method is get)
	 *	elseif (request method is post)
	 *	endif
	 *
	 * So making the request method available in a class property avoid us calling [Request::method()] twice in each function.
	 *
	 * The value of this is set in [Boom_Controller_Cms_Page_Settings::before()]
	 *
	 * @var	integer
	 */
	public $method;

	public function before()
	{
		parent::before();

		// Assign the request method to $this->method
		$this->method = $this->request->method();
	}

	/**
	 * **Edit the page admin settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Internal name
	 *
	 *
	 * @uses	Boom_Controller::authorization()
	 * @uses	Boom_Controller::log()
	 */
	public function action_admin()
	{
		// Permissions check
		$this->authorization('edit_page_admin', $this->page);

		if ($this->method === Request::GET)
		{
			// GET request - display the admin settings form.
			$this->template = View::factory("$this->_view_directory/admin", array(
				'page'	=>	$this->page,
			));
		}
		elseif ($this->method === Request::POST)
		{
			// Log the action.
			$this->log("Saved admin settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Set the new page internal name and save the page.
			$this->page
				->values(array(
					'internal_name'		=>	$this->request->post('internal_name'),
				))
				->update();
		}
	}

	/**
	 * **Edit the child page settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *    * Default child template
	 *    * Child ordering policy
	 *  * Advanced:
	 *    * Children visible in nav
	 *    * Children visible in CMS nav
	 *    * Default child URL prefix
	 *    * Default grandchild template
	 *
	 * @uses	Boom_Controller::authorization()
	 * @uses	Boom_Controller::log()
	 */
	public function action_children()
	{
		// Permissions check
		// These settings are divided into basic and advanced.
		// We only need to check for the basic permissions here
		// If they can't edit the basic stuff then they shouldn't have the advanced settings either.
		$this->authorization('edit_page_children_basic', $this->page);

		// Is the current user allowed to edit the advanced settings?
		$allow_advanced = $this->auth->logged_in('edit_page_children_advanced');

		if ($this->method === REQUEST::GET)
		{
			// Show the child page settings form.

			// Get the id and names of all the templates in the database.
			// These are used by both the basic and the advanced settings.
			$templates = ORM::factory('Template')
				->names();

			// Get the current child ordering policy column and direction.
			list($child_order_colum, $child_order_direciton) = $this->page->children_ordering_policy();

			// Create the main view with the basic settings
			$this->template = View::factory("$this->_view_directory/children", array(
				'default_child_template'	=>	($this->page->children_template_id != 0)? $this->page->children_template_id : $this->page->version()->template_id,
				'templates'			=>	$templates,
				'child_order_column'		=>	$child_order_colum,
				'child_order_direction'	=>	$child_order_direciton,
				'allow_advanced'		=>	$allow_advanced,
			));

			// If we're showing the advanced settings then set the neccessary variables.
			if ($allow_advanced)
			{
				// Add the view for the advanced settings to the main view.
				$this->template->set(array(
					'default_grandchild_template'	=>	($this->page->grandchild_template_id != 0)? $this->page->grandchild_template_id : $this->page->version()->template_id,
					'page'					=>	$this->page,
					'templates'				=>	$templates,
				));
			}
		}
		elseif ($this->method === Request::POST)
		{
			// POST request - update the child page settings.
			// Get the POST data.
			$post = $this->request->post();

			// Log the action.
			$this->log("Saved child page settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Set the new advanced settings, if allowed.
			if ($allow_advanced)
			{
				$this->page
					->values($post, array(
						'children_visible_in_nav',
						'children_visible_in_nav_cms',
						'children_url_prefix'	,
						'grandchild_template_id'
					));

				// Updated existing child pages with leftnav visibility or template if required.
				if ($post['visible_in_nav_cascade'] == 1 OR
					$post['visible_in_nav_cms_cascase'] == 1 OR
					$post['child_template_cascade'] == 1
				)
				{
					if ($post['child_template_cascade'] == 1)
					{
						// Which template should be cascaded to the children?
						$child_template = ($this->page->children_template_id == 0)? $this->page->version()->template_id : $this->page->children_template_id;
					}

					$values = array();

					// Cascading the visible_in_nav setting?
					if ($post['visible_in_nav_cascade'] == 1)
					{
						$values['visible_in_nav'] = $this->page->children_visible_in_nav;
					}

					// Cascading the visible_in_nav_cms setting?
					if ($post['visible_in_nav_cms_cascade'] == 1)
					{
						$values['visible_in_nav_cms'] = $this->page->children_visible_in_nav_cms;
					}

					// Cascading the template setting?
					if ($post['child_template_cascade'] == 1)
					{
						$values['template_id'] = $child_template;
					}

					// Run the query
					DB::update('pages')
						->join('page_mptt', 'inner')
						->on('pages.id', '=', 'page_mptt.id')
						->where('parent_id', '=', $this->page->id)
						->set($values)
						->execute();
				}

				// Update the basic values and save the page.
				$this->page
					->children_ordering_policy($post['children_ordering_policy'], $post['child_ordering_direction'])
					->set('children_template_id', $post['children_template_id'])
					->update();

			}
		}
	}

	/**
	 * **Edit page navigation settings.**
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *    * Visible in navigation
	 *    * Visible in CMS navigation
	 *  * Advanced:
	 *    * Parent page
	 *
	 *
	 *  **Performs the following checks before reparenting a page:**
	 *
	 * *	That the current user has the required permission.
	 * *	The new parent ID is different to the current parent ID.
	 * *	The new parent ID is not the current page.
	 * *	The new parent page exists.
	 *
	 * @uses	Boom_Controller::authorization()
	 * @uses	Boom_Controller::log()
	 * @uses	Model_Page_MPTT::move_tp_last_child()
	 * @uses	Model_Page::sort_children()
	 */
	public function action_navigation()
	{
		// Permissions check
		// The need to have a minimum of being able to edit the basic navigation settings.
		// If they can't edit the basic settings they won't be able to edit the advanced settings either.
		$this->authorization('edit_page_navigation_basic', $this->page);

		// Is the current user allowed to edit the advanced settings?
		$allow_advanced = $this->auth->logged_in('edit_page_navigation_advanced');

		if ($this->method === Request::GET)
		{
			// GET request - show the navigation settings form.
			$this->template = View::factory("$this->_view_directory/navigation", array(
				'page'			=>	$this->page,
				'allow_advanced'	=>	$allow_advanced,
			));
		}
		elseif ($this->method === Request::POST)
		{
			// Get the POST data.
			$post = $this->request->post();

			// Allow changing the advanced settings?
			if ($allow_advanced)
			{
				// Reparenting the page?
				// Check that the ID of the parent has been changed and the page hasn't been set to be a child of itself.
				if ($post['parent_id'] AND $post['parent_id'] != $this->page->mptt->parent_id AND $post['parent_id'] != $this->page->id)
				{
					// Check that the new parent ID is a valid page.
					$parent = new Model_Page($post['parent_id']);

					if ($parent->loaded())
					{
						// New parent is a valid page so update the page.
						// Move the page to be the last child of the new parent.
						$this->page
							->mptt
							->move_to_last_child($post['parent_id']);

						// Now sort the parent's children according to it's child ordering policy to move the new page into place.
						$parent->sort_children();
					}
				}
			}

			// Log the action.
			$this->log("Saved navigation settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Update the visible_in_nav and visible_in_nav_cms settings.
			$this->page
				->values(array(
					'visible_in_nav'		=>	$post['visible_in_nav'],
					'visible_in_nav_cms'	=>	$post['visible_in_nav_cms'],
				))
				->update();
		}
	}

	/**
	 * ** Edit page search settings. **
	 *
	 * Settings in this group:
	 *
	 *  * Basic:
	 *     * Keywords
	 *     * Description
	 *  Advanced:
	 *     * External indexing
	 *     * Internal indexing
	 *
	 * @uses	Boom_Controller::authorization()
	 * @uses	Boom_Controller::log()
	 */
	public function action_search()
	{
		// Check permissions
		$this->authorization('edit_page_search_basic', $this->page);

		// Is the current user allowed to edit the advanced settings?
		$allow_advanced = $this->auth->logged_in('edit_page_search_advanced');

		if ($this->method === Request::GET)
		{
			// GET request - show the search settings template.
			$this->template = View::factory("$this->_view_directory/search", array(
				'allow_advanced'	=>	$allow_advanced,
				'page'			=>	$this->page,
			));
		}
		elseif ($this->method === Request::POST)
		{
			// Log the action
			$this->log("Saved search settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Get the POST data.
			$post = $this->request->post();

			// Update the basic settings.
			$this->page
				->values(array(
					'description'	=>	$post['description'],
					'keywords'	=>	$post['keywords']
				));

			// If the current user can edit the advanced settings then update the values for those as well.
			if ($allow_advanced)
			{
				$this->page
					->values(array(
						'external_indexing'	=>	$post['external_indexing'],
						'internal_indexing'	=>	$post['internal_indexing']
					));
			}

			// Save the page.
			$this->page
				->update();
		}
	}

	/**
	 * ** Edit page visibility settings. **
	 *
	 * Settings in this group:
	 *  * visible
	 *  * visible from
	 *  * visible to
	 *
	 * @uses	Boom_Controller::log()
	 * @uses	Boom_Controller::authorization();
	 */
	public function action_visibility()
	{
		// Permissions check.
		$this->authorization('edit_page', $this->page);

		if ($this->method === Request::GET)
		{
			// GET request - show the visiblity form.
			$this->template = View::factory("$this->_view_directory/visibility", array(
				'page'	=>	$this->page,
			));
		}
		elseif ($this->method === Request::POST)
		{
			// POST request - save changes to page visibility settings.

			// Get the POST data
			$post = $this->request->post();

			// Log the action
			$this->log("Updated visibility settings for page " . $this->page->version()->title . " (ID: " . $this->page->id . ")");

			// Update the page settings.
			$this->page
				->values(array(
					'visible_from'	=>	strtotime($post['visible_from']),
					'visible_to'	=>	isset($post['visible_to'])? strtotime($post['visible_to']) : NULL,
					'visible'		=>	$post['visible']
				))
				->update();
		}
	}
} // End Boom_Controller_Cms_Page