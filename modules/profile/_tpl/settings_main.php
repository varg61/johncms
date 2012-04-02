<div class="phdr">
    <b><?= lng('settings') ?></b> | <?= lng('common_settings') ?>
</div>
<div class="topmenu"><?= Functions::displayMenu($this->menu) ?></div>
<form action="<?= Vars::$URI ?>?act=settings" method="post">
    <div class="menu">
        <div class="formblock">
            <label for="timeshift"><?= lng('settings_clock') ?></label><br/>
            <input id="timeshift" type="text" name="timeshift" size="2" maxlength="3" value="<?= Vars::$USER_SET['timeshift'] ?>"/> <?= lng('settings_clock_shift') ?> (+-12)<br/>
            <span style="font-weight:bold; background-color:#CCC"><?= date("H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600) ?></span> <?= lng('system_time') ?>
        </div>
        <div class="formblock">
            <label><?= lng('system_functions') ?></label><br/>
            <input name="direct_url" type="checkbox" value="1" <?= (Vars::$USER_SET['direct_url'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('direct_url') ?><br/>
            <input name="avatar" type="checkbox" value="1" <?= (Vars::$USER_SET['avatar'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('avatars') ?><br/>
            <input name="smileys" type="checkbox" value="1" <?= (Vars::$USER_SET['smileys'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('smileys') ?><br/>
            <input name="digest" type="checkbox" value="1" <?= (Vars::$USER_SET['digest'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('digest') ?>
        </div>
        <div class="formblock">
            <label><?= lng('text_input') ?></label><br/>
            <input type="text" name="field_h" size="2" maxlength="1" value="<?= Vars::$USER_SET['field_h'] ?>"/> <?= lng('field_height') ?> (1-9)<br/>
            <?php if (Vars::$LNG_ISO == 'ru' || Vars::$LNG_ISO == 'uk') : ?>
            <input name="translit" type="checkbox" value="1" <?= (Vars::$USER_SET['translit'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('translit') ?>
            <?php endif; ?>
        </div>
        <div class="formblock">
            <label><?= lng('apperance') ?></label><br/>
            <input type="text" name="page_size" size="2" maxlength="2" value="<?= Vars::$USER_SET['page_size'] ?>"/> <?= lng('lines_on_page') ?> (5-99)<br/>
            <input name="quick_go" type="checkbox" value="1" <?= (Vars::$USER_SET['quick_go'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('quick_jump') ?>
        </div>
        <div class="formblock">
            <label><?= lng('design_template') ?></label><br/>
            <select name="skin">
                <?php foreach ($this->tpl_list as $theme) : ?>
                <option <?= (Vars::$USER_SET['skin'] == $theme ? 'selected="selected"' : '') ?>><?= $theme ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (count(Vars::$LNG_LIST) > 1) : ?>
        <div class="formblock">
            <label><?= lng('language_select') ?></label><br/>
            <?php $user_lng = isset(Vars::$USER_SET['lng']) ? Vars::$USER_SET['lng'] : Vars::$LNG_ISO ?>
            <?php foreach (Vars::$LNG_LIST as $key => $val) : ?>
            <div>
                <input type="radio" value="<?= $key ?>" name="iso" <?= ($key == $user_lng ? 'checked="checked"' : '') ?>/>
                <?php if (file_exists(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'flags' . DIRECTORY_SEPARATOR . $key . '.gif')) : ?>
                <img src="<?= Vars::$HOME_URL ?>/images/flags/<?= $key ?>.gif" alt=""/>&#160;
                <?php endif; ?>
                <?= $val ?>
                <?php if ($key == Vars::$SYSTEM_SET['lng']) : ?>
                <small class="red">[<?= lng('default') ?>]</small>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="profile.php?act=settings&amp;reset"><?= lng('reset_settings') ?></a>
</div>