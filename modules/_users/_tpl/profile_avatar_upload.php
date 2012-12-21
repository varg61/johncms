<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= __('upload_avatar') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form enctype="multipart/form-data" method="post" action="<?= Vars::$URI ?>?act=avatar_upload&amp;user=<?= $this->user['id'] ?>">
        <div class="form-block">
            <label for="image"><?= __('select_image') ?></label><br/>
            <input id="image" type="file" name="image" value=""/>
            <span class="description" style="padding-top: 8px"><?= __('select_avatar_help')  ?></span><br/>

            <label for="animation"><?= __('animation') ?></label><br/>
            <input id="animation" type="file" name="animation" value=""/>
            <span class="description" style="padding-top: 8px"><?= __('select_animation_help')  ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= __('upload') ?>"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= __('back') ?></a>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= (1024 * Vars::$SYSTEM_SET['filesize']) ?>"/>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>