<ul class="nav">
    <li><h1 class="section-personal"><?= __('exit') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block" style="padding: 20px; text-align: center;">
        <form action="<?= Router::getUrl(2) ?>/exit" method="post">
            <label><?= __('exit_warning') ?></label>
            <br/><br/>
            <label class="small"><input type="checkbox" name="clear" value="1"/>&#160;<?= __('clear_authorisation') ?></label>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value="  <?= __('exit') ?>  "/>
            <a class="btn btn-large" href="<?= $this->backlink ?>"><?= __('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->token ?>"/>
        </form>
    </div>
</div>