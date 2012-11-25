<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b><?= __('admin_panel') ?></b></a> | <?= __('sitemap') ?>
</div>
<?php if (isset($this->saved)) : ?>
<div class="gmenu"><p><?= __('settings_saved') ?></p></div>
<?php endif ?>
<?php if (isset($this->default)) : ?>
<div class="rmenu"><p><?= __('settings_default') ?></p></div>
<?php endif ?>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label><?= __('include_in_map') ?></label><br/>
            <input name="forum" type="checkbox" value="1" <?= ($this->settings['forum'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('forum') ?><br/>
            <input name="lib" type="checkbox" value="1" <?= ($this->settings['lib'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('library') ?>
        </div>
        <div class="formblock">
            <label><?= __('browsers') ?></label><br/>
            <input type="radio" value="1" name="browsers" <?= ($this->settings['browsers'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('show_all') ?><br/>
            <input type="radio" value="0" name="browsers" <?= (!$this->settings['browsers'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('show_only_computers') ?>
        </div>
        <div class="formblock">
            <label><?= __('users') ?></label><br/>
            <input type="radio" value="1" name="users" <?= ($this->settings['users'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('show_all') ?><br/>
            <input type="radio" value="0" name="users" <?= (!$this->settings['users'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('show_only_guests') ?>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= __('save') ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?reset"><?= __('reset_settings') ?></a>
</div>
<p><a href="<?= Vars::$MODULE_URI ?>"><?= __('admin_panel') ?></a></p>