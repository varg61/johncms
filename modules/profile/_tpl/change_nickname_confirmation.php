<div class="gmenu">
    <p><?= lng('change_nickname_confirm') ?></p>
    <p><?= lng('new_nickname') ?>: <b><?= $this->nickname ?></b></p>
    <p><a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= lng('continue') ?></a></p>
</div>