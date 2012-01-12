<?php defined('SYSPATH') or die('No direct script access.');

/**
* Sledge CMS controller template.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Site extends Sledge_Controller
{
	/**
	* Set the default template.
	* Used by Controller_Template to know which template to use.
	* @see http://kohanaframework.org/3.0/guide/kohana/tutorials/hello-world#that-was-good-but-we-can-do-better
	* @access public
	* @var string
	*/
	public $template = 'site/standard_template';
	
	/**
	* The requested page.
	* @access protected
	* @var object
	*/
	protected $page;
	
	public function before()
	{
		parent::before();

		// All pages and templates are hard coded for the CMS so this is all site specific.
		// Find the requested page.
		$uri = ($this->request->initial()->uri() == '/')? '' : $this->request->initial()->uri();
		$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', $uri )->find();
		
		// If the page wasn't found by URI load the 404 page.
		if (!$page_uri->loaded() && $uri != 'error/404')
			$page_uri = ORM::factory( 'page_uri' )->where( 'uri', '=', 'error/404' )->find();
		
		// Load the relevant page object.
		//print_r( $page_uri );exit;
		$page = $page_uri->page;

		$page_type = ($this->mode == 'cms' && $this->person->can( 'edit', $page ))? 'cms' : 'site';
		
		// Decorate the page model with a page class.
		// This allows us to change what the page does depending on whether we're in cms or site mode
		// Without changing the page model itself.
		$this->page = Page::factory( $page_type, $page );
		
		// Set the base template.
		if ($page_type == 'cms' && !Arr::get( $_GET, 'state' ) == 'siteeditcms')
		{
			$this->template = View::factory( 'cms/standard_template' );
			$title = $page->title;
			$subtpl_topbar = View::factory( 'ui/subtpl_sites_topbar' );
			
			View::bind_global( 'title', $title );
			View::bind_global( 'subtpl_topbar', $subtpl_topbar );
		}
		else
		{
			$this->template = View::factory( 'site/standard_template' );
		}

		// Add the main subtemplate to the standard template.
		$this->template->subtpl_main = View::factory( $this->page->template->filename );
	}
	
	public function after()
	{	
		// Add the header subtemplate.
		$this->template->subtpl_header = View::factory( 'site/subtpl_header' );
		
		View::bind_global( 'page', $this->page );
		
		parent::after();
	}
}

?>
