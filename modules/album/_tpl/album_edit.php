<?php if (isset($this->error)): ?>
<?= $this->error ?>
<?php endif ?>
<div class="menu">
    <form action="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] . '&amp;al=' . $this->al ?>" method="post">
        <div class="formblock">
            <label for="name"><?= lng('title') ?></label><br/>
            <input type="text" id="name" name="name" value="<?= Validate::filterString($this->name) ?>" maxlength="30"/><br/>
            <div class="desc">Min. 2, Max. 30</div>
            <label><?= lng('description') ?></label><br/>
            <textarea name="description" rows="<?= Vars::$USER_SET['field_h'] ?>"><?= Validate::filterString($this->description) ?></textarea><br/>
            <div class="desc"><?= lng('not_mandatory_field') ?><br/>Max. 500</div>
        </div>
        <div class="formblock">
            <label><?= lng('access') ?></label><br/>
            <input type="radio" name="access" value="4" <?= (!$this->access || $this->access == 4 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_all') ?><br/>
            <input type="radio" name="access" value="3" <?= ($this->access == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_friends') ?><br/>
            <input type="radio" name="access" value="2" <?= ($this->access == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_by_password') ?><br/>
            <input type="radio" name="access" value="1" <?= ($this->access == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('access_closed') ?>
        </div>
        <div class="formblock">
            <label><?= lng('password') ?></label><br/>
            <input type="text" name="password" value="<?= Validate::filterString($this->password) ?>" maxlength="15"/><br/>
            <div class="desc"><?= lng('access_help') ?><br/>Min. 3, Max. 15</div>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=list&amp;user=<?= $this->user['id'] ?>"><?= lng('cancel') ?></a>
</div>