<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b><?= lng('admin_panel') ?></b></a> | <?= lng('users') ?>
</div>

<?php if (isset($this->save)) : ?>
<div class="gmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('settings_saved') ?>
</div>
<?php endif ?>

<?php if (isset($this->reset)) : ?>
<div class="gmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('settings_default') ?>
</div>
<?php endif ?>

<div class="menu">
    <form action="<?= Vars::$URI ?>?act=users_settings" method="post">
        <div class="formblock">
            <label><?= lng('registration') ?></label><br/>
            <input type="radio" value="3" name="reg_mode" <?= ($this->setUsers['reg_mode'] == 3 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('access_enabled') ?><br/>
            <input type="radio" value="2" name="reg_mode" <?= ($this->setUsers['reg_mode'] == 2 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('access_with_moderation') ?><br/>
            <input type="radio" value="1" name="reg_mode" <?= ($this->setUsers['reg_mode'] == 1 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('access_disabled') ?>
        </div>
        <div class="formblock">
            <label><?= lng('permissions') ?></label><br/>
            <input name="change_status" type="checkbox" value="1" <?= ($this->setUsers['change_status'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('change_status') ?><br/>
            <input name="upload_avatars" type="checkbox" value="1" <?= ($this->setUsers['upload_avatars'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('upload_avatars') ?><br/>
            <input name="upload_animation" type="checkbox" value="1" <?= ($this->setUsers['upload_animation'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('upload_animation') ?>
        </div>
        <div class="formblock">
            <label><?= lng('antiflood') ?></label><br/>
            <input type="radio" name="flood_mode" value="3" <?= ($this->setUsers['flood_mode'] == 3 ? 'checked="checked"' : '') ?>/>
            <input name="flood_day" size="3" value="<?= $this->setUsers['flood_day'] ?>" maxlength="3"/>&#160;
            <?= lng('sec') . ', ' . lng('day') ?><br/>
            <input type="radio" name="flood_mode" value="4" <?= ($this->setUsers['flood_mode'] == 4 ? 'checked="checked"' : '') ?>/>
            <input name="flood_night" size="3" value="<?= $this->setUsers['flood_night'] ?>" maxlength="3"/>&#160;
            <?= lng('sec') . ', ' . lng('night') ?><br/>
            <input type="radio" name="flood_mode" value="2" <?= ($this->setUsers['flood_mode'] == 2 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('autoswitch') ?><br/>
            <input type="radio" name="flood_mode" value="1" <?= ($this->setUsers['flood_mode'] == 1 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('adaptive') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=users_settings&amp;reset"><?= lng('reset_settings') ?></a>
</div>
<p><a href="<?= Vars::$MODULE_URI ?>"><?= lng('admin_panel') ?></a></p>