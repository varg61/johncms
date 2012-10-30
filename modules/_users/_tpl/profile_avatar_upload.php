<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= lng('upload_avatar') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form enctype="multipart/form-data" method="post" action="<?= Vars::$URI ?>?act=avatar_upload&amp;user=<?= $this->user['id'] ?>">
        <div class="form-block">
            <label for="image"><?= lng('select_image') ?></label><br/>
            <input id="image" type="file" name="image" value=""/>
            <span class="input-help" style="padding-top: 8px"><?= lng('select_avatar_help')  ?></span><br/>

            <label for="animation"><?= lng('animation') ?></label><br/>
            <input id="animation" type="file" name="animation" value=""/>
            <span class="input-help" style="padding-top: 8px"><?= lng('select_animation_help')  ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('upload') ?>"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= (1024 * Vars::$SYSTEM_SET['flsz']) ?>"/>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>