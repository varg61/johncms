<div class="phdr"><strong><?php echo lng('settings') ?></strong></div>
<?php echo $this->save ?>
<div>
<form name="form" action="<?php echo Vars::$MODULE_URI ?>?act=settings" method="post">
<div class="gmenu">
<strong><?php echo lng('can_write') ?>:</strong><br/>
<input type="radio" name="access" value="0" <?php echo ($this->access == 0 ? 'checked="checked"' : '') ?>/> <?php echo lng('all') ?><br />
<input type="radio" name="access" value="1" <?php echo ($this->access == 1 ? 'checked="checked"' : '') ?>/> <?php echo lng('contact_friends') ?><br />
<input type="radio" name="access" value="2" <?php echo ($this->access == 2 ? 'checked="checked"' : '') ?>/> <?php echo lng('only_friends') ?><br />
</div>
<div class="rmenu">
<input type="submit" name="submit" value="<?php echo lng('save') ?>" />
</div>
</form>
</div>
<p>
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng('mail') ?></a><br />
<a href="<?php echo Vars::$HOME_URL ?>/contacts"><?php echo lng('contacts') ?></a>
</p>