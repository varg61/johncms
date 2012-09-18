<ul class="nav">
    <li><h1><?= lng('news_on_frontpage') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->default)): ?>
    <div class="form-block"><?= lng('settings_default') ?></div>
    <?php endif ?>
    <?php if (isset($this->saved)): ?>
    <div class="form-block"><?= lng('settings_saved') ?></div>
    <?php endif ?>
    <form name="form" action="<?= Vars::$URI ?>" method="post">
        <div class="form-block">
            <label><?= lng('apperance') ?></label><br/>
            <label class="small"><input id="view" type="radio" value="1" name="view" <?= ($this->settings['view'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('heading_and_text') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="view" <?= ($this->settings['view'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('heading') ?></label><br/>
            <label class="small"><input type="radio" value="3" name="view" <?= ($this->settings['view'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('text') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="view" <?= (!$this->settings['view'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('dont_display') ?></label><br/><br/>

            <label class="small"><input name="breaks" type="checkbox" value="1" <?= ($this->settings['breaks'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('line_foldings') ?></label><br/>
            <label class="small"><input name="smileys" type="checkbox" value="1" <?= ($this->settings['smileys'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('smileys') ?></label><br/>
            <label class="small"><input name="tags" type="checkbox" value="1" <?= ($this->settings['tags'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('bbcode') ?></label><br/>
            <label class="small"><input name="kom" type="checkbox" value="1" <?= ($this->settings['kom'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('comments') ?></label><br/>

            <label class="small"><input class="small" type="text" size="3" maxlength="3" name="size" value="<?= $this->settings['size'] ?>"/>&#160;<?= lng('text_size') ?></label>
            <span class="input-help">(100 - 1000)</span><br/>
            <label class="small"><input class="mini" type="text" size="3" maxlength="2" name="quantity" value="<?= $this->settings['quantity'] ?>"/>&#160;<?= lng('news_count') ?></label>
            <span class="input-help">(1 - 15)</span><br/>
            <label class="small"><input class="mini" type="text" size="3" maxlength="2" name="days" value="<?= $this->settings['days'] ?>"/>&#160;<?= lng('news_howmanydays_display') ?></label>
            <span class="input-help">(0-15) 0 - <?= lng('without_limit') ?></span>

            <br/><br/><input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('save') ?>"/>
            <a class="btn btn-large" href="<?= Vars::$URI ?>?reset"><?= lng('reset_settings') ?></a>
            <a class="btn btn-large" href="<?= Vars::$MODULE_URI ?>"><?= lng('back') ?></a>
        </div>
    </form>
</div>