<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b><?= lng('admin_panel') ?></b></a> | <?= lng('users') ?>
</div>

<?php if (isset($this->save)) : ?>
<div class="gmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('settings_saved') ?>
</div>
<?php endif; ?>

<?php if (isset($this->reset)) : ?>
<div class="gmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('settings_default') ?>
</div>
<?php endif; ?>

<div class="menu">
    <form action="<?= Vars::$URI ?>?act=users_settings" method="post">
        <div class="formblock">
            <label><?= lng('registration') ?></label><br/>
            <input type="radio" value="3" name="reg_mode" <?= (Vars::$USER_SYS['reg_mode'] == 3 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('access_enabled') ?><br/>
            <input type="radio" value="2" name="reg_mode" <?= (Vars::$USER_SYS['reg_mode'] == 2 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('access_with_moderation') ?><br/>
            <input type="radio" value="1" name="reg_mode" <?= (Vars::$USER_SYS['reg_mode'] == 1 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('access_disabled') ?>
        </div>
        <div class="formblock">
            <label><?= lng('permissions') ?></label><br/>
            <input name="change_status" type="checkbox" value="1" <?= (Vars::$USER_SYS['change_status'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('change_status') ?><br/>
            <input name="upload_avatars" type="checkbox" value="1" <?= (Vars::$USER_SYS['upload_avatars'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('upload_avatars') ?><br/>
            <input name="upload_animation" type="checkbox" value="1" <?= (Vars::$USER_SYS['upload_animation'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('upload_animation') ?>
        </div>
        <div class="formblock">
            <label><?= lng('antiflood') ?></label><br/>
            <input type="radio" name="flood_mode" value="3" <?= (Vars::$USER_SYS['flood_mode'] == 3 ? 'checked="checked"' : '') ?>/>
            <input name="flood_day" size="3" value="<?= Vars::$USER_SYS['flood_day'] ?>" maxlength="3"/>&#160;
            <?= lng('sec') . ', ' . lng('day') ?><br/>
            <input type="radio" name="flood_mode" value="4" <?= (Vars::$USER_SYS['flood_mode'] == 4 ? 'checked="checked"' : '') ?>/>
            <input name="flood_night" size="3" value="<?= Vars::$USER_SYS['flood_night'] ?>" maxlength="3"/>&#160;
            <?= lng('sec') . ', ' . lng('night') ?><br/>
            <input type="radio" name="flood_mode" value="2" <?= (Vars::$USER_SYS['flood_mode'] == 2 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('autoswitch') ?><br/>
            <input type="radio" name="flood_mode" value="1" <?= (Vars::$USER_SYS['flood_mode'] == 1 ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('adaptive') ?>
        </div>
        <div class="formblock">
            <label><?= lng('for_guests') ?></label><br/>
            <input name="view_online" type="checkbox" value="1" <?= (Vars::$USER_SYS['view_online'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('view_online') ?><br/>
            <input name="viev_history" type="checkbox" value="1" <?= (Vars::$USER_SYS['viev_history'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('viev_history') ?><br/>
            <input name="view_userlist" type="checkbox" value="1" <?= (Vars::$USER_SYS['view_userlist'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('view_userlist') ?><br/>
            <input name="view_profiles" type="checkbox" value="1" <?= (Vars::$USER_SYS['view_profiles'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('view_profiles') ?><br/>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
        <input type="hidden" name="token" value="<?= $this->token ?>"/>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=users_settings&amp;reset"><?= lng('reset_settings') ?></a>
</div>
<p><a href="<?= Vars::$MODULE_URI ?>"><?= lng('admin_panel') ?></a></p>