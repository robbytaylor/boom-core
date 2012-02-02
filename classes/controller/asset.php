<?php defined('SYSPATH') or die('No direct script access.');

/**
* Asset controller.
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Asset extends Kohana_Controller
{
	private $asset;
	
	public function before()
	{
		$id = $this->request->param( 'id' );
		
		$this->asset = ORM::factory( 'asset', $id );
		if (!$this->asset->loaded())
		{
			exit;
		}
	}
	
	/**
	* Search index method.
	* Performs all searching etc.
	*
	*/
	public function action_view()
	{

		$this->asset = Asset::factory( $this->asset->type, $this->asset );
		echo $this->asset->show();
			
		exit();
	}
	
	public function action_save()
	{
		$this->asset->title = Arr::get( $_POST, 'title' );
		$this->asset->filename = Arr::get( $_POST, 'filename' );
		$this->asset->description = Arr::get( $_POST, 'description' );
		$this->asset->visible_from = strtotime( Arr::get( $_POST, 'visible_from' ) );
		$this->asset->save();
		
		$this->request->redirect( '/cms/assets/view/' . $this->asset->id );
	}
}

?>
