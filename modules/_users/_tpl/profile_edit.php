<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= lng('profile_edit') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <div class="form-block">
        <form name="form" action="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>" method="post">
            <label for="imname"><?= lng('name') ?></label><br/>
            <input id="imname" type="text" value="<?= htmlspecialchars($this->user['imname']) ?>" name="imname"/><br/>
            <span class="input-help"><?= lng('description_name') ?></span><br/>

            <?php if (Vars::$USER_SYS['change_sex'] || Vars::$USER_RIGHTS >= 7) : ?>
            <label><?= lng('sex') ?></label><br/>
            <label class="small"><input id="sex" type="radio" value="m" name="sex" <?= ($this->user['sex'] == 'm' ? 'checked="checked"' : '') ?>/>&#160;<?= lng('sex_m') ?></label><br/>
            <label class="small"><input type="radio" value="w" name="sex" <?= ($this->user['sex'] == 'w' ? 'checked="checked"' : '') ?>/>&#160;<?= lng('sex_w') ?></label><br/>
            <?php endif; ?>

            <label for="birth"><?= lng('birthday') ?></label><br/>
            <?php if (isset($this->birth_error)) : ?>
            <small class="red"><?= $this->birth_error ?></small><br/>
            <?php endif ?>
            <input class="mini" id="birth" type="text" value="<?= htmlspecialchars($this->day) ?>" size="2" maxlength="2" name="day" <?= (isset($this->birth_error) ? 'class="error"' : '') ?>/>
            <input class="mini" type="text" value="<?= htmlspecialchars($this->month) ?>" size="2" maxlength="2" name="month" <?= (isset($this->birth_error) ? 'class="error"' : '') ?>/>
            <input class="small" type="text" value="<?= htmlspecialchars($this->year) ?>" size="4" maxlength="4" name="year" <?= (isset($this->birth_error) ? 'class="error"' : '') ?>/><br/>
            <span class="input-help"><?= lng('description_birth') ?></span><br/>

            <label for="live"><?= lng('live') ?></label><br/>
            <input id="live" type="text" value="<?= htmlspecialchars($this->user['live']) ?>" name="live"/><br/>
            <span class="input-help"><?= lng('description_live') ?></span><br/>

            <label for="about"><?= lng('about') ?></label><br/>
            <?= !Vars::$IS_MOBILE ? TextParser::autoBB('form', 'about') : '' ?>
            <textarea id="about" rows="<?= Vars::$USER_SET['field_h'] ?>" cols="20" name="about"><?= htmlspecialchars($this->user['about']) ?></textarea><br/>
            <span class="input-help"><?= lng('description_about') ?></span><br/>

            <label for="tel"><?= lng('phone_number') ?></label><br/>
            <input id="tel" type="text" value="<?= htmlspecialchars($this->user['tel']) ?>" name="tel"/><br/>
            <span class="input-help"><?= lng('description_phone_number') ?></span><br/>

            <label for="email">E-mail</label><br/>
            <input id="email" type="text" value="<?= htmlspecialchars($this->user['email']) ?>" name="email" <?= (isset($this->email_error) ? 'class="error"' : '') ?>/>
            <?php if (isset($this->email_error)) : ?>
            <span class="input-help error"><?= implode(' ', $this->email_error) ?></span>
            <?php endif ?>
            <br/>
            <label class="small"><input name="mailvis" type="checkbox" value="1"<?= ($this->user['mailvis'] ? ' checked="checked"' : '') ?>/>&#160;<?= lng('show_in_profile') ?></label><br/>
            <span class="input-help"><?= lng('description_email') ?></span><br/>

            <label for="siteurl"><?= lng('site') ?></label><br/>
            <input id="siteurl" type="text" value="<?= htmlspecialchars($this->user['siteurl']) ?>" name="siteurl"/><br/>
            <span class="input-help"><?= lng('description_siteurl') ?></span><br/>

            <label for="skype">Skype</label><br/>
            <input id="skype" type="text" value="<?= htmlspecialchars($this->user['skype']) ?>" name="skype"/><br/>
            <span class="input-help"><?= lng('description_skype') ?></span><br/>

            <label for="icq">ICQ</label><br/>
            <input id="icq" type="text" value="<?= $this->user['icq'] ?>" name="icq" size="10" maxlength="10"/><br/>
            <span class="input-help"><?= lng('description_icq') ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" value="<?= lng('save') ?>" name="submit"/>
            <a class="btn btn-large" href="<?= Vars::$MODULE_URI ?>/personal"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </form>
    </div>
</div>