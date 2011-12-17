<?php

/**
* Controller for doing stuff with templates.
*
* @package Sledge
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates Ltd
*/
class Controller_Cms_Templates extends Controller_Template_Cms
{

	public function before()
	{	
		parent::before();
		
		$this->template->title = 'Template Manager';
	}
	
	/**
	* Add a new template to the database.
	* Code mostly copied from template.
	*
 	*/
	public function action_add()
	{
		$template = ORM::factory('template');
		$template->version->name = $_POST['name'];
		$template->version->description = $_POST['description'];
		$template->version->filename = $_POST['filename'];
		$template->version->visible = $_POST['visible'];
		$template->save();
	}
	
	/**
	* Edit an existing template.
	* @todo tidy up the code.
	*/
	public function action_edit()
	{
		$id = $this->request->param('id');
		$id = preg_replace( "/[^0-9]+/", "", $id );
		$template = ORM::factory( 'template', $id );
		
		if ( $this->request->method() == 'POST')
		{
			$version = ORM::factory( 'version_template' );
			$version->name = Arr::get( $_POST, 'name', $template->version()->name );
			$version->description = Arr::get( $_POST, 'description', $template->version()->description );
			$version->filename = Arr::get( $_POST, 'filename', $template->version()->filename );
			$version->visible = Arr::get( $_POST, 'visible', $template->version()->visible );
			$version->audit_person = $this->person->id;
			$version->template_id = $template->id;
			$version->save();
			
			$template->active_vid = $version->id;
			$template->save();
		}

		$this->template->subtpl_main = View::factory( 'cms/pages/templates/edit' );
		$this->template->subtpl_main->template = $template;
	}
	
	/**
	* Display the list of available templates.
	*
	*/
	public function action_index()
	{
		$new = $this->find();

		$templates = ORM::factory( 'template' )->join( 'template_v', 'inner' )->on( 'active_vid', '=', 'template_v.id' )->where( 'visible', '=', true )->order_by( 'name' )->find_all();
		
		$this->template->subtpl_main = View::factory( 'cms/pages/templates/index' );
		$this->template->subtpl_main->templates = $templates;	
		$this->template->subtpl_main->new = $new;
	}
	
	/**
	* Scans the templates directory to find new templates and adds them to the database.
	* Code copied from the old templates cms template.
	*
	* @todo make this code nice.
	* @return array Array of added templates.
	*/
	private function find()
	{
		if ($dh = @opendir (APPPATH."views/site/templates"))
		{
			$new = array();
			
			while ($file = readdir($dh))
			{
				if (preg_match('/\.php$/',$file))
				{
					$name = preg_replace( '/\.php$/','' , $file );
					
					$template = ORM::factory('template')->find_by_filename( 'site/templates/' . $name );
					if (!$template->id)
					{
						$template->version->name = $name;
						$template->version->filename = 'site/templates/' . $name;
						$template->save();
						
						$new[] = $template;
					}
				}
			}
			closedir($dh);
			return $new;
		}
	}
}

?>