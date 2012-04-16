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
        <div>
            <?= Functions::getImage('friends.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends?id=<?php echo $this->user['id'] ?>"><?= lng('friends') ?></a>&#160;(<?php echo Functions::friendsCount($this->user['id']) ?>)
        </div>
    </div>
    
    <?php if(Vars::$USER_ID && Vars::$USER_ID != $this->user['id']):?>
    <!-- Block friends -->
    <div class="formblock">
    	<?php if(empty($this->banned) && $this->friend == 2): ?>
    	<div>
            <?= Functions::getImage('mail-friends.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends?act=ok&amp;id=<?php echo $this->user['id'] ?>"><?php echo lng('friends_demands_ok') ?></a>&#160;|&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends?act=no&amp;id=<?php echo $this->user['id'] ?>"><?php echo lng('friends_demands_no') ?></a>
        </div>
       	<?php endif ?>
        <?php if(empty($this->banned) && $this->friend == 3): ?>
    	<div>
            <?= Functions::getImage('mail-friends.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends?act=cancel&amp;id=<?php echo $this->user['id'] ?>"><?php echo lng('friends_demands_cancel') ?></a>
        </div>
       	<?php endif ?>
        <?php if(empty($this->banned) && $this->friend == 0): ?>
    	<div>
            <?= Functions::getImage('mail-friends.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends?act=add&amp;id=<?php echo $this->user['id'] ?>"><?php echo lng('friends_add') ?></a>
        </div>
       	<?php endif ?>
        <?php if($this->friend == 1): ?>
    	<div>
            <?= Functions::getImage('mail-friends.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends?act=delete&amp;id=<?php echo $this->user['id'] ?>"><?php echo lng('friends_delete') ?></a>
        </div>
       	<?php endif ?>
    </div>
    <!--// Block friends -->

    <!-- Block contacts -->
    <div class="formblock">
    	<div>
            <?= Functions::getImage('mail-blocked.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/contacts?act=select&amp;mod=banned&amp;id=<?php echo $this->user['id'] ?>"><?php echo ($this->banned == 1 ? lng('contact_delete_ignor') : lng('contact_add_ignor')) ?></a>
    	</div>
        <?php if(empty($this->banned)): ?>
    	<div>
            <?= Functions::getImage('mail-outbox.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/contacts?act=select&amp;mod=contact&amp;id=<?php echo $this->user['id'] ?>"><?php echo ($this->num_cont ? lng('contact_delete') : lng('contact_add')) ?></a>
    	</div>
        <?php endif ?>
    	<?php if(empty($this->banned)): ?>
    	<div>
            <?= Functions::getImage('mail_write.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail?act=messages&amp;id=<?php echo $this->user['id'] ?>"><?php echo lng('contact_write') ?></a>
    	</div>
        <?php endif ?>
    </div>
    <!--// Block contacts -->
    <?php endif ?>
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/users"><?= lng('users') ?></a>
</div>