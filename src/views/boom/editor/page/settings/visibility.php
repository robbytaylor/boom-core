<form class="b-form-settings">
    <label>
        <?= Lang::get('Visible') ?>

        <select name="visible" id="b-page-visible">
            <option value="1"<?php if ($page->isVisibleAtAnyTime()): ?> selected="selected"<?php endif ?>>Yes</option>
            <option value="0"<?php if ( ! $page->isVisibleAtAnyTime()): ?> selected="selected"<?php endif ?>>No</option>
        </select>
    </label>

    <label>
        <?= Lang::get('Visible from') ?>

        <input type="text" name="visible_from" value="<?= $page->getVisibleFrom()->format('d F Y H:i') ?>" id="visible-from" classs="boom-datepicker" />
    </label>

    <label>
        <?= Lang::get('Visible until') ?>

        <input type="checkbox" name="toggle_visible_to" value="1" id="toggle-visible"<?php if ($page->getVisibleTo() !== null): ?> checked="checked"<?php endif ?> />
        <input type="text" name="visible_to" value="<?= ($page->getVisibleTo() != null) ? $page->getVisibleTo()->format('d F Y H:i') : date("d F Y H:i", time()) ?>" id="visible-to" class="boom-datepicker" />
    </label>
</form>
