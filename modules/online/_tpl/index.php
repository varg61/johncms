<ul class="nav">
    <li><h1><?= __('who_on_site') ?></h1></li>
</ul>
<div class="toolbar-top">
    <a class="btn<?= !Vars::$ACT ? '' : ' btn-primary' ?> btn-mini" href="<?= Vars::$URI ?>">Пользователи</a>
    <?php if ((Vars::$USER_ID && Vars::$USER_DATA['level']) || Vars::$USER_SYS['viev_history']): ?>
    <a class="btn<?= Vars::$ACT == 'history' ? '' : ' btn-primary' ?> btn-mini" href="<?= Vars::$URI ?>?act=history"><?= __('history') ?></a>
    <?php endif ?>
    <?php if (Vars::$USER_RIGHTS): ?>
    <a class="btn<?= Vars::$ACT == 'guest' ? '' : ' btn-primary' ?> btn-mini" href="<?= Vars::$URI ?>?act=guest"><?= __('guests') ?></a>
    <a class="btn<?= Vars::$ACT == 'ip' ? '' : ' btn-primary' ?> btn-mini" href="<?= Vars::$URI ?>?act=ip">IP Активность</a>
    <?php endif ?>
</div>
<?php if (isset($this->list)): ?>
<?php foreach ($this->list as $key => $val): ?>
    <div class="<?= $key % 2 ? 'block-odd' : 'block-even' ?>"><?= $val ?></div>
    <?php endforeach ?>
<?php else: ?>
<div class="form-container">
    <div class="form-block align-center"><?= __('list_empty') ?></div>
</div>
<?php endif ?>