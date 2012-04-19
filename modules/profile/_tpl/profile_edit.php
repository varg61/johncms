<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b></a> | <?= lng('edit') ?>
</div>
<?php if (Vars::$USER_RIGHTS >= 7) : ?>
<div class="topmenu"><b><?= lng('user') ?></b> | <a
    href="<?= Vars::$URI ?>?act=edit&amp;mod=administration&amp;user=<?= $this->user['id'] ?>"><?= lng('administration') ?></a>
</div>
<?php endif; ?>

<?php if (isset($this->save)) : ?>
<div class="gmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('settings_saved') ?>
</div>
<?php endif; ?>
<?php if (isset($this->email_error) || isset($this->birth_error)) : ?>
<div class="rmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('errors_occurred') ?>
</div>
<?php endif; ?>

<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<form name="form" action="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label><?= lng('photo') ?></label>
            <?php if (isset($this->photo)) : ?>
            <br/><a href="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>.jpg"><img src="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="" border="0"/></a>
            <?php endif; ?>
            <div class="small">
                <a href="<?= Vars::$URI ?>?act=edit&amp;mod=upload_photo"><?= lng('upload') ?></a>
                <?php if (isset($this->photo)) : ?>
                | <a href="<?= Vars::$URI ?>?act=edit&amp;mod=delete_photo&amp;user=<?= $this->user['id'] ?>"><?= lng('delete') ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php if (Vars::$USER_SYS['change_sex'] || Vars::$USER_RIGHTS >= 7) : ?>
        <div class="formblock">
            <label for="sex"><?= lng('sex') ?></label><br/>
            <input id="sex" type="radio" value="m" name="sex" <?= ($this->user['sex'] == 'm' ? 'checked="checked"' : '') ?>/>&#160;<?= lng('sex_m') ?><br/>
            <input type="radio" value="w" name="sex" <?= ($this->user['sex'] == 'w' ? 'checked="checked"' : '') ?>/>&#160;<?= lng('sex_w') ?>
        </div>
        <?php endif; ?>
        <div class="formblock">
            <label for="imname"><?= lng('name') ?></label><br/>
            <input id="imname" type="text" value="<?= htmlspecialchars($this->user['imname']) ?>" name="imname"/>
            <div class="desc"><?= lng('description_name') ?></div>
        </div>
        <div class="formblock">
            <label for="birth"><?= lng('birthday') ?></label><br/>
            <?php if (isset($this->birth_error)) : ?>
            <small class="red"><?= $this->birth_error ?></small><br/>
            <?php endif; ?>
            <input id="birth" type="text" value="<?= htmlspecialchars($this->day) ?>" size="2" maxlength="2" name="day" <?= (isset($this->birth_error) ? 'class="error"' : '') ?>/>
            <input type="text" value="<?= htmlspecialchars($this->month) ?>" size="2" maxlength="2" name="month" <?= (isset($this->birth_error) ? 'class="error"' : '') ?>/>
            <input type="text" value="<?= htmlspecialchars($this->year) ?>" size="4" maxlength="4" name="year" <?= (isset($this->birth_error) ? 'class="error"' : '') ?>/>
            <div class="desc"><?= lng('description_birth') ?></div>
        </div>
        <div class="formblock">
            <label for="live"><?= lng('live') ?></label><br/>
            <input id="live" type="text" value="<?= htmlspecialchars($this->user['live']) ?>" name="live"/>
            <div class="desc"><?= lng('description_live') ?></div>
        </div>
        <div class="formblock">
            <label for="about"><?= lng('about') ?></label><br/>
            <?php if (!Vars::$IS_MOBILE) echo TextParser::autoBB('form', 'about') ?>
            <textarea id="about" rows="<?= Vars::$USER_SET['field_h'] ?>" cols="20" name="about"><?= htmlspecialchars($this->user['about']) ?></textarea>
            <div class="desc"><?= lng('description_about') ?></div>
        </div>
        <div class="formblock">
            <label for="tel"><?= lng('phone_number') ?></label><br/>
            <input id="tel" type="text" value="<?= htmlspecialchars($this->user['tel']) ?>" name="tel"/>
            <div class="desc"><?= lng('description_phone_number') ?></div>
        </div>
        <div class="formblock">
            <label for="siteurl"><?= lng('site') ?></label><br/>
            <input id="siteurl" type="text" value="<?= htmlspecialchars($this->user['siteurl']) ?>" name="siteurl"/>
            <div class="desc"><?= lng('description_siteurl') ?></div>
        </div>
        <div class="formblock">
            <label for="email">E-mail</label><br/>
            <?php if (isset($this->email_error)) : ?>
            <small class="red"><?= implode(' ', $this->email_error) ?></small><br/>
            <?php endif; ?>
            <input id="email" type="text" value="<?= htmlspecialchars($this->user['email']) ?>" name="email" <?= (isset($this->email_error) ? 'class="error"' : '') ?>/>
            <input name="mailvis" type="checkbox" value="1"<?= ($this->user['mailvis'] ? ' checked="checked"' : '') ?>/>&#160;<?= lng('show_in_profile') ?>
            <div class="desc"><?= lng('description_email') ?></div>
        </div>
        <div class="formblock">
            <label for="icq">ICQ</label><br/>
            <input id="icq" type="text" value="<?= $this->user['icq'] ?>" name="icq" size="10" maxlength="10"/>
            <div class="desc"><?= lng('description_icq') ?></div>
        </div>
        <div class="formblock">
            <label for="skype">Skype</label><br/>
            <input id="skype" type="text" value="<?= htmlspecialchars($this->user['skype']) ?>" name="skype"/>
            <div class="desc"><?= lng('description_skype') ?></div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
    <input type="hidden" name="token" value="<?= $this->token ?>"/>
</form>
<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>