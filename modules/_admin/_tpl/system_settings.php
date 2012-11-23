<ul class="nav">
    <li><h1 class="section-warning"><?= lng('system_settings') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (!empty($this->error)): ?>
        <div class="form-block error"><?= lng('errors_occurred') ?></div>
    <?php elseif (isset($this->save)): ?>
        <div class="form-block confirm"><?= lng('settings_saved') ?></div>
    <?php endif ?>

    <form action="<?= Vars::$URI ?>?act=system_settings" method="post">
        <div class="form-block">
            <label for="timeshift"><?= lng('time_shift') ?></label><br/>
            <?php if (isset($this->error['timeshift'])): ?>
                <span class="label label-red"><?= $this->error['timeshift'] ?></span><br/>
            <?php endif ?>
            <input id="timeshift" class="small<?= (isset($this->error['timeshift']) ? ' error' : '') ?>" type="text" name="timeshift" size="2" maxlength="3" value="<?= Vars::$SYSTEM_SET['timeshift'] ?>"/><span class="input-help">&#160;+-12 <?= lng('hours') ?></span><br/>
            <label for="timeshift" class="small">
                <span class="badge badge-green"><?= date("H:i", time() + Vars::$SYSTEM_SET['timeshift'] * 3600) ?></span> <?= lng('system_time') ?><br/>
            </label><br/><br/>

            <label for="copyright"><?= lng('site_copyright') ?></label><br/>
            <?php if (isset($this->error['copyright'])): ?>
                <span class="label label-red"><?= $this->error['copyright'] ?></span><br/>
            <?php endif ?>
            <textarea id="copyright" <?= (isset($this->error['copyright']) ? 'class="error"' : '') ?> rows="<?= Vars::$USER_SET['field_h'] ?>" name="copyright"><?= Validate::checkout(Vars::$SYSTEM_SET['copyright']) ?></textarea><br/>

            <label for="email"><?= lng('site_email') ?></label><br/>
            <?php if (isset($this->error['email'])): ?>
                <span class="label label-red"><?= $this->error['email'] ?></span><br/>
            <?php endif ?>
            <input id="email" <?= (isset($this->error['email']) ? 'class="error"' : '') ?> type="text" name="email" value="<?= Validate::checkout(Vars::$SYSTEM_SET['email']) ?>"/><br/>

            <label for="filesize"><?= lng('file_maxsize') ?></label><br/>
            <?php if (isset($this->error['filesize'])): ?>
                <span class="label label-red"><?= $this->error['filesize'] ?></span><br/>
            <?php endif ?>
            <input id="filesize" class="small<?= (isset($this->error['filesize']) ? ' error' : '') ?>" type="text" name="filesize" value="<?= intval(Vars::$SYSTEM_SET['filesize']) ?>"/><span class="input-help">&#160;100 - 50000kB</span><br/>
            <span class="input-help"><?= lng('filesize_note') ?></span><br/>

            <label class="small"><input name="gzip" type="checkbox" value="1" <?= (Vars::$SYSTEM_SET['gzip'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('gzip_compress') ?></label><br/><br/>

            <label><?= lng('profiling') ?></label><br/>
            <label class="small"><input name="generation" type="checkbox" value="1" <?= (Vars::$SYSTEM_SET['generation'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('profiling_generation') ?></label><br/>
            <label class="small"><input name="memory" type="checkbox" value="1" <?= (Vars::$SYSTEM_SET['memory'] ? 'checked="checked"' : '') ?>/>&#160;<?= lng('profiling_memory') ?></label><br/><br/>

            <label for="keywords">META Keywords</label><br/>
            <?php if (isset($this->error['keywords'])): ?>
                <span class="label label-red"><?= $this->error['keywords'] ?></span><br/>
            <?php endif ?>
            <textarea id="keywords" <?= (isset($this->error['keywords']) ? 'class="error"' : '') ?> rows="<?= Vars::$USER_SET['field_h'] ?>" name="keywords"><?= Validate::checkout(Vars::$SYSTEM_SET['keywords']) ?></textarea><br/>
            <span class="input-help"><?= lng('keywords_note') ?></span><br/>

            <label for="description">META Description</label><br/>
            <?php if (isset($this->error['description'])): ?>
                <span class="label label-red"><?= $this->error['description'] ?></span><br/>
            <?php endif ?>
            <textarea id="description" <?= (isset($this->error['description']) ? 'class="error"' : '') ?> rows="<?= Vars::$USER_SET['field_h'] ?>" name="description"><?= Validate::checkout(Vars::$SYSTEM_SET['description']) ?></textarea><br/>
            <span class="input-help"><?= lng('description_note') ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" name="submit" value=" <?= lng('save') ?> "/>
            <a class="btn" href="<?= Vars::$URI ?>"><?= lng('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>