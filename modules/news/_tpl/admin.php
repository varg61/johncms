<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/admin"><strong><?= lng('admin_panel') ?></strong></a> | <?= lng('news_on_frontpage') ?>
</div>
<?php if (isset($this->default)): ?>
<div class="gmenu">
    <p><?= lng('settings_default') ?></p>
</div>
<?php endif ?>
<?php if(isset($this->saved)): ?>
<div class="gmenu">
    <p><?= lng('settings_saved') ?></p>
</div>
<?php endif ?>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label for="view"><?= lng('apperance') ?></label><br/>
            <input id="view" type="radio" value="1" name="view" <?= ($this->settings['view'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('heading_and_text') ?><br/>
            <input type="radio" value="2" name="view" <?= ($this->settings['view'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('heading') ?><br/>
            <input type="radio" value="3" name="view" <?= ($this->settings['view'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('text') ?><br/>
            <input type="radio" value="0" name="view" <?= (!$this->settings['view'] ? 'checked="checked"' : '') ?>/>&#160;<strong><?= lng('dont_display') ?></strong>
        </div>
        <div class="formblock">
            <input name="breaks" type="checkbox" value="1" <?= ($this->settings['breaks'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('line_foldings') ?><br/>
            <input name="smileys" type="checkbox" value="1" <?= ($this->settings['smileys'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('smileys') ?><br/>
            <input name="tags" type="checkbox" value="1" <?= ($this->settings['tags'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('bbcode') ?><br/>
            <input name="kom" type="checkbox" value="1" <?= ($this->settings['kom'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('comments') ?>
        </div>
        <div class="formblock">
            <label for="size"><?= lng('text_size') ?></label><br/>
            <input id="size" type="text" size="3" maxlength="3" name="size" value="<?= $this->settings['size'] ?>"/>&#160;(50 - 500)
        </div>
        <div class="formblock">
            <label for="quantity"><?= lng('news_count') ?></label><br/>
            <input id="quantity" type="text" size="3" maxlength="2" name="quantity" value="<?= $this->settings['quantity'] ?>"/>&#160;(1 - 15)
        </div>
        <div class="formblock">
            <label for="days"><?= lng('news_howmanydays_display') ?></label><br/>
            <input id="days" type="text" size="3" maxlength="2" name="days" value="<?= $this->settings['days'] ?>"/>&#160;(0 - 15)<br/>
            <div class="desc">0 - <?= lng('without_limit') ?></div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?reset"><?= lng('reset_settings') ?></a>
</div>
<p>
    <a href="<?= Vars::$HOME_URL ?>/admin"><?= lng('admin_panel') ?></a><br/>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('to_news') ?></a>
</p>