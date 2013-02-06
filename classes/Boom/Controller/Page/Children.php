<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Generates a list of pages which are children of a specified page. The child pages may be filtered by tag or date and sorted.
 *
 * All controllers in this class use the following POST data:
 * Name			|	Description
 * ---------------------|--------------------------
 * parent			|	Required. Can be the ID of a page or a Model_Page object (if an internal request).
 * page			|	A page number, when retrieving a paginated list.
 * perpage		|	Number of results on each page. Pagination will be disabled when perpage is missing or 0.
 * tag			|	A tag path if filtering by tag.
 * order			|	Which column to order by, can be title or visible_from.
 * direction		|	Which direction to sort in.
 * navigation		|	Whether to treat the page list as a navigation. This restricts results by the visible_in_nav and visible_in_nav_cms settings.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
*/
class Boom_Controller_Page_Children extends Boom_Controller
{
	/**
	 *
	 * @var integer		A year to filter by
	 */
	protected $year;

	/**
	 *
	 * @var integer		A month to filter by.
	 */
	protected $month;

	/**
	 *
	 * @var boolean	Whether to filter the pages by leftnav visibility (i.e. on the visible_in_nav or visible_in_nav_cms columns).
	 */
	protected $nav = FALSE;

	/**
	 * @var integer		Which page in a paginated list to show.
	 */
	protected $page;

	/**
	 *
	 * @var Model_Page	The page to list children of. Taken from $this->request->post('parent')
	 */
	protected $parent;

	/**
	 *
	 * @var integer The number of results to show per paginated list.
	 */
	protected $perpage;

	/**
	 *
	 * @var string	Which column in the page_versions table to sort by. Will be either visible_from or title.
	 */
	protected $sort_column;

	/**
	 *
	 * @var string	Which direction to sort results in. Will be either 'asc' or 'desc'.
	 */
	protected $sort_direction;

	/**
	 *
	 * @var Model_Tag	When set only pages which have this tag will be returned. Set from $this->request->post('tag');
	 */
	protected $tag;

	/**
	 * Processes POST data and populates the class properties.
	 */
	public function before()
	{
		parent::before();

		// Get the parent page.
		if ($this->request->post('parent'))
		{
			// Get it from the POST data.
			$this->parent = $this->request->post('parent');

			// Parent may be an object when this is an internal request - no point instantiating a new object when we already have one.
			if ( ! is_object($this->parent))
			{
				$this->parent = new Model_Page($this->request->post('parent'));
			}
		}
		elseif (Request::$current !== Request::$initial)
		{
			// Use the page from the initial request.
			$this->parent = Request::initial()->param('page');
		}

		// 404 if the parent page doesn't exist.
		if ( ! $this->parent->loaded())
		{
			throw new HTTP_Exception_404;
		}


		// If a sort column is given then use it, otherwise use the parent page default child order column
		list($column, $direction) = $this->parent->children_ordering_policy();

		if ($this->request->post('order'))
		{
			$this->sort_column = ($this->request->post('order') === 'title')? 'version.title' : 'page.visible_from';
		}
		else
		{
			$this->sort_column = $column;
		}

		// If a direction has been set then use that, overwise use the parent default child order direction
		if ( ! $this->request->post('direction'))
		{
			$this->sort_direction = $direction;
		}
		else
		{
			$this->sort_direction = ($this->request->post('direction') === 'desc')? 'desc' : 'asc';
		}

		// Get a tag for filtering pages.
		$tag = $this->request->post('tag');

		if ($tag)
		{
			// Try and get the tag from the database. $_POST['tag'] should be a tag path.
			$tag = new Model_Tag(array('path' => $tag));

			// Only set the tag property if the tag was found.
			// Then we always know that if $this->tag is set then it's a valid tag.
			if ($tag->loaded())
			{
				$this->tag = $tag;
			}
		}

		// Set the number of results perpage, has to be 0 or more.
		// 0 results perpage will disable pagination.
		$this->perpage = max(0, $this->request->post('perpage'));

		// Only set properties which are used for pagination if pagination is enabled.
		if ($this->perpage > 0)
		{
			// Set the page number, page number has to be 1 or more.
			$this->page = max(1, $this->request->post('page'));
		}

		// Set whether we're filter by nav visibility.
		if ($this->request->post('nav'))
		{
			$this->nav = TRUE;
		}

		// Set filtering by year and month.
		if ($this->request->post('year'))
		{
			$this->year = $this->request->post('year');
		}

		if ($this->request->post('month'))
		{
			$this->month = $this->request->post('month');
		}
	}

	/**
	 * Return the child page list in JSON format.
	 */
	public function action_json()
	{
		list($query, $total) = $this->build_query();

		// Set the select columns.
		// This is done individually in each action because different output formats needs different columns
		$results = $query
			->find_all()
			->as_array();

		$pages = array();

		foreach ($results as & $result)
		{
			$result = array(
				'id'			=>	$result->id,
				'title'			=>	$result->version()->title,
				'url'			=>	(string) $result->url(),
				'visible'		=>	(int) $result->is_visible(),
				'has_children'	=>	(int) $result->mptt->has_children(),
			);
		}

		$this->response
			->headers('Content-Type', 'application/json')
			->body(json_encode($results));
	}

	/**
	 * Display a HTML formatted list of child pages.
	 * This controller accepts and addition $_POST['template'] parameter to specify which template to use to display the page list.
	 */
	public function action_html()
	{
		$cache_key = "child_page_list:".md5($this->parent->id . "-" . $this->auth->logged_in() . "-" . serialize($this->request->post()));

		// Try and get it from the cache, unless they're logged in.
		if ($this->auth->logged_in() OR ($view = Kohana::cache($cache_key, NULL, 300)) === NULL)
		{
			list($query, $total) = $this->build_query();

			// Execute the query.
			$pages = $query
				->find_all();

			// Get the number of results returned.
			$count = $pages
				->count();

			// Only continue if there are child pages.
			if ($count == 0)
			{
				Kohana::cache($cache_key, "");
				return;
			}

			// Which template to use?
			$template = ($this->request->post('template'))? $this->request->post('template') : "boom/page/children";


			// Include the POST data in the view so that extra paramaters can be passed to the view.
			$view = View::factory($template, array_merge($this->request->post(), array(
				'page'	=>	$this->parent,
				'pages'	=>	$pages,
			)));

			// Pagination is disabled when results per page is 0 or there's only one page of results.
			if ($this->perpage > 0 AND $total > $this->perpage)
			{
				$pagination = Pagination::factory(array(
					'current_page'	=>	array(
						'key'		=>	'page',
						'source'	=>	'mixed',
					),
					'total_items'		=>	$total,
					'items_per_page'	=>	$this->perpage,
					'view'			=>	'pagination/hoop',
					'count_in'			=>	1,
					'count_out'		=>	1,
				));

				// Add pagination values to template.
				$view->set('pagination', $pagination);
			} // End pagination

			// Update the cache.
			// But only if we're not logged in
			// Don't want logged in child page lists getting loaded from cache when not logged in.
			if ( ! $this->auth->logged_in())
			{
				Kohana::cache($cache_key, $view->render());
			}
		}

		// Set the response body.
		$this->response->body($view);
	}

	/**
	 * Builds a database query to retrieve the child pages.
	 * Fucntionality common to all controller functions.
	 *
	 * @return	array	Array of Database_Query_Builder_Select and number of results which matched query (if pagination is enabled)
	 */
	protected function build_query()
	{
		// Build the query.
		$query = ORM::factory('Page')
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->with_current_version($this->editor)
			->join('page_urls', 'inner')
			->on('page.id', '=', 'page_urls.page_id')
			->where('page_mptt.parent_id', '=', $this->parent->id)
			->where('page_urls.is_primary', '=', TRUE)
			->order_by($this->sort_column, $this->sort_direction);

		// Filtering by date from?
		if ($this->year)
		{
			$query->where(DB::expr('year(from_unixtime(visible_from))'), '=', $this->year);
		}

		// Filtering by date to?
		if ($this->month)
		{
			$query->where(DB::expr('month(from_unixtime(visible_from))'), '=', $this->month);
		}

		// Filtering for navigation?
		if ($this->nav)
		{
			// Which column to use for navigation visibility
			$nav_visibility_column = ($this->editor->state_is(Editor::EDIT))? 'visible_in_nav_cms' : 'visible_in_nav';

			// Add a where clause on the navigation visibility column
			$query->where($nav_visibility_column, '=', TRUE);
		}

		// Filtering by tag?
		if ($this->tag)
		{
			$query->where('tag', '=', $this->tag);
		}

		// Pagination
		$total = NULL;

		if ($this->perpage > 0)
		{
			$total = clone $query;
			$total = $total->count_all();

			$query
				->offset(($this->page - 1) * $this->perpage)
				->limit($this->perpage);
		}

		return array($query, $total);
	}
}