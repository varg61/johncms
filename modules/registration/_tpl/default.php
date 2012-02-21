<div class="phdr">
    <b><?= Vars::$LNG['registration'] ?></b>
</div>
<div class="topmenu">
    <p class="red">
        <?= (Vars::$USER_ID ? $this->lng_reg['already_registered'] : $this->lng_reg['registration_closed']) ?>
    </p>
</div>