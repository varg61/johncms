<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b></a> | <?= lng('information') ?>
</div>
<?php if (isset($this->save)): ?>
<div class="gmenu"><p><?= lng('vote_adopted') ?></p></div>
<?php endif ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p>
</div>
<div class="menu" style="padding-bottom: 8px">
    <div class="formblock">
        <label><?= lng('relationship') ?></label>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="rel_a_font">
                    26
                </td>
                <td width="90%">
                    <small><?= lng('relationship_excellent') ?></small>
                    <div class="bar">
                        <div class="bar_a" style="width: 30%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_b_font">
                    150
                </td>
                <td>
                    <small><?= lng('relationship_good') ?></small>
                    <div class="bar">
                        <div class="bar_b" style="width: 60%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_c_font">
                    34
                </td>
                <td>
                    <small><?= lng('relationship_neutrally') ?></small>
                    <div class="bar">
                        <div class="bar_c" style="width: 45%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_d_font">
                    34
                </td>
                <td>
                    <small><?= lng('relationship_badly') ?></small>
                    <div class="bar">
                        <div class="bar_d" style="width: 20%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_e_font">
                    34
                </td>
                <td>
                    <small><?= lng('relationship_verybad') ?></small>
                    <div class="bar">
                        <div class="bar_e" style="width: 10%"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php if($this->user['id'] != Vars::$USER_ID): ?>
<form action="<?= Vars::$URI ?>?act=relationship&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="list2">
        <div class="formblock">
            <label><?= lng('my_relationship') ?></label><br/>
            <input type="radio" value="2" name="vote" <?= ($this->user['rights'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('relationship_excellent') ?><br/>
            <input type="radio" value="1" name="vote" <?= ($this->user['rights'] == 4 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('relationship_good') ?><br/>
            <input type="radio" value="0" name="vote" <?= ($this->user['rights'] == 5 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('relationship_neutrally') ?><br/>
            <input type="radio" value="-1" name="vote" <?= ($this->user['rights'] == 5 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('relationship_badly') ?><br/>
            <input type="radio" value="-2" name="vote" <?= ($this->user['rights'] == 6 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('relationship_verybad') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('vote') ?>"/>
        </div>
    </div>
</form>
<?php endif ?>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>