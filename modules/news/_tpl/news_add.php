<ul class="nav">
    <li><h1><?= lng('news') ?> :: <?= lng('add') ?></h1></li>
</ul>
<div class="form-container">
    <form name="form" action="<?= Vars::$URI ?>?act=add" method="post">
        <div class="form-block">
            <label for="name"><?= lng('article_title') ?></label><br/>
            <input id="name" type="text" name="name"/><br/>
            <label for="text"><?= lng('text') ?></label>
            <?= !Vars::$IS_MOBILE ? TextParser::autoBB('form', 'text') : '' ?>
            <textarea id="text" rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"></textarea>
            <label><?= lng('discuss') ?></label><br/>
            <?php foreach ($this->list as $val): ?>
            <div><?= $val ?></div>
            <?php endforeach ?>
            <br/><input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('save') ?>"/>
            <a class="btn btn-large" href="<?= Vars::$URI ?>"><?= lng('cancel') ?></a>
        </div>
    </form>
</div>