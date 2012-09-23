<table id="nav-bottom">
    <tr>
        <td>
            <i class="icn-w-man-woman"></i><?= Counters::usersOnline() ?>
        </td>
        <?php if (Vars::$SYSTEM_SET['stat'] == 3 || (Vars::$SYSTEM_SET['stat'] == 2 && Vars::$USER_ID) || (Vars::$SYSTEM_SET['stat'] == 1 && Vars::$USER_RIGHTS >= 7)): ?>
        <td>
            <i class="icn-w-piechart"></i><a href="<?= Vars::$HOME_URL ?>/stats"><?= statistic::$hity ?> :: <?= statistic::$hosty ?></a>
        </td>
        <?php endif ?>
        <td>
            <a href="#top"><i class="icn-w-top"></i><?= lng('up') ?></a>
        </td>
    </tr>
</table>