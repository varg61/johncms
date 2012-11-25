<ul class="nav">
    <li><h1 class="section-warning"><?= __('users') ?> :: <?= __('settings') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->save)) : ?>
    <div class="form-block confirm"><?= __('settings_saved') ?></div>
    <?php endif ?>
    <?php if (isset($this->reset)) : ?>
    <div class="form-block confirm"><?= __('settings_default') ?></div>
    <?php endif ?>

    <form action="<?= Vars::$URI ?>?act=users_settings" method="post">
        <div class="form-block">
            <label><?= __('registration') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="registration" <?= (Vars::$USER_SYS['registration'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('registration_open') ?></label><br/>
            <label class="small"><input type="radio" value="1" name="registration" <?= (Vars::$USER_SYS['registration'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('registration_moderation') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="registration" <?= (!Vars::$USER_SYS['registration'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('registration_closed') ?></label><br/><br/>

            <label class="small"><input name="reg_welcome" type="checkbox" value="1" <?= (Vars::$USER_SYS['reg_welcome'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('welcome_message') ?></label><br/>
            <label class="small"><input name="reg_email" type="checkbox" value="1" <?= (Vars::$USER_SYS['reg_email'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('registration_email') ?></label><br/>
            <label class="small"><input name="reg_quarantine" type="checkbox" value="1" <?= (Vars::$USER_SYS['reg_quarantine'] ? 'checked="checked"' : '') ?>/>&#160;<a href="#"><?= __('registration_quarantine') ?></a></label>
        </div>
        <div class="form-block">
            <?php if (Vars::$USER_RIGHTS == 9) : ?>
            <label><?= __('permissions') ?></label><br/>
            <label class="small"><input name="autologin" type="checkbox" value="1" <?= (Vars::$USER_SYS['autologin'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('autologin') ?></label><br/>
            <label class="small"><input name="change_sex" type="checkbox" value="1" <?= (Vars::$USER_SYS['change_sex'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('change_sex') ?></label><br/>
            <label class="small"><input name="change_status" type="checkbox" value="1" <?= (Vars::$USER_SYS['change_status'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('change_status') ?></label><br/>
            <label class="small"><input name="upload_avatars" type="checkbox" value="1" <?= (Vars::$USER_SYS['upload_avatars'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('upload_avatars') ?></label><br/>
            <label class="small"><input name="change_nickname" type="checkbox" value="1" <?= (Vars::$USER_SYS['change_nickname'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('change_nickname_allow') ?></label><br/>
            <label class="small"><input class="mini" type="text" name="change_period" size="2" value="<?= Vars::$USER_SYS['change_period'] ?>" maxlength="2"/>&#160;&#160;<?= __('how_many_days') ?></label><br/>
            <label class="small"><input name="digits_only" type="checkbox" value="1" <?= (Vars::$USER_SYS['digits_only'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('digits_only') ?></label>
            <br/><br/>
            <label><?= __('antiflood') ?></label><br/>
            <label class="small"><input type="radio" name="flood_mode" value="3" <?= (Vars::$USER_SYS['flood_mode'] == 3 ? 'checked="checked"' : '') ?>/>
                <input class="small" type="text" name="flood_day" size="3" value="<?= Vars::$USER_SYS['flood_day'] ?>" maxlength="3"/>&#160;<?= __('sec') . ', ' . __('day') ?></label><br/>
            <label class="small"><input type="radio" name="flood_mode" value="4" <?= (Vars::$USER_SYS['flood_mode'] == 4 ? 'checked="checked"' : '') ?>/>
                <input class="small" type="text" name="flood_night" size="3" value="<?= Vars::$USER_SYS['flood_night'] ?>" maxlength="3"/>&#160;<?= __('sec') . ', ' . __('night') ?></label><br/>
            <label class="small"><input type="radio" name="flood_mode" value="2" <?= (Vars::$USER_SYS['flood_mode'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('autoswitch') ?></label><br/>
            <label class="small"><input type="radio" name="flood_mode" value="1" <?= (Vars::$USER_SYS['flood_mode'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('adaptive') ?></label>
            <br/><br/>
            <label><?= __('for_guests') ?></label><br/>
            <label class="small"><input name="view_online" type="checkbox" value="1" <?= (Vars::$USER_SYS['view_online'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('view_online') ?></label><br/>
            <label class="small"><input name="viev_history" type="checkbox" value="1" <?= (Vars::$USER_SYS['viev_history'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('viev_history') ?></label><br/>
            <label class="small"><input name="view_userlist" type="checkbox" value="1" <?= (Vars::$USER_SYS['view_userlist'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('view_userlist') ?></label><br/>
            <label class="small"><input name="view_profiles" type="checkbox" value="1" <?= (Vars::$USER_SYS['view_profiles'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('view_profiles') ?></label>
            <br/><br/>
            <?php endif ?>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value=" <?= __('save') ?> "/>
            <?php if (Vars::$USER_RIGHTS == 9): ?>
            <a class="btn" href="<?= Vars::$URI ?>?act=users_settings&amp;reset"><?= __('reset_settings') ?></a>
            <a class="btn" href="<?= Vars::$URI ?>"><?= __('back') ?></a>
            <?php endif ?>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>