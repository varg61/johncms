<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> | <?= $this->lng['upload_avatar'] ?>
</div>
<form enctype="multipart/form-data" method="post" action="<?= Vars::$URI ?>?act=upload_avatar&amp;user=<?= $this->user['id'] ?>">
    <div class="menu">
        <div class="formblock">
            <label for="file"><?= $this->lng['select_image'] ?></label><br/>
            <input id="file" type="file" name="imagefile" value=""/>
            <div class="desc" style="padding-top: 8px">
                <?= $this->lng['select_image_help'] . ' ' . Vars::$SYSTEM_SET['flsz'] ?> kb.<br/>
                <?= $this->lng['select_image_help_2'] ?><br/>
                <?= $this->lng['select_image_help_3'] ?><br/>
                <?= $this->lng['select_image_help_4'] ?>
            </div>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= $this->lng['upload'] ?>"/>
        </div>
        <input type="hidden" name="MAX_FILE_SIZE" value="<?= (1024 * Vars::$SYSTEM_SET['flsz']) ?>"/>
    </div>
</form>
<div class="phdr"><a href="<?= Vars::$URI ?>?act=avatar&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a></div>