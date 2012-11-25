<ul class="nav">
    <?php if (Vars::$MOD == 'adm'): ?>
    <li><h1 class="section-warning"><?= __('admin_club') ?></h1></li>
    <?php else: ?>
    <li><h1><?= __('guestbook') ?></h1></li>
    <?php endif ?>
</ul>

<?php if (!isset(Vars::$ACL['guestbook']) || !Vars::$ACL['guestbook']): ?>
<div class="alarm"><?= __('guestbook_closed') ?></div>
<?php endif ?>

<div class="user-block">
<form name="form" action="<?= Vars::$URI ?>?act=say<?= ($this->mod ? '&amp;mod=adm' : '') ?>" method="post">
    <?php if(!Vars::$USER_ID): ?>
    <?= __('name') ?> (max 25):<br/><input type="text" name="name" maxlength="25"/><br/>
    <?php endif ?>
    <?= (Vars::$USER_ID && !Vars::$IS_MOBILE ? TextParser::autoBB('form', 'msg') : '') ?>
    <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="msg"></textarea><br/>
    <?php if (!Vars::$USER_ID): ?>
    <?= Captcha::display(1) ?><br/>
    <?php endif ?>
    <input class="btn btn-primary" type="submit" name="submit" value="<?= __('write') ?>"/>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
</div>

<ul class="nav"><li><h2><?= __('comments') ?></h2></li></ul>
<?php if (isset($this->list)): ?>
    <?php foreach ($this->list as $key => $val): ?>
        <div class="<?= $key % 2 ? 'block-odd' : 'block-even' ?>">
        <?= $val ?>
        </div>
    <?php endforeach ?>
    <ul class="nav"><li><h2><?= __('total') . ': ' . $this->total ?></h2></li></ul>
    <?php if (Vars::$USER_RIGHTS >= 7): ?>
    <a href="<?= Vars::$URI . '?act=clean' . ($this->mod ? '&amp;mod=adm' : '') ?>"><?= __('clear') ?></a>
    <?php endif ?>
<?php else: ?>
    <div class="block-even align-center"><br/><?= __('guestbook_empty') ?><br/><br/></div>
<?php endif ?>