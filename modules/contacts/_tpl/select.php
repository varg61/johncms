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
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>