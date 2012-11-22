<ul class="nav">
    <li><h1 class="section-warning"><?= lng('system_settings') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->save)) : ?>
    <div class="form-block"><?= lng('settings_saved') ?></div>
    <?php endif ?>
    <?php if (isset($this->reset)) : ?>
    <div class="form-block"><?= lng('settings_default') ?></div>
    <?php endif ?>

    <form action="<?= Vars::$URI ?>" method="post">
        <div class="form-block">
            <label for="timeshift"><?= lng('time_shift') ?></label><br/>
            <input id="timeshift" class="small" type="text" name="timeshift" size="2" maxlength="3" value="<?= Vars::$SYSTEM_SET['timeshift'] ?>"/><span class="input-help">&#160;+-12 <?= lng('hours') ?></span><br/>
            <label for="timeshift" class="small">
                <span class="badge badge-green"><?= date("H:i", time() + Vars::$SYSTEM_SET['timeshift'] * 3600) ?></span> <?= lng('system_time') ?><br/>
                <span class="badge badge-red"><?= date("H:i") ?></span> <?= lng('server_time') ?>
            </label>
            <br/><br/>
            <label for="copyright"><?= lng('site_copyright') ?></label><br/>
            <input id="copyright" type="text" name="copyright" value="<?= Validate::filterString(Vars::$SYSTEM_SET['copyright']) ?>"/><br/>
            <label for="email"><?= lng('site_email') ?></label><br/>
            <input id="email" type="text" name="email" value="<?= Validate::filterString(Vars::$SYSTEM_SET['email']) ?>"/><br/>
            <label for="filesize"><?= lng('file_maxsize') ?></label><br/>
            <input id="filesize" class="small" type="text" name="filesize" value="<?= intval(Vars::$SYSTEM_SET['flsz']) ?>"/><span class="input-help">&#160;kB</span><br/>
            <label class="small"><input name="gzip" type="checkbox" value="1" <?= (Vars::$SYSTEM_SET['gzip'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('gzip_compress') ?></label>
            <br/><br/>
            <label for="keywords">META Keywords</label><br/>
            <textarea id="keywords" rows="<?= Vars::$USER_SET['field_h'] ?>" name="keywords"><?= Validate::filterString(Vars::$SYSTEM_SET['meta_key']) ?></textarea><br/>
            <label for="description">META Description</label><br/>
            <textarea id="description" rows="<?= Vars::$USER_SET['field_h'] ?>" name="description"><?= Validate::filterString(Vars::$SYSTEM_SET['meta_desc']) ?></textarea>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value=" <?= lng('save') ?> "/>
            <a class="btn" href="<?= Vars::$MODULE_URI ?>"><?= lng('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>