<table id="nav-bottom">
    <tr>
        <td>
            <?php if (Vars::$USER_ID || Vars::$USER_SYS['view_online']): ?>
            <a href="<?= Vars::$HOME_URL ?>online/"><i class="icn-w-man-woman"></i><?= Counters::usersOnline() ?> :: <?= Counters::guestaOnline() ?></a>
            <?php else: ?>
            <i class="icn-w-man-woman"></i><?= Counters::usersOnline() ?> :: <?= Counters::guestaOnline() ?>
            <?php endif ?>
        </td>
        <td><a href="#top"><i class="icn-w-top"></i><?= __('up') ?></a></td>
    </tr>
</table>