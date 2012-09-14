<div class="footer">
    <?php if (Vars::$SYSTEM_SET['stat'] == 3 || (Vars::$SYSTEM_SET['stat'] == 2 && Vars::$USER_ID) || (Vars::$SYSTEM_SET['stat'] == 1 && Vars::$USER_RIGHTS >= 7)) : ?>
    <a href="<?= Vars::$HOME_URL ?>/stats"><?= statistic::$hity ?> | <?= statistic::$hosty ?></a>
    <?php endif ?>
</div>