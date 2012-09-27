<ul class="nav" xmlns="http://www.w3.org/1999/html">
    <?php if (Vars::$MOD == 'adm'): ?>
    <li><h1 class="section-warning"><?= lng('admin_club') ?></h1></li>
    <?php else: ?>
    <li><h1><?= lng('guestbook') ?></h1></li>
    <?php endif ?>
</ul>

<?php if (!Vars::$SYSTEM_SET['mod_guest']): ?>
<div class="alarm"><?= lng('guestbook_closed') ?></div>
<?php endif ?>

<div class="form-container">
    <div class="form-block">
<form name="form" action="<?= Vars::$URI ?>?act=say<?= ($this->mod ? '&amp;mod=adm' : '') ?>" method="post">
    <?php if(!Vars::$USER_ID): ?>
    <?= lng('name') ?> (max 25):<br/><input type="text" name="name" maxlength="25"/><br/>
    <?php endif ?>
    <b><?= lng('message') ?></b> <small>(max 5000)</small>:<br/>
    <?= (Vars::$USER_ID && !Vars::$IS_MOBILE ? TextParser::autoBB('form', 'msg') : '') ?>
    <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="msg"></textarea><br/>
    <?php if (!Vars::$USER_ID): ?>
    <?= Captcha::display(1) ?><br/>
    <?php endif ?>
    <input class="btn btn-primary" type="submit" name="submit" value="<?= lng('sent') ?>"/>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
</div></div>


<?php if (isset($this->list)): ?>
<?php foreach ($this->list as $key => $val): ?>
    <div class="<?= $key % 2 ? 'block-odd' : 'block-even' ?>">
        <?= $val ?>
    </div>
    <?php endforeach ?>
    <?php if (Vars::$USER_RIGHTS >= 7): ?>
    <a href="<?= Vars::$URI . '?act=clean' . ($this->mod ? '&amp;mod=adm' : '') ?>"><?= lng('clear') ?></a>
    <?php endif ?>
<?php else: ?>
<div class="block-even align-center"><br/><?= lng('guestbook_empty') ?><br/><br/></div>
<?php endif ?>