<div class="phdr">
    <a href="<?= $this->url ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? __('profile') : __('my_profile')) ?></b></a> | <?= __('upload_photo') ?>
</div>
<form enctype="multipart/form-data" method="post" action="<?= $this->url ?>?act=edit&amp;mod=upload_photo&amp;user=<?= $this->user['id'] ?>">
    <div class="menu">
        <div class="formblock">
            <label for="file"><?= __('select_image') ?></label><br/>
            <input id="file" type="file" name="imagefile" value=""/>
            <div class="desc" style="padding-top: 8px">
                <?= __('select_photo_help')  ?>
            </div>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= __('upload') ?>"/>
        </div>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?= (1024 * Vars::$SYSTEM_SET['filesize']) ?>"/>
        <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
    </div>
</form>
<div class="phdr"><a href="<?= $this->url ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= __('back') ?></a></div>