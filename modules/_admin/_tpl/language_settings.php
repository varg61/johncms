<ul class="nav">
    <li><h1 class="section-warning"><?= lng('language_settings') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (!empty($this->error)): ?>
    <div class="form-block error"><?= lng('errors_occurred') ?></div>
    <?php elseif (isset($this->save)): ?>
    <div class="form-block confirm"><?= lng('settings_saved') ?></div>
    <?php endif ?>

    <form action="<?= Vars::$URI ?>?act=language_settings" method="post">
        <div class="form-block">
            <label><?= lng('language_default') ?></label><br/>
            <span class="input-help"><?= lng('select_language_help') ?></span><br/>
            <label class="small">
                <input type="radio" value="#" name="iso" <?= (Vars::$SYSTEM_SET['lng'] == '#' ? 'checked="checked"' : '') ?>/>
                <?= lng('select_automatically') ?>
            </label><br/>
            <?php foreach (Languages::getInstance()->getLngDescription() as $key => $val): ?>
            <label class="small">
                <input type="radio" value="<?= $key ?>" name="iso" <?= ($key == Vars::$SYSTEM_SET['lng'] ? 'checked="checked"' : '') ?>/>
                &#160;<?= Functions::loadImage('flag_' . $key . '.gif') ?>
                &#160;<?= Validate::checkout($val) ?>
            </label><br/>
            <?php endforeach ?>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value=" <?= lng('save') ?> "/>
            <a class="btn" href="<?= Vars::$URI ?>"><?= lng('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>