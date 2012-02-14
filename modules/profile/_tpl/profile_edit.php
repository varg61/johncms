<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> |
    <?= Vars::$LNG['edit'] ?>
</div>
<form action="profile.php?act=edit&amp;user=' . $user['user_id'] . '" method="post">
    <div class="gmenu">
        <?php if (Vars::$USER_RIGHTS >= 7) : ?>
        <h3><?= Vars::$LNG['nick'] ?></h3>
        <input type="text" value="<?= $this->user['nickname'] ?>" name="name"/><br/>
        <small class="gray"><?= $this->lng['nick_lenght'] ?></small>
        <h3><?= Vars::$LNG['status'] ?></h3>
        <input type="text" value="<?= $this->user['status'] ?>" name="status"/><br/>
        <small class="gray"><?= $this->lng['status_lenght'] ?></small>
        <?php else : ?>
        <h3><?= Vars::$LNG['nick'] ?></h3>
        <input type="text" value="<?= $this->user['nickname'] ?>" name="name" disabled="disabled"/><br/>
        <small class="gray"><?= $this->lng['nick_lenght'] ?></small>
        <h3><?= Vars::$LNG['status'] ?></h3>
        <input type="text" value="<?= $this->user['status'] ?>" name="status" disabled="disabled"/><br/>
        <small class="gray"><?= $this->lng['status_lenght'] ?></small>
        <?php endif ?>
        <h3><?=  Vars::$LNG['avatar'] ?></h3>
        <?php if (isset($this->avatar)) : ?>
        <img src="<?= Vars::$HOME_URL ?>/files/users/avatar/<?= $this->user['id'] ?>.gif" width="32" height="32" alt="<?= $this->user['nickname'] ?>" border="0"/><br/>
        <?php endif ?>
        <div style="font-size: x-small; margin-bottom: 6px">
            <a href="<?= Vars::$HOME_URL ?>/avatars"><?= Vars::$LNG['select'] ?></a> |
            <a href=""><?= $this->lng['upload'] ?></a>
            <?php if (isset($this->avatar)) : ?>
            | <a href="<?= Vars::$URI ?>?act=delete_avatar&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['delete'] ?></a>
            <?php endif ?>
        </div>
    </div>
    <div class="menu">
        <h3><?= $this->lng['photo'] ?></h3>
        <?php if (isset($this->photo)) : ?>
        <a href="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>.jpg">
            <img src="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="" border="0"/>
        </a>
        <?php endif ?>
        <div style="font-size: x-small; margin-bottom: 6px">
            <a href=""><?= $this->lng['upload'] ?></a>
            <?php if (isset($this->photo)) : ?>
            | <a href="<?= Vars::$URI ?>?act=delete_photo&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['delete'] ?></a>
            <?php endif ?>
        </div>
        <h3><?= $this->lng['name'] ?></h3>
        <input type="text" value="<?= $this->user['imname'] ?>" name="imname"/>
        <h3><?= $this->lng['birthday'] ?></h3>
        <input type="text" value="" size="2" maxlength="2" name="day"/>
        <input type="text" value="" size="2" maxlength="2" name="month"/>
        <input type="text" value="" size="4" maxlength="4" name="year"/><br/>
        <small class="gray"><?= $this->lng['birthday_desc'] ?></small>
        <h3><?= $this->lng['city'] ?></h3>
        <input type="text" value="<?= $this->user['live'] ?>" name="live"/>
        <h3><?= $this->lng['about'] ?></h3>
        <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" cols="20" name="about"><?= str_replace('<br />', "\r\n", $this->user['about']) ?></textarea>
        <h3><?= $this->lng['phone_number'] ?></h3>
        <input type="text" value="<?= $this->user['tel'] ?>" name="tel"/>
        <h3><?= $this->lng['site'] ?></h3>
        <input type="text" value="<?= $this->user['www'] ?>" name="www"/>
        <h3>E-mail</h3>
        <input type="text" value="<?= $this->user['email'] ?>" name="mail"/><br/>
        <small class="gray"><?= $this->lng['email_warning'] ?></small>
        <br/>
        <input name="mailvis" type="checkbox" value="1"<?= ($this->user['mailvis'] ? ' checked="checked"' : '') ?>/>&#160;<?= $this->lng['show_in_profile'] ?>
        <h3>ICQ</h3>
        <input type="text" value="<?= $this->user['icq'] ?>" name="icq" size="10" maxlength="10"/>
        <h3>Skype</h3>
        <input type="text" value="<?= $this->user['skype'] ?>" name="skype"/>
        <p style="margin-top: 10px"><input type="submit" value="<?= Vars::$LNG['save'] ?>" name="submit"/></p>
    </div>
</form>
<div class="phdr"><a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a></div>