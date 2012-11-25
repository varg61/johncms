<ul class="nav">
    <li><h1 class="section-warning"><?= __('language_settings') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (!empty($this->error)): ?>
    <div class="form-block error"><?= __('errors_occurred') ?></div>
    <?php elseif (isset($this->save)): ?>
    <div class="form-block confirm"><?= __('settings_saved') ?></div>
    <?php endif ?>

    <form action="<?= Vars::$URI ?>?act=language_settings" method="post">
        <div class="form-block">
            <label><?= __('language_default') ?></label><br/>
            <span class="input-help"><?= __('select_language_help') ?></span><br/>
            <label class="small">
                <input type="radio" value="#" name="iso" <?= (Vars::$SYSTEM_SET['lng'] == '#' ? 'checked="checked"' : '') ?>/>
                <?= __('select_automatically') ?>
            </label><br/>
            <?php foreach (Languages::getInstance()->getLngDescription() as $key => $val): ?>
            <label class="small">
                <input type="radio" value="<?= $key ?>" name="iso" <?= ($key == Vars::$SYSTEM_SET['lng'] ? 'checked="checked"' : '') ?>/>
                &#160;<?= Functions::loadImage('flag_' . $key . '.gif') ?>
                &#160;<?= Validate::checkout($val) ?>
            </label><br/>
            <?php endforeach ?>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value=" <?= __('save') ?> "/>
            <a class="btn" href="<?= Vars::$URI ?>"><?= __('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>