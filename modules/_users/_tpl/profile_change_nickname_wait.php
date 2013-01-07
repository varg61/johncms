<div class="phdr">
    <a href="<?= Router::getUrl(3) ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? __('profile') : __('my_profile')) ?></b></a> | <?= __('change_nickname') ?>
</div>
<div class="menu">
    <div class="formblock">
        <?= __('change_nickname_note2') . ' ' . Functions::displayDate($this->user['change_time']) ?><br/>
        <?= __('change_nickname_note1') . ' ' . Vars::$USER_SYS['change_period'] . ' ' . __('days') ?>
    </div>
    <div class="formblock">
        <?= __('change_nickname_note3') . ' ' . Functions::displayDate($this->change_time) ?>
    </div>
</div>
<div class="phdr">
    <a href="<?= Router::getUrl(3) ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= __('back') ?></a>
</div>