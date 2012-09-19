<ul class="nav">
    <li><h1 class="section-personal"><?= lng('system_settings') ?></h1></li>
</ul>

<div class="form-container">
    <?php if (isset($this->save)) : ?>
    <div class="form-block"><?= lng('settings_saved') ?></div>
    <?php endif ?>
    <?php if (isset($this->reset)) : ?>
    <div class="form-block"><?= lng('settings_default') ?></div>
    <?php endif ?>

    <form action="<?= Vars::$URI ?>?act=settings" method="post">
        <div class="form-block">
            <label for="timeshift"><?= lng('settings_clock') ?></label><br/>
            <input class="small" id="timeshift" type="text" name="timeshift" size="2" maxlength="3" value="<?= Vars::$USER_SET['timeshift'] ?>"/>
            <span class="badge badge-large"><?= date("H:i", time() + (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600) ?></span>
            <span class="input-help"><?= lng('settings_clock_shift') ?> (+ - 12)</span><br/>

            <label for="page_size"><?= lng('list_size') ?></label><br/>
            <input class="mini" id="page_size" type="text" name="page_size" size="2" maxlength="2" value="<?= Vars::$USER_SET['page_size'] ?>"/>
            <span class="input-help"><?= lng('list_size_help') ?> (5-99)</span><br/>

            <label for="field_h"><?= lng('field_height') ?></label><br/>
            <input class="mini" id="field_h" type="text" name="field_h" size="2" maxlength="1" value="<?= Vars::$USER_SET['field_h'] ?>"/>
            <span class="input-help"><?= lng('field_height_help') ?> (2-9)</span><br/>

            <label class="small" style="margin-top: 18px"><input name="direct_url" type="checkbox" value="1" <?= (Vars::$USER_SET['direct_url'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('direct_url') ?></label><br/>
            <?php if (Vars::$LNG_ISO == 'ru' || Vars::$LNG_ISO == 'uk') : ?>
            <label class="small"><input name="translit" type="checkbox" value="1" <?= (Vars::$USER_SET['translit'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('translit') ?></label><br/>
            <?php endif; ?>
            <label class="small"><input name="avatar" type="checkbox" value="1" <?= (Vars::$USER_SET['avatar'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('avatars') ?></label><br/>
            <label class="small"><input name="smileys" type="checkbox" value="1" <?= (Vars::$USER_SET['smileys'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('smileys') ?></label><br/>

            <label for="skin"><?= lng('design_template') ?></label><br/>
            <select id="skin" name="skin">
                <?php foreach ($this->tpl_list as $theme) : ?>
                <option <?= (Vars::$USER_SET['skin'] == $theme ? 'selected="selected"' : '') ?>><?= $theme ?></option>
                <?php endforeach; ?>
            </select><br/>

            <?php if (count(Vars::$LNG_LIST) > 1): ?>
            <label><?= lng('language_select') ?></label><br/>
            <?php $user_lng = isset(Vars::$USER_SET['lng']) ? Vars::$USER_SET['lng'] : Vars::$LNG_ISO ?>
            <?php foreach (Vars::$LNG_LIST as $key => $val) : ?>
                <div>
                    <label class="small"><input type="radio" value="<?= $key ?>" name="iso" <?= ($key == $user_lng ? 'checked="checked"' : '') ?>/>
                        <?php if (file_exists(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'flags' . DIRECTORY_SEPARATOR . $key . '.gif')) : ?>
                            <img src="<?= Vars::$HOME_URL ?>/images/flags/<?= $key ?>.gif" alt=""/>&#160;
                            <?php endif ?>
                        <?= $val ?>
                        <?php if ($key == Vars::$SYSTEM_SET['lng']) : ?>
                            <span class="attn">*</span>
                            <?php endif ?>
                    </label>
                </div>
                <?php endforeach; ?>
            <?php endif ?>

            <br/>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('save') ?>"/>
            <a class="btn btn-large" href="<?= Vars::$URI ?>?act=settings&amp;mod=reset&amp;user=<?= $this->user['id'] ?>"><?= lng('reset_settings') ?></a>
            <a class="btn btn-large" href="<?= Vars::$MODULE_URI ?>/settings&amp;user=<?= $this->user['id'] ?>"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>