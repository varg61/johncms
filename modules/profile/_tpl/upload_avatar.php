<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('upload_avatar') ?>
</div>
<form enctype="multipart/form-data" method="post" action="<?= Vars::$URI ?>?act=edit&amp;mod=upload_avatar&amp;user=<?= $this->user['id'] ?>">
    <div class="menu">
        <div class="formblock">
            <label for="file"><?= lng('select_image') ?></label><br/>
            <input id="file" type="file" name="imagefile" value=""/>
            <div class="desc" style="padding-top: 8px">
                <?= lng('select_image_help')  ?>
            </div>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('upload') ?>"/>
        </div>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?= (1024 * Vars::$SYSTEM_SET['flsz']) ?>"/>
        <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
    </div>
</form>
<div class="phdr"><a href="<?= Vars::$URI ?>?act=edit&amp;mod=avatar&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a></div>