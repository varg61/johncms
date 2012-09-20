<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= lng('upload_animation_avatar') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form enctype="multipart/form-data" method="post" action="<?= Vars::$URI ?>?act=edit&amp;mod=upload_animation&amp;user=<?= $this->user['id'] ?>">
        <div class="form-block">
            <label for="file"><?= lng('select_image') ?></label><br/>
            <input id="file" type="file" name="imagefile" value=""/>
            <span class="input-help" style="padding-top: 8px"><?= lng('select_animation_help')  ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('upload') ?>"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=edit&amp;mod=avatar&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
            <input type="hidden" name="MAX_FILE_SIZE" value="10240"/>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>