<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b></a> | <?= lng('information') ?>
</div>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p>
</div>
<div class="menu">
    <div class="formblock">
        <label><?= lng('relationship') ?></label>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="rel_a_font">
                    26
                </td>
                <td>
                    <small>Отлично</small>
                    <div class="rel_a" style="height: 6px; width: 20px"></div>
                </td>
            </tr>
            <tr>
                <td class="rel_b_font">
                    150
                </td>
                <td>
                    <small>Хорошо</small>
                    <div class="rel_b" style="height: 6px; width: 40px"></div>
                </td>
            </tr>
            <tr>
                <td class="rel_c_font">
                    34
                </td>
                <td>
                    <small>Нейтрально</small>
                    <div class="rel_c" style="height: 6px; width: 40px"></div>
                </td>
            </tr>
            <tr>
                <td class="rel_d_font">
                    34
                </td>
                <td>
                    <small>Плохо</small>
                    <div class="rel_d" style="height: 6px; width: 40px"></div>
                </td>
            </tr>
            <tr>
                <td class="rel_e_font">
                    34
                </td>
                <td>
                    <small>Очень плохо</small>
                    <div class="rel_e" style="height: 6px; width: 40px"></div>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="list2">
    <div class="formblock">
        gg
    </div>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>