<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('edit') ?>
</div>
<?php if (Vars::$USER_RIGHTS >= 7) : ?>
<div class="topmenu"><b><?= lng('user') ?></b> | <a
    href="<?= Vars::$URI ?>?act=administration&amp;user=<?= $this->user['id'] ?>"><?= lng('administration') ?></a>
</div>
<?php endif ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<form action="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label><?= lng('photo') ?></label>
            <?php if (isset($this->photo)) : ?>
            <a href="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>.jpg"><img src="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="" border="0"/></a>
            <?php endif ?>
            <div class="small">
                <a href=""><?= lng('upload') ?></a>
                <?php if (isset($this->photo)) : ?>
                | <a href="<?= Vars::$URI ?>?act=delete_photo&amp;user=<?= $this->user['id'] ?>"><?= lng('delete') ?></a>
                <?php endif ?>
            </div>
        </div>
        <div class="formblock">
            <label for="imname"><?= lng('name') ?></label><br/>
            <input id="imname" type="text" value="<?= $this->user['imname'] ?>" name="imname"/>
        </div>
        <div class="formblock">
            <label for="birth"><?= lng('birthday') ?></label><br/>
            <input id="birth" type="text" value="" size="2" maxlength="2" name="day"/>
            <input type="text" value="" size="2" maxlength="2" name="month"/>
            <input type="text" value="" size="4" maxlength="4" name="year"/>
            <div class="desc"><?= lng('birthday_desc') ?></div>
        </div>
        <div class="formblock">
            <label for="live"><?= lng('city') ?></label><br/>
            <input id="live" type="text" value="<?= $this->user['live'] ?>" name="live"/>
        </div>
        <div class="formblock">
            <label for="about"><?= lng('about') ?></label><br/>
            <textarea id="about" rows="<?= Vars::$USER_SET['field_h'] ?>" cols="20" name="about"><?= str_replace('<br />', "\r\n", $this->user['about']) ?></textarea>
        </div>
        <div class="formblock">
            <label for="tel"><?= lng('phone_number') ?></label><br/>
            <input id="tel" type="text" value="<?= $this->user['tel'] ?>" name="tel"/>
        </div>
        <div class="formblock">
            <label for="www"><?= lng('site') ?></label><br/>
            <input id="www" type="text" value="<?= $this->user['www'] ?>" name="www"/>
        </div>
        <div class="formblock">
            <label for="mail">E-mail</label><br/>
            <input id="mail" type="text" value="<?= $this->user['email'] ?>" name="mail"/>
            <input name="mailvis" type="checkbox" value="1"<?= ($this->user['mailvis'] ? ' checked="checked"' : '') ?>/>&#160;<?= lng('show_in_profile') ?>
            <div class="desc"><?= lng('email_warning') ?></div>
        </div>
        <div class="formblock">
            <label for="icq">ICQ</label><br/>
            <input id="icq" type="text" value="<?= $this->user['icq'] ?>" name="icq" size="10" maxlength="10"/>
        </div>
        <div class="formblock">
            <label for="skype">Skype</label><br/>
            <input id="skype" type="text" value="<?= $this->user['skype'] ?>" name="skype"/>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>