<div class="phdr">
    <b><?= Vars::$LNG['system_language'] ?></b>
</div>
<div class="menu">
    <form action="<?= $this->referer ?>" method="post">
        <p>
        <h3><?= Vars::$LNG['language_select'] ?></h3>
        <?php foreach (Vars::$LNG_LIST as $key => $val) : ?>
        <div>
            <input type="radio" value="<?= $key ?>" name="setlng" <?= ($key == Vars::$LNG_ISO ? 'checked="checked"' : '') ?>/>&#160;
            <?= (file_exists('images/flags/' . $key . '.gif') ? '<img src="images/flags/' . $key . '.gif" alt=""/>&#160;' : '') . $val ?>
            <?= ($key == Vars::$SYSTEM_SET['lng'] ? ' <small class="red">[' . Vars::$LNG['default'] . ']</small>' : '') ?>
        </div>
        <?php endforeach ?>
        </p>
        <p>
            <input type="submit" name="submit" value="<?= Vars::$LNG['apply'] ?>"/>
        </p>
    </form>
</div>
<div class="phdr">
    <a href="<?= $this->referer ?>"><?= Vars::$LNG['back'] ?></a>
</div>