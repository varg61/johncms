<div class="phdr"><strong><?= $this->phdr ?></strong></div>
<div class="gmenu">
    <form name="form" action="<?= $this->urlSelect ?>" method="post">
        <div>
            <strong><?= $this->select ?></strong><br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="submit" value="<?= $this->submit ?>"/>
        </div>
    </form>
</div>
<p>
    <a href="<?= Router::getUrl(2) ?>"><?= __('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>contacts/"><?= __('contacts') ?></a>
</p>