<div class="phdr">
    <b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b>
</div>
<?php if (isset($this->menu)) : ?>
<div class="topmenu">
    <?= $this->menu ?>
</div>
<?php endif; ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<div class="list2">
    <div class="formblock">
        <div>
            <?= Functions::getImage('contacts.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=info&amp;user=<?= $this->user['id'] ?>"><?= lng('information') ?></a>
        </div>
        <div>
            <?= Functions::getImage('user_edit.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=activity&amp;user=<?= $this->user['id'] ?>"><?= lng('activity') ?></a>
        </div>
        <div>
            <?= Functions::getImage('rating.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=stat&amp;user=<?= $this->user['id'] ?>"><?= lng('statistics') ?></a>
        </div>
        <?php if ($this->bancount) : ?>
        <div>
            <?= Functions::getImage('user_block.png') ?>&#160;<a href="profile.php?act=ban&amp;user=<?= $this->user['id'] ?>"><?= lng('infringements') ?></a> (<?= $this->bancount ?>)
        </div>
        <?php endif; ?>
    </div>
    <div class="formblock">
        <div>
            <?= Functions::getImage('album_4.png') ?>&#160;<a href="album.php?act=list&amp;user=<?= $this->user['id'] ?>"><?= lng('photo_album') ?></a>&#160;(<?= $this->total_photo ?>)
        </div>
        <div>
            <?= Functions::getImage('comments.png') ?>&#160;<a href="profile.php?act=guestbook&amp;user=<?= $this->user['id'] ?>"><?= lng('guestbook') ?></a>&#160;(<?= $this->user['comm_count'] ?>)
        </div>
        <!-- Block contacts -->
    </div>
    <?php if (Vars::$USER_ID && Vars::$USER_ID != $this->user['id']): ?>
    <div class="formblock">
        <div>
            <?= Functions::getImage('mail-blocked.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail?act=select&amp;mod=banned&amp;id=<?php echo $this->user['id'] ?>"><?php echo $this->textbanned ?></a>
        </div>
        <?php if (empty($this->banned)): ?>
        <div>
            <?= Functions::getImage('mail-outbox.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail?act=select&amp;mod=contact&amp;id=<?php echo $this->user['id'] ?>"><?php echo $this->textcontact ?></a>
        </div>
        <?php endif; ?>
        <?php if (empty($this->banned)): ?>
        <div>
            <?= Functions::getImage('mail_write.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail?act=messages&amp;id=<?php echo $this->user['id'] ?>"><?php echo lng('contact_write') ?></a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <!--// Block contacts -->
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/users"><?= lng('users') ?></a>
</div>