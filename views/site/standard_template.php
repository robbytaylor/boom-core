<?php
/**
* This is the base template for non-editable site pages.
* Nothing really special here - it requires a $page variable representing the page and doesn't include any jQuery stuff.
*
* Rendered by Sledge_Controller::after() via Controller_Site::before();
*
*********************** Variables **********************
*	global $page			****	Instance of Page. Not Model_Page, but it can be used in the same way.
*	global $actual_person	****	Instance of Model_Person		****	The current logged in user.
*	global $person			****	Instance of Model_Person		****	The active user.
*	global $mode			****	string							****	'cms' or 'site'.
*	global $request			****	Instance of Request				****	See http://kohanaframework.org/3.2/guide/api/Request
********************************************************
*
* @uses Kohana::$config
* @uses URL::base()
* @uses View::factory()
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="https://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<base target="_top" />
		<title><?= $page->title ?> | <?=Kohana::$config->load('config')->get('client_name')?></title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta name="description" content="<?= htmlspecialchars( $page->description );?>" />
		<meta name="keywords" content="<?= htmlspecialchars( $page->keywords );?>" />
		
		<?= View::factory( 'site/css' ); ?>

		<?
			if ($page->indexed):
				echo "<meta name='robots' content='index, follow' />";
			else:
				echo "<meta name='robots' content='noindex, nofollow' />";
			endif;
		?>
		
		<?
			echo View::factory( 'site/subtpl_header' );
		?>

		<?
			if ( $page->enable_rss )
			{
				echo "<link rel='alternate' type='application/rss+xml' title='RSS' href='" . URL::base( $request ) . "rss/" . $page->get_primary_uri() . "' />";
			}
		?>
	</head>
	<body>
		<?			
			if ($mode == 'cms')
			{
				echo new View('site/nav/top');
			}

			echo $subtpl_main;

			if (Kohana::$config->load('config')->get('include_analytics') === true)
			{
				echo View::factory( 'site/analytics' );
			}
		?>	
		
	<?= View::factory('profiler/stats'); ?>
	</body>
</html>
