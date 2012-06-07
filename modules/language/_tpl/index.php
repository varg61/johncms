<div class="phdr">
    <b><?= lng('system_language') ?></b>
</div>
<div class="menu">
    <form action="<?= $this->referer ?>" method="post">
        <p>
        <h3><?= lng('language_select') ?></h3>
        <?php foreach (Vars::$LNG_LIST as $key => $val) : ?>
        <div>
            <input type="radio" value="<?= $key ?>" name="setlng" <?= ($key == Vars::$LNG_ISO ? 'checked="checked"' : '') ?>/>
            <?= Functions::getImage('flag_' . $key . '.gif') ?>&#160;
            <?= $val ?>
            <?= ($key == Vars::$SYSTEM_SET['lng'] ? ' <small class="red">[' . lng('default') . ']</small>' : '') ?>
        </div>
        <?php endforeach ?>
        </p>
        <p>
            <input type="submit" name="submit" value="<?= lng('apply') ?>"/>
        </p>
    </form>
</div>
<div class="phdr">
    <a href="<?= $this->referer ?>"><?= lng('back') ?></a>
</div>