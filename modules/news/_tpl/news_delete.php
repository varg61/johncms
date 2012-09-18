<ul class="nav">
    <li><h1><?= lng('news') ?> :: <?= lng('delete') ?></h1></li>
</ul>
<div class="form-container">
    <form action="<?= Vars::$URI ?>?act=del&amp;id=<?= $this->id ?>" method="post">
        <div class="form-block align-center">
            <div class="info-message"><?= lng('delete_confirmation') ?></div>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('delete') ?>"/>
            <a class="btn btn-large" href="<?= Vars::$URI ?>"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>