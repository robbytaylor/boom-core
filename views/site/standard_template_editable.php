<?
/**
* This is the standard template for editable site pages - i.e. the CMS view.
* This is very similar to the CMS standard template since it requires all the JavaScript stuff.
* But because these pages aren't hard coded the templates require a $page variable.
*
* At one point this was shared with the cms standard template but I think it's important to keep the distinction between hard coded cms pages and variable site page albeit in cms view.
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?= $page->title; ?> | <?=Kohana::$config->load('config')->get('client_name')?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<?/*
	<link rel="stylesheet" type="text/css" href="/sledge/css/ui-smoothness/jquery-ui.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/sledge/css/sledge.ui.css" media="screen" />
	*/?>
	<link rel="stylesheet" type="text/css" href="/sledge/js/tiny_mce/themes/advanced/skins/o2k7/ui.css" />
	<link rel="stylesheet" type="text/css" href="/sledge/js/tiny_mce/themes/advanced/skins/o2k7/ui_silver.css" />
	<link rel="stylesheet" type="text/css" href="/sledge/css/sledge.tagmanager.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="/sledge/css/ui-smoothness/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="/sledge/css/sledge.ui.css" />
	<link rel="stylesheet" type="text/css" href="sledge/css/cms.css" />
</head>
<body>
	<div id="sledge-wysiwyg-toolbar" class="mceEditor o2k7Skin o2k7SkinSilver"></div>

	<?
		echo View::factory( 'ui/subtpl_sites_topbar' );
	?>

	<div id="sledge-dialogs">
		<div id="sledge-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="sledge-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="sledge-page-edit">
		<iframe id="sledge-page-edit-iframe" src="<?= '/ajax' . Request::detect_uri() . URL::query();?>"></iframe>
	</div>

	<script type="text/javascript" src="/sledge/js/sledge.helpers.js"></script>
	<script type='text/javascript' src='/sledge/js/jquery.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.jquery.ui.js'></script>
	<script type='text/javascript' src='/sledge/js/jquery.ui.button.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.plugins.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.config.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.core.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.chunk.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.page.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.helpers.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.tagmanager.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.tagmanager.assets.js'></script>
	<script type='text/javascript' src='/sledge/js/sledge.tagmanager.items.js'></script>

	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.sledge.init('sites', {
				person: {
					rid: <?= $person->id?>,
					firstname: '<?= $person->firstname?>',
					lastname: "<?= $person->lastname?>"
				}
			});

			$.sledge.page.init({
				defaultRid: 1,
				<? 
					if (isset( $page )): 
						echo "id: $page->id,"; 
						echo "vid: ", $page->version->id, ",";
					endif;
				?> 
			});

		})(jQuery);
		//]]>
	</script>
</body>
</html>
