<ul class="nav">
    <li><h1><?= __('news') ?> :: <?= __('add') ?></h1></li>
</ul>
<div class="form-container">
    <form name="form" action="<?= Vars::$URI ?>?act=add" method="post">
        <div class="form-block">
            <label for="name"><?= __('article_title') ?></label><br/>
            <?php if (isset($this->error) && isset($this->error['title'])) : ?>
            <span class="label label-red"><?= $this->error['title'] ?></span><br/>
            <?php endif ?>
            <input id="name" type="text" name="name" value="<?= (isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '') ?>" <?= (isset($this->error['title']) ? 'class="error"' : '') ?>/><br/>
            <label for="text"><?= __('text') ?></label>
            <?= !Vars::$IS_MOBILE ? TextParser::autoBB('form', 'text') : '' ?>
            <?php if (isset($this->error) && isset($this->error['text'])) : ?>
            <span class="label label-red"><?= $this->error['text'] ?></span><br/>
            <?php endif ?>
            <textarea id="text" rows="<?= Vars::$USER_SET['field_h'] ?>" name="text" <?= (isset($this->error['text']) ? 'class="error"' : '') ?>><?= (isset($_POST['text']) ? htmlspecialchars(trim($_POST['text'])) : '') ?></textarea>
            <label><?= __('discuss') ?></label><br/>
            <?php foreach ($this->list as $val): ?>
            <div><?= $val ?></div>
            <?php endforeach ?>
            <br/><input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= __('save') ?>"/>
            <a class="btn" href="<?= Vars::$URI ?>"><?= __('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>