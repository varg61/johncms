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
<form action="<?= Vars::$URI ?>" method="post">
<div class="<?= (Vars::$USER_SYS['reg_open'] ? 'g' : 'r') ?>menu">
    <div class="formblock">
        <label><?= lng('registration') ?></label><br/>
        <input type="radio" value="1" name="reg_open" <?= (Vars::$USER_SYS['reg_open'] ? 'checked="checked"' : '') ?>/>&#160;
        <?= lng('registration_open') ?><br/>
        <input type="radio" value="0" name="reg_open" <?= (!Vars::$USER_SYS['reg_open'] ? 'checked="checked"' : '') ?>/>&#160;
        <?= lng('registration_closed') ?>
    </div>
    <div class="formblock">
        <input name="reg_moderation" type="checkbox" value="1" <?= (Vars::$USER_SYS['reg_moderation'] ? 'checked="checked"' : '') ?>/>&#160;
        <?= lng('registration_moderation') ?><br/>
        <input name="reg_welcome" type="checkbox" value="1" <?= (Vars::$USER_SYS['reg_welcome'] ? 'checked="checked"' : '') ?>/>&#160;
        <?= lng('welcome_message') ?><br/>
        <input name="reg_email" type="checkbox" value="1" <?= (Vars::$USER_SYS['reg_email'] ? 'checked="checked"' : '') ?>/>&#160;
        <?= lng('registration_email') ?><br/>
        <input name="reg_quarantine" type="checkbox" value="1" <?= (Vars::$USER_SYS['reg_quarantine'] ? 'checked="checked"' : '') ?>/>&#160;
        <a href=""><?= lng('registration_quarantine') ?></a><br/>
    </div>
    <?php if (Vars::$USER_RIGHTS == 9) : ?>
    </div>
    <div class="menu">
        <div class="formblock">
            <label><?= lng('permissions') ?></label><br/>
            <input name="autologin" type="checkbox" value="1" <?= (Vars::$USER_SYS['autologin'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('autologin') ?><br/>
            <input name="change_sex" type="checkbox" value="1" <?= (Vars::$USER_SYS['change_sex'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('change_sex') ?><br/>
            <input name="change_status" type="checkbox" value="1" <?= (Vars::$USER_SYS['change_status'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('change_status') ?><br/>
            <input name="upload_avatars" type="checkbox" value="1" <?= (Vars::$USER_SYS['upload_avatars'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('upload_avatars') ?><br/>
            <input name="upload_animation" type="checkbox" value="1" <?= (Vars::$USER_SYS['upload_animation'] ? 'checked="checked"' : '') ?>/>&#160;
            <?= lng('upload_animation') ?>
        </div>
    <div class="formblock">
        <input name="change_nickname" type="checkbox" value="1" <?= (Vars::$USER_SYS['change_nickname'] ? 'checked="checked"' : '') ?>/>&#160;
        <?= lng('change_nickname_allow') ?><br/>
        <input name="change_period" size="2" value="<?= Vars::$USER_SYS['change_period'] ?>" maxlength="2"/>&#160;
        <?= lng('how_many_days') ?><br/>
        <input name="digits_only" type="checkbox" value="1" <?= (Vars::$USER_SYS['digits_only'] ? 'checked="checked"' : '') ?>/>&#160;
        <?= lng('digits_only') ?>
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
    <?php endif; ?>
    <div class="formblock">
        <input type="submit" name="submit" value="<?= lng('save') ?>"/>
    </div>
    <input type="hidden" name="token" value="<?= $this->token ?>"/>
</div>
</form>
<div class="phdr">
    <?php if (Vars::$USER_RIGHTS == 9) : ?>
    <a href="<?= Vars::$URI ?>?reset"><?= lng('reset_settings') ?></a>
    <?php else : ?>
    &#160;
    <?php endif; ?>
</div>
<p><a href="<?= Vars::$MODULE_URI ?>"><?= lng('admin_panel') ?></a></p>