<ul class="nav">
    <li><h1 class="section-personal"><?= lng('settings') ?></h1></li>
</ul>
<?= $this->save ?>
<div class="form-container">
    <form name="form" action="<?= Vars::$MODULE_URI ?>?act=settings" method="post">
        <div class="form-block">
            <label><?= lng('can_write') ?></label><br/>
            <input type="radio" name="access" value="0" <?= ($this->access == 0 ? 'checked="checked"' : '') ?> id="all"/> <label for="all"><?= lng('all') ?></label><br/>
            <input type="radio" name="access" value="1" <?= ($this->access == 1 ? 'checked="checked"' : '') ?> id="contact_friends"/> <label for="contact_friends"><?= lng('contact_friends') ?></label><br/>
            <input type="radio" name="access" value="2" <?= ($this->access == 2 ? 'checked="checked"' : '') ?> id="only_friends"/> <label for="only_friends"><?= lng('only_friends') ?></label><br/><br/>
			<input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('save') ?>"/>
			<a class="btn" href="<?= Vars::$URI ?>"><?= lng('cancel') ?></a>
        </div>
    </form>
</div>