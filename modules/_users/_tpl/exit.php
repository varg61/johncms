<ul class="nav">
    <li><h1 class="section-personal"><?= lng('exit') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block" style="padding: 20px; text-align: center;">
        <form action="<?= Vars::$MODULE_URI ?>/exit" method="post">
            <label><?= lng('exit_warning') ?></label>
            <br/><br/>
            <label class="small"><input type="checkbox" name="clear" value="1"/>&#160;<?= lng('clear_authorisation') ?></label>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value="  <?= lng('exit') ?>  "/>
            <a class="btn btn-large" href="<?= $this->backlink ?>"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->token ?>"/>
        </form>
    </div>
</div>