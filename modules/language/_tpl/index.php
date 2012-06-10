<div class="phdr">
    <b><?= lng('system_language') ?></b>
</div>
<div class="menu">
    <form action="<?= $this->referer ?>" method="post">
        <div class="formblock">
            <label><?= lng('language_select') ?></label>
            <ul style="list-style: none; padding-left: 0">
                <?php foreach (Vars::$LNG_LIST as $key => $val): ?>
                <li>
                    <input type="radio" value="<?= $key ?>" name="setlng" <?= ($key == Vars::$LNG_ISO ? 'checked="checked"' : '') ?>/>
                    <?= Functions::getIcon('flag_' . $key . '.gif', 11) ?>&#160;
                    <?= $val ?>
                    <?= ($key == Vars::$SYSTEM_SET['lng'] ? ' <small class="red">[' . lng('default') . ']</small>' : '') ?>
                </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('apply') ?>"/>
        </div>
    </form>
</div>
<div class="phdr">
    <a href="<?= $this->referer ?>"><?= lng('back') ?></a>
</div>