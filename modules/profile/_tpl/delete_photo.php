<div class="phdr">
    <a href="<?= Router::getUrl(2) ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? __('profile') : __('my_profile')) ?></b></a> |
    <?= __('delete_photo') ?>
</div>
<form action="<?= $this->url ?>?act=edit&amp;mod=delete_photo&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="rmenu">
        <p><img src="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="" border="0"/></p>
        <p><?= __('delete_photo_warning') ?></p>
        <p style="margin-top: 10px"><input type="submit" value="<?= __('delete') ?>" name="submit"/></p>
    </div>
</form>
<div class="phdr"><a href="<?= $this->url ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= __('back') ?></a></div>