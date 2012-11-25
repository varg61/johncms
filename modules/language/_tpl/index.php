<div class="phdr">
    <b><?= __('system_language') ?></b>
</div>
<div class="menu">
    <form action="<?= $this->referer ?>" method="post">
        <div class="formblock">
            <label><?= __('language_select') ?></label>
            <ul style="list-style: none; padding-left: 0">
                <?php foreach (Vars::$LNG_LIST as $key => $val): ?>
                <li>
                    <input type="radio" value="<?= $key ?>" name="setlng" <?= ($key == Vars::$LNG_ISO ? 'checked="checked"' : '') ?>/>
                    <?= Functions::getIcon('flag_' . $key . '.gif', 11) ?>&#160;
                    <?= $val ?>
                    <?= ($key == Vars::$SYSTEM_SET['lng'] ? ' <small class="red">[' . __('default') . ']</small>' : '') ?>
                </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= __('apply') ?>"/>
        </div>
    </form>
</div>
<div class="phdr">
    <a href="<?= $this->referer ?>"><?= __('back') ?></a>
</div>