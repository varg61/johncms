<table id="nav-bottom">
    <tr>
        <td>
            <?php if (Vars::$USER_ID || Vars::$USER_SYS['view_online']): ?>
            <a href="<?= Vars::$HOME_URL ?>/online"><i class="icn-w-man-woman"></i><?= Counters::usersOnline() ?> :: <?= Counters::guestaOnline() ?></a>
            <?php else: ?>
            <i class="icn-w-man-woman"></i><?= Counters::usersOnline() ?> :: <?= Counters::guestaOnline() ?>
            <?php endif ?>
        </td>
        <?php if ((isset(Vars::$ACL['stat']) && Vars::$ACL['stat'] == 3) || (isset(Vars::$ACL['stat']) && Vars::$ACL['stat'] == 2 && Vars::$USER_ID) || (isset(Vars::$ACL['stat']) && Vars::$ACL['stat'] == 1 && Vars::$USER_RIGHTS >= 7)): ?>
        <td><a href="<?= Vars::$HOME_URL ?>/stats"><i class="icn-w-piechart"></i><?= statistic::$hity ?> :: <?= statistic::$hosty ?></a></td>
        <?php endif ?>
        <td><a href="#top"><i class="icn-w-top"></i><?= lng('up') ?></a></td>
    </tr>
</table>