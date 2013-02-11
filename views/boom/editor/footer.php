	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/boom.jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.page.js") ?>
<? if ($register_page): ?>
	<?= HTML::script("media/boom/js/boom.chunk.js") ?>
	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/boom.tagmanager.js") ?>
	<?= HTML::script("media/boom/js/boom.items.js") ?>
	<?= HTML::script("media/boom/js/boom.tags.js") ?>
	<?= HTML::script("media/boom/js/boom.assets.js") ?>
	<?= HTML::script("media/boom/js/boom.links.js") ?>
<? endif; ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){

			$.boom.init('sites', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.boom.page.init({
				defaultRid: 1,
				<?
					if (isset( $page )):
						echo "id: $page->id,";
						echo "vid: ", $page->version()->id;
					endif;
				?>
			});

			<? if ($register_page): ?>
				$.boom.page.register({
					rid: <?=$page->id;?>,
					vid: <?=$page->version()->id;?>,
					writable: <?= (int) ($auth->logged_in('edit_page_content', $page) OR $page->was_created_by($person)) ?>,
					editorOptions: {}
				});
			<? endif; ?>
		})(jQuery);
		//]]>
	</script>
</body>
</html>