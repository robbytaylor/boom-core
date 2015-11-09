<div class="b-chunk-library">
    <h1><?= Lang::get('boomcms::editor.chunk.library.heading') ?></h1>
    <p><?= Lang::get('boomcms::editor.chunk.library.about') ?></p>

    <section>
        <h2><?= Lang::get('boomcms::asset.search.type') ?></h2>
        <?= View::make('boomcms::assets.search.type', ['selected' => $chunk->getParam('type')]) ?>
    </section>

    <section id="b-tags-search">
        <h2><?= Lang::get('boomcms::asset.search.tag') ?></h2>
        <?= View::make('boomcms::assets.search.tag', ['tags' => $chunk->getTags()]) ?>
    </section>

    <section>
        <h2><?= Lang::get('boomcms::asset.search.sort') ?></h2>
        <?= View::make('boomcms::assets.search.sort', ['selected' => $chunk->getOrder()]) ?>
    </section>

    <section>
        <h2><?= Lang::get('boomcms::editor.chunk.library.limit') ?></h2>
        <p><?= Lang::get('boomcms::editor.chunk.library.limit_about') ?></p>

        <input type="text" name="limit" value="<?= $chunk->getLimit() ?>">
    </section>
</div>

<div class="buttons">
    <?= $button('trash-o', 'clear-filters', ['class' => 'clear b-button-withtext']) ?>
</div>