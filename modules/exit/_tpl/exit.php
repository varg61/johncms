<div class="phdr">
    <b><?= lng('exit') ?></b>
</div>
<div class="rmenu">
    <form action="<?= Vars::$HOME_URL ?>/exit" method="post">
        <div class="formblock">
            <label><?= lng('exit_warning') ?></label>
        </div>
        <div class="formblock">
            <input type="checkbox" name="clear" value="1"/>&#160;<?= lng('clear_authorisation') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('exit') ?>"/>
        </div>
        <input type="hidden" name="form_token" value="<?= $this->token ?>"/>
    </form>
</div>
<div class="phdr">
    <a href="<?= $this->referer ?>"><?= lng('cancel') ?></a>
</div>