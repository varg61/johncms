<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('change_nickname') ?>
</div>
<div class="menu">
    <div class="formblock">
        <?= lng('change_nickname_note2') . ' ' . Functions::displayDate($this->user['change_time']) ?><br/>
        <?= lng('change_nickname_note1') . ' ' . Vars::$USER_SYS['change_period'] . ' ' . lng('days') ?>
    </div>
    <div class="formblock">
        <?= lng('change_nickname_note3') . ' ' . Functions::displayDate($this->change_time) ?>
    </div>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>