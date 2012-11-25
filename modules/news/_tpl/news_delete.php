<ul class="nav">
    <li><h1><?= __('news') ?> :: <?= __('delete') ?></h1></li>
</ul>
<div class="form-container">
    <form action="<?= Vars::$URI ?>?act=del&amp;id=<?= $this->id ?>" method="post">
        <div class="form-block align-center">
            <div class="info-message"><?= __('delete_confirmation') ?></div>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= __('delete') ?>"/>
            <a class="btn" href="<?= Vars::$URI ?>"><?= __('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>