<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= lng('change_avatar') ?></h1></li>
</ul>
<div class="user-block"><?= Functions::displayUser($this->user, array('iphide' => 1)) ?></div>
<ul class="nav">
    <li><h2><?=  lng('avatar') ?></h2></li>
    <?php if ($this->setUsers['upload_avatars'] || Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= Vars::$URI ?>?act=edit&amp;mod=upload_avatar&amp;user=<?= $this->user['id'] ?>"><i class="icn-upload"></i><?= lng('upload_image') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <?php if ($this->setUsers['upload_animation'] || Vars::$USER_RIGHTS >= 7) : ?>
    <li><a href="<?= Vars::$URI ?>?act=edit&amp;mod=upload_animation&amp;user=<?= $this->user['id'] ?>"><i class="icn-upload"></i><?= lng('upload_animation') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <?php if ($this->user['id'] == Vars::$USER_ID) : ?>
    <li><a href="<?= Vars::$HOME_URL ?>/avatars"><i class="icn-image"></i><?= lng('select_in_catalog') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <?php if (isset($this->avatar)) : ?>
    <li><a href="<?= Vars::$URI ?>?act=edit&amp;mod=delete_avatar&amp;user=<?= $this->user['id'] ?>"><i class="icn-trash"></i><?= lng('delete') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
</ul>