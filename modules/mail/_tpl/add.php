<ul class="nav">
    <li><h1><?= lng('write_message') ?></h1></li>
</ul>
<?= $this->mail_error ?>
<div class="form-container">
    <form name="form" action="<?= $this->url ?>" method="post" enctype="multipart/form-data">
        <div class="form-block">
			<label for="name"><?= lng('nick') ?></label><br/>
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
			<label for="text"><?= lng('message') ?></label><br/>
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <? endif ?>
            <textarea id="text" rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"><?= $this->text ?></textarea><br/>
            <small><?= lng('text_size') ?></small>
            <br/>
			<label for="file"><?= lng('file') ?></label><br/>
            <input id="file" type="file" name="0"/><br/>
            <small><?= lng('max_file_size') ?> <?= $this->size ?> кб.</small>
            <br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $this->maxsize ?>"/>
			<input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('sent') ?>"/>
			<a class="btn" href="<?= Vars::$URI ?>"><?= lng('cancel') ?></a>
        </div>
    </form>
</div>