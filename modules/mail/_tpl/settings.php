<div class="phdr"><strong><?= lng('settings') ?></strong></div>
<?= $this->save ?>
<div>
    <form name="form" action="<?= Vars::$MODULE_URI ?>?act=settings" method="post">
        <div class="gmenu">
            <strong><?= lng('can_write') ?>:</strong><br/>
            <input type="radio" name="access" value="0" <?= ($this->access == 0 ? 'checked="checked"' : '') ?>/> <?= lng('all') ?><br/>
            <input type="radio" name="access" value="1" <?= ($this->access == 1 ? 'checked="checked"' : '') ?>/> <?= lng('contact_friends') ?><br/>
            <input type="radio" name="access" value="2" <?= ($this->access == 2 ? 'checked="checked"' : '') ?>/> <?= lng('only_friends') ?><br/>
        </div>
        <div class="rmenu">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
    </form>
</div>
<p>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>