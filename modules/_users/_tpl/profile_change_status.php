<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= lng('change_status') ?></h1></li>
</ul>

<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form action="<?= Vars::$URI ?>?act=edit&amp;mod=status&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block">
            <label for="status"><?= lng('status') ?></label><br/>
            <input <?= (isset($this->error) ? 'class="error"' : '') ?> id="status" type="text" value="<?= $this->status ?>" name="status"/>
            <?php if (isset($this->error)): ?>
            <span class="input-help error"><?= $this->error ?></span>
            <?php endif ?>
            <br/><span class="input-help"><?= lng('status_lenght') ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" value="<?= lng('save') ?>" name="submit"/>
            <a class="btn" href="<?= Vars::$MODULE_URI ?>/settings?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>