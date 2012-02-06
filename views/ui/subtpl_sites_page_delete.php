<?php
/**
* Delete page confirmation.
*
* Rendered by: Controller_Cms_Page::action_delete()
*
*********************** Variables **********************
*	$page	****	Instance of Model_Page	****	The page being deleted
********************************************************
*
*/
?>
<p>Are you sure you want to delete this page? This cannot be recovered.</p>

<?
	$msg = '';

	$count = $page->mptt->count();
	if ($count > 0):
		$titlelist = '';
		
		foreach ($page->mptt->descendants() as $pi):
			$titlelist .= "<li>" . $pi->page->title . "</li>";
		endforeach;
		
		$titlelist = preg_replace("/\, $/", "", $titlelist);
		
		$msg = "<p><strong>Warning:</strong><br />Deleting this page will make it's " . $count . " child ";

		$msg .= ($count !== 1) ? 'page' : 'pages';

		$msg .=  " inaccessible:</p><div id=\"sledge-page-delete-children\"><ul>" . $titlelist . "</ul></div>";
	endif;
	
	echo $msg;
?>

<p>Click 'Okay' to delete, or 'Cancel' to keep the page.</p>
