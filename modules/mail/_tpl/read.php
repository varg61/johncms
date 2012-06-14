<p>
    <a href="<?= Vars::$MODULE_URI ?>?act=messages&amp;id=<?= $this->user_id ?>"><?= lng('answer') ?></a>
</p>
<div class="phdr"><?= $this->pref ?>: <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $this->user_id ?>"><?= $this->contact_login ?></a> (<?= $this->time_message ?>)</div>
<div class="list1">
    <?= $this->text ?>
    <?php if ($this->file): ?>
    <div class="func">
        <?= lng('file') ?>: <?= $this->file ?>
    </div>
    <? endif ?>
</div>
<div class="phdr"><a href="<?= Vars::$MODULE_URI ?>?act=<?= $this->back ?>"><?= lng('back') ?></a></div>
<p>
<div class="func">
    <a href="<?= Vars::$MODULE_URI ?>?act=send&amp;id=<?= Vars::$ID ?>">Переслать</a><br/>
    <?php if ($this->read == 0 && $this->users_id == Vars::$USER_ID): ?>
    <a href="<?= Vars::$MODULE_URI ?>?act=messages&amp;mod=edit&amp;id=<?= Vars::$ID ?>"><?= lng('edit') ?></a><br/>
    <? endif ?>
    <a href="<?= Vars::$MODULE_URI ?>?act=messages&amp;mod=delete&amp;id=<?= Vars::$ID ?>"><?= lng('delete') ?></a><br/>
</div>
</p>
<p>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a><br/>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a>
</p>