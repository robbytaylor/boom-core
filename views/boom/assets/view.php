<div id="b-assets-view">
	<form onsubmit="return false;" class="b-form">
		<?= Form::hidden('csrf', Security::token()) ?>
		<?= Form::hidden('id', $asset->getId(), array('id' => 'asset_id')) ?>

		<div class="b-assets-preview">
			<a href="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->getId(), 'width' => 600, 'height' => 500)) ?>"
				title="Click for larger view"
				class="boom-asset-preview">
				<img src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->getId(), 'width' => 0, 'height' => 300, 'quality' => 85, 'crop' => 0)) ?>">
			</a>

			<div class="ui-dialog-buttonpane">
				<?= \Boom\UI::button('back', __('Back'), array('class' => 'b-assets-back', 'rel' => $asset->getId())) ?>
				<?= \Boom\UI::button('delete', __('Delete'), array('class' => 'b-assets-delete', 'rel' => $asset->getId())) ?>
				<?= \Boom\UI::button('download', __('Download'), array('class' => 'b-assets-download', 'rel' => $asset->getId())) ?>
				<?= \Boom\UI::button('replace', __('Replace'), array('class' => 'b-assets-replace', 'rel' => $asset->getId())) ?>
			</div>
		</div>

		<div class="boom-tabs">
			<ul>
				<li><a href="#b-assets-view-info<?=$asset->getId();?>"><?=__('Info')?></a></li>
				<li><a href="#b-assets-view-attributes<?=$asset->getId();?>"><?=__('Attributes')?></a></li>
				<li class="b-dialog-hidden"><a href="#b-assets-view-tags<?=$asset->getId();?>"><?=__('Tags')?></a></li>
				<? if (count($asset->getOldFiles()) > 0): ?>
					<li class="b-dialog-hidden"><a href="#b-assets-view-files<?=$asset->getId();?>"><?=__('Previous Files')?></a></li>
				<? endif; ?>
			</ul>

			<div id="b-assets-view-attributes<?=$asset->getId();?>" class="b-assets-view-attributes ui-helper-left">
				<label>
					<?=__('Title')?>
					<input type="text" id="title" name="title" value="<?= $asset->getTitle() ?>" />
				</label>

				<label>
					<?=__('Description')?>
					<textarea id="description" name="description"><?= $asset->getDescription() ?></textarea>
				</label>

				<label>
					<?=__('Credits')?>
					<textarea id="credits" name="credits"><?= $asset->getCredits() ?></textarea>
				</label>

				<label>
					<?= __('Visible from') ?>
					<input type="text" id="visible_from" name="visible_from" class="boom-datepicker" value="<?= $asset->getVisibleFrom()->format('d F Y h:m') ?>" />
				</label>

				<? if ( ! $asset instanceof \Boom\Asset\Type\Image): ?>
					<label for="thumbnail">Thumbnail asset ID
						<input type="text" id="thumbnail" name="thumbnail_asset_id" value="<?= $asset->thumbnail_asset_id ?>" size="4" />
					</label>
				<? endif; ?>

				<?= \Boom\UI::button('accept', __('Save'), array('class' => 'b-assets-save b-button-withtext', 'rel' => $asset->getId())) ?>
			</div>

			<div id="b-assets-view-info<?=$asset->getId();?>" class="ui-helper-left">
				<dl>
					<dt><?=__('Type')?></dt>
					<dd><?= $asset->getType() ?></dd>

					<dt><?=__('Filesize')?></dt>
					<dd><span id='filesize'><?= Text::bytes($asset->getFilesize()) ?></dd>

					<? if ($asset instanceof \Boom\Asset\Type\Image): ?>
						<dt><?=__('Dimensions')?></dt>
						<dd><?=$asset->getWidth()?> x <?=$asset->getHeight()?></dd>
					<? endif; ?>

					<? if ($uploader = $asset->getUploadedBy()): ?>
						<dt><?=__('Uploaded by')?></dt>
						<dd><?= $uploader->name ?></dd>
					<? endif; ?>

					<dt><?=__('Uploaded on')?></dt>
					<dd><?= $asset->getUploadedTime()->format('d F Y h:i:s') ?></dd>

					<? if ( ! $asset instanceof \Boom\Asset\Type\Image): ?>
						<dt><?=__('Downloads')?></dt>
						<dd><?= Num::format($asset->getDownloads(), 0) ?></dd>
					<? endif ?>
				</dl>
			</div>

			<div id="b-assets-view-tags<?=$asset->getId();?>" class="b-dialog-hidden ui-helper-left">
				<?= View::factory('boom/tags/list', array('tags' => $tags)) ?>
			</div>

			<? if (count($asset->getOldFiles()) > 0): ?>
				<div id="b-assets-view-files<?= $asset->getId() ?>" class="b-dialog-hidden ui-helper-left">
					<p>
						These files were previously assigned to this asset but were replaced.
					</p>
					<ul>
						<? foreach ($asset->getOldFiles() as $timestamp => $filename): ?>
							<li>
								<a href="/cms/assets/restore/<?= $asset->getId() ?>?timestamp=<?= $timestamp ?>">
									<img src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->getId(), 'width' => 160, 'height' => 160, 'quality' => 85, 'crop' => 1)) ?><? if ($timestamp): ?>?timestamp=<?= $timestamp ?><? endif; ?>" />
								</a>
								<?=date("d F Y H:i", $timestamp);?>
							</li>
						<? endforeach; ?>
					</ul>
				</div>
			<? endif; ?>
		</div>
	</form>
</div>