<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b><?= lng('admin_panel') ?></b></a> | <?= lng('acl') ?>
</div>
<?php if (isset($this->saved)) : ?>
<div class="gmenu"><p><?= lng('settings_saved') ?></p></div>
<?php endif ?>
<form method="post" action="<?= Vars::$URI ?>">
    <div class="menu">
        <div class="formblock">
            <label><?= lng('forum') ?></label><br/>
            <input type="radio" value="2" name="forum" <?= (Vars::$SYSTEM_SET['mod_forum'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_enabled') ?><br/>
            <input type="radio" value="1" name="forum" <?= (Vars::$SYSTEM_SET['mod_forum'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_authorised') ?><br/>
            <input type="radio" value="3" name="forum" <?= (Vars::$SYSTEM_SET['mod_forum'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('read_only') ?><br/>
            <input type="radio" value="0" name="forum" <?= (!Vars::$SYSTEM_SET['mod_forum'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_disabled') ?>
        </div>
        <div class="formblock">
            <label><?= lng('guestbook') ?></label><br/>
            <input type="radio" value="2" name="guest" <?= (Vars::$SYSTEM_SET['mod_guest'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_enabled_for_guests') ?><br/>
            <input type="radio" value="1" name="guest" <?= (Vars::$SYSTEM_SET['mod_guest'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_enabled') ?><br/>
            <input type="radio" value="0" name="guest" <?= (!Vars::$SYSTEM_SET['mod_guest'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_disabled') ?>
        </div>
        <div class="formblock">
            <label><?= lng('library') ?></label><br/>
            <input type="radio" value="2" name="lib" <?= (Vars::$SYSTEM_SET['mod_lib'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_enabled') ?><br/>
            <input type="radio" value="1" name="lib" <?= (Vars::$SYSTEM_SET['mod_lib'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_authorised') ?><br/>
            <input type="radio" value="0" name="lib" <?= (!Vars::$SYSTEM_SET['mod_lib'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_disabled') ?><br/>
            <input name="libcomm" type="checkbox" value="1" <?= (Vars::$SYSTEM_SET['mod_lib_comm'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('comments') ?>
        </div>
        <div class="formblock">
            <label><?= lng('downloads') ?></label><br/>
            <input type="radio" value="2" name="down" <?= (Vars::$SYSTEM_SET['mod_down'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_enabled') ?><br/>
            <input type="radio" value="1" name="down" <?= (Vars::$SYSTEM_SET['mod_down'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_authorised') ?><br/>
            <input type="radio" value="0" name="down" <?= (!Vars::$SYSTEM_SET['mod_down'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_disabled') ?><br/>
            <input name="downcomm" type="checkbox" value="1" <?= (Vars::$SYSTEM_SET['mod_down_comm'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('comments') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" id="button" value="<?= lng('save') ?>"/>
        </div>
    </div>
    <div class="phdr">
        <small><?= lng('access_help') ?></small>
    </div>
</form>
<p><a href="<?= Vars::$MODULE_URI ?>"><?= lng('admin_panel') ?></a></p>