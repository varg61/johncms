<div class="phdr">
    <b><?= lng('registration') ?></b>
</div>
<div class="topmenu">
    <p class="red">
        <?= (Vars::$USER_ID ? lng('already_registered') : lng('registration_closed')) ?>
    </p>
</div>