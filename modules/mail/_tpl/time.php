<div class="phdr"><strong><?= $this->phdr ?></strong></div>
<div class="gmenu">
    <form name="form" action="<?= $this->urlSelect ?>" method="post">
        <div>
            <p>

            <h3><?= lng('clear_param') ?></h3>
            <input type="radio" name="cl" value="0" checked="checked"/><?= lng('clear_month') ?><br/>
            <input type="radio" name="cl" value="1"/><?= lng('clear_week') ?><br/>
            <input type="radio" name="cl" value="2"/><?= lng('clear_all') ?></p>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="submit" value="<?= $this->submit ?>"/>
        </div>
    </form>
</div>
<p>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>