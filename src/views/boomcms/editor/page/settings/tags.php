<div id="b-tags">
    <h1><?= trans('boomcms::settings.tags.heading') ?></h1>

    <section id="free">
        <h2><?= trans('Free tags') ?></h2>

        <ul class="b-tags-list" data-group="">
            <?php if (isset($tags[''])): ?>
                <?php foreach ($tags[''] as $tag): ?>
                    <?= new BoomCMS\UI\Tag($tag) ?>
                <?php endforeach ?>
            <?php endif ?>

            <li class="b-tag">
                <form class="b-tags-add">
                    <input type="text" value="" class="b-tags-add-name" />
                    <?= $button('plus', 'Add tag') ?>
                </form>
            </li>
        </ul>
    </section>

    <section id="grouped">
        <h2><?= trans('Grouped tags') ?></h2>

        <ul class="b-tags-grouped">
            <?php foreach (array_keys($tags) as $group): ?>
                <?php if ($group): ?>
                    <li>
                        <p><?= $group ?></p>

                        <ul class="b-tags-list" data-group="<?= $group ?>">
                            <?php if (isset($tags[$group])): ?>
                                <?php foreach ($tags[$group] as $tag): ?>
                                    <?= new BoomCMS\UI\Tag($tag) ?>
                                <?php endforeach ?>
                            <?php endif ?>

                            <li class="b-tag">
                                <form class="b-tags-add">
                                    <input type="text" value="" class="b-tags-add-name" />
                                    <?= $button('plus', 'Add tag') ?>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php endif ?>
            <?php endforeach ?>

            <li class="b-tags-newgroup">
                <p>Add a new tag group</p>

                <form>
                    <input type="text" value="" class="b-tags-newgroup-name" />
                    <?= $button('plus', 'Add tag group') ?>
                </form>
            </li>
        </ul>
    </section>
</div>
