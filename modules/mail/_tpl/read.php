<p>
    <a href="<?= $this->link ?>?act=messages&amp;id=<?= $this->user_id ?>"><?= __('answer') ?></a>
</p>
<div class="phdr"><?= $this->pref ?>: <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $this->user_id ?>"><?= $this->contact_login ?></a> (<?= $this->time_message ?>)</div>
<div class="list1">
    <?= $this->text ?>
    <?php if ($this->file): ?>
    <div class="func">
        <?= __('file') ?>: <?= $this->file ?>
    </div>
    <? endif ?>
</div>
<div class="phdr"><a href="<?= $this->link ?>?act=<?= $this->back ?>"><?= __('back') ?></a></div>
<p>
<div class="func">
    <a href="<?= $this->link ?>?act=send&amp;id=<?= Vars::$ID ?>">Переслать</a><br/>
    <?php if ($this->read == 0 && $this->users_id == Vars::$USER_ID): ?>
    <a href="<?= $this->link ?>?act=edit&amp;id=<?= Vars::$ID ?>"><?= __('edit') ?></a><br/>
    <? endif ?>
    <a href="<?= $this->link ?>?act=messages&amp;mod=delete&amp;id=<?= Vars::$ID ?>"><?= __('delete') ?></a><br/>
</div>
</p>
<p>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= __('contacts') ?></a><br/>
    <a href="<?= $this->link ?>"><?= __('mail') ?></a>
</p>