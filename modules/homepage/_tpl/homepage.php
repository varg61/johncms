<div class="phdr">
    <b><?= lng('information') ?></b>
</div>
<?= $this->mp->news ?>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/news"><?= lng('news_archive') ?></a> (<?= $this->mp->newscount ?>)
</div>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/help"><?= lng('information') ?>, FAQ</a>
</div>
<div class="phdr">
    <b><?= lng('dialogue') ?></b>
</div>
<?php if (Vars::$SYSTEM_SET['mod_guest'] || Vars::$USER_RIGHTS >= 7): ?>
<?php endif ?>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/guestbook"><?= lng('guestbook') ?></a> (<?= $this->count->guestbook . (Vars::$USER_RIGHTS ? '&#160;/&#160;<span class="red"><a href="' . Vars::$HOME_URL . '/guestbook?mod=adm">' . $this->count->adminclub . '</a></span>' : '') ?>)
</div>
<?php if (Vars::$SYSTEM_SET['mod_forum'] || Vars::$USER_RIGHTS >= 7): ?>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/forum"><?= lng('forum') ?></a> (<?= $this->count->forum_topics . '&#160;/&#160;' . $this->count->forum_messages . (($new_messages = Counters::forumMessagesNew()) ? '&#160;/&#160;<span class="red"><a href="' . Vars::$HOME_URL . '/forum/new">+' . $new_messages . '</a></span>' : '') ?>)
</div>
<?php endif ?>
<div class="phdr">
    <b><?= lng('useful') ?></b>
</div>
<?php if (Vars::$SYSTEM_SET['mod_down'] || Vars::$USER_RIGHTS >= 7): ?>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/download"><?= lng('downloads') ?></a> (<?= $this->count->downloads . ($this->count->downloads_new ? '&#160;/&#160;<span class="red"><a href="' . Vars::$HOME_URL . '/download?act=new_files">+' . $this->count->downloads_new . '</a></span>' : '') . ($this->count->downloads_mod && (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) ? '&#160;/&#160;<span class="red"><a href="' . Vars::$HOME_URL . '/download?act=mod_files">mod:' . $this->count->downloads_mod . '</a></span>' : '') ?>)
</div>
<?php endif ?>
<?php if (Vars::$SYSTEM_SET['mod_lib'] || Vars::$USER_RIGHTS >= 7): ?>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/library"><?= lng('library') ?></a> (<?= $this->count->library . ($this->count->library_new ? '&#160;/&#160;<span class="red">+' . $this->count->library_new . '</span>' : '') . ($this->count->library_mod ? '&#160;/&#160;<span class="red"><a href="' . Vars::$HOME_URL . '/library/index.php?act=moder">mod:' . $this->count->library_mod . '</a></span>' : '') ?>)
</div>
<?php endif ?>
<?php if (Vars::$USER_ID || Vars::$USER_SYS['view_userlist']): ?>
<div class="phdr">
    <b><?= lng('community') ?></b>
</div>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/users"><?= lng('users') ?></a> (<?= $this->count->users . ($this->count->users_new ? '&#160;/&#160;<span class="red">+' . $this->count->users_new . '</span>' : '') ?>)
</div>
<div class="menu">
    <a href="<?= Vars::$HOME_URL ?>/album"><?= lng('photo_albums') ?></a>(<?= $this->count->album . '&#160;/&#160;' . $this->count->album_photo . ($this->count->album_photo_new ? '&#160;/&#160;<span class="red">+' . $this->count->album_photo_new . '</span>' : '') ?>)
</div>
<?php endif ?>
<div class="phdr">
    <a href="http://gazenwagen.com">Gazenwagen</a>
</div>
