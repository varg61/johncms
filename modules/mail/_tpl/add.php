<ul class="nav">
    <li><h1 class="section-personal"><?= __('write_message') ?></h1></li>
</ul>
<?= $this->mail_error ?>
<div class="form-container">
    <form name="form" action="<?= $this->url ?>" method="post" enctype="multipart/form-data">
        <div class="form-block">
			<label for="name"><?= __('nick') ?></label><br/>
            <input id="name" type="text" name="login" value="<?= $this->login ?>"/><br/>
            <?php if ($this->count_contact): ?>
			<label for="contact_id">Или выберите из списка:</label><br/>
            <select name="contact_id" id="contact_id">
                <option value="">Выбрать контакт</option>
                <?php foreach ($this->query as $row): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nickname'] ?></option>
                <?php endforeach ?>
            </select><br/>
            <? endif ?>
			<label for="text"><?= __('message') ?></label><br/>
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <? endif ?>
            <textarea id="text" rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"><?= $this->text ?></textarea><br/>
            <span class="description"><?= __('text_size') ?></span>
            <br/>
			<label for="file"><?= __('file') ?></label><br/>
            <input id="file" type="file" name="0"/><br/>
            <span class="description"><?= __('max_file_size') ?> <?= $this->size ?> кб.</span>
            <br/><br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $this->maxsize ?>"/>
			<input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= __('sent') ?>"/>
			<a class="btn" href="<?= Router::getUrl(2) ?>"><?= __('cancel') ?></a>
        </div>
    </form>
</div>