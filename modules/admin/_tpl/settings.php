<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b><?= lng('admin_panel') ?></b></a> | <?= lng('system_settings') ?>
</div>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label><?= lng('common_settings') ?></label><br/>
            <?= lng('site_copyright') ?>:<br/>
            <input type="text" name="copyright" value="<?= htmlentities(Vars::$SYSTEM_SET['copyright'], ENT_QUOTES, 'UTF-8') ?>"/><br/>
            <?= lng('site_email') ?>:<br/>
            <input name="madm" maxlength="50" value="<?= htmlentities(Vars::$SYSTEM_SET['email']) ?>"/><br/>
            <?= lng('file_maxsize') ?> (kb):<br/>
            <input type="text" name="flsz" value="<?= intval(Vars::$SYSTEM_SET['flsz']) ?>"/><br/>
            <input name="gzip" type="checkbox" value="1" <?= (Vars::$SYSTEM_SET['gzip'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('gzip_compress') ?>
        </div>
        <div class="formblock">
            <label><?= lng('clock_settings') ?></label><br/>
            <span style="font-weight:bold; background-color:#C0FFC0"><?= date("H:i", time() + Vars::$SYSTEM_SET['timeshift'] * 3600) ?></span> <?= lng('system_time') ?><br/>
            <span style="font-weight:bold; background-color:#FFC0C0"><?= date("H:i") ?></span> <?= lng('server_time') ?><br/>
            <input type="text" name="timeshift" size="2" maxlength="3" value="<?= Vars::$SYSTEM_SET['timeshift'] ?>"/> <?= lng('time_shift') ?> (+-12)
        </div>
        <div class="formblock">
            <label><?= lng('meta_tags') ?></label><br/>
            <?= lng('meta_keywords') ?>:<br/>
            <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="meta_key"><?= Vars::$SYSTEM_SET['meta_key'] ?></textarea><br/>
            <?= lng('meta_description') ?>:<br/>
            <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="meta_desc"><?= Vars::$SYSTEM_SET['meta_desc'] ?></textarea>
        </div>
        <div class="formblock">
            <label><?= lng('design_template') ?></label><br/>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
    </div>
</form>
<div class="phdr">
    &#160;
</div>
<p><a href="<?= Vars::$MODULE_URI ?>"><?= lng('admin_panel') ?></a></p>