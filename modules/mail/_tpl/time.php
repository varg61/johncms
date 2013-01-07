<div class="phdr"><strong><?= $this->phdr ?></strong></div>
<div class="gmenu">
    <form name="form" action="<?= $this->urlSelect ?>" method="post">
        <div>
            <p>

            <h3><?= __('clear_param') ?></h3>
            <input type="radio" name="cl" value="0" checked="checked"/><?= __('clear_month') ?><br/>
            <input type="radio" name="cl" value="1"/><?= __('clear_week') ?><br/>
            <input type="radio" name="cl" value="2"/><?= __('clear_all') ?></p>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="submit" value="<?= $this->submit ?>"/>
        </div>
    </form>
</div>
<p>
    <a href="<?= Router::getUrl(2) ?>"><?= __('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= __('contacts') ?></a>
</p>