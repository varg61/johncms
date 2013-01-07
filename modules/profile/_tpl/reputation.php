<div class="phdr">
    <a href="<?= $this->url ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? __('user_profile') : __('my_profile')) ?></b></a> | <?= __('reputation') ?>
</div>
<?php if (isset($this->save)): ?>
<div class="gmenu"><p><?= __('vote_adopted') ?></p></div>
<?php endif ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p>
</div>
<div class="menu" style="padding-bottom: 8px">
    <div class="formblock">
        <label><?= ($this->user['id'] == Vars::$USER_ID ? __('my_reputation') : __('reputation')) ?></label>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="rel_a_font">
                    <?= $this->rel['a'] ?>
                </td>
                <td width="90%">
                    <small><?= __('reputation_excellent') ?></small>
                    <div class="bar">
                        <div class="bar_a" style="width: <?= $this->bar['a'] ?>%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_b_font">
                    <?= $this->rel['b'] ?>
                </td>
                <td>
                    <small><?= __('reputation_good') ?></small>
                    <div class="bar">
                        <div class="bar_b" style="width: <?= $this->bar['b'] ?>%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_c_font">
                    <?= $this->rel['c'] ?>
                </td>
                <td>
                    <small><?= __('reputation_neutrally') ?></small>
                    <div class="bar">
                        <div class="bar_c" style="width: <?= $this->bar['c'] ?>%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_d_font">
                    <?= $this->rel['d'] ?>
                </td>
                <td>
                    <small><?= __('reputation_badly') ?></small>
                    <div class="bar">
                        <div class="bar_d" style="width: <?= $this->bar['d'] ?>%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="rel_e_font">
                    <?= $this->rel['e'] ?>
                </td>
                <td>
                    <small><?= __('reputation_verybad') ?></small>
                    <div class="bar">
                        <div class="bar_e" style="width: <?= $this->bar['e'] ?>%"></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
<?php if (Vars::$USER_ID && $this->user['id'] != Vars::$USER_ID): ?>
<form action="<?= $this->url ?>?act=reputation&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="list2">
        <div class="formblock">
            <label><?= __('vote') ?></label><br/>
            <input type="radio" value="2" name="vote" <?= ($this->my_rel == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('reputation_excellent') ?><br/>
            <input type="radio" value="1" name="vote" <?= ($this->my_rel == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('reputation_good') ?><br/>
            <input type="radio" value="0" name="vote" <?= ($this->my_rel == 0 ? 'checked="checked"' : '') ?>/>&#160;<?= __('reputation_neutrally') ?><br/>
            <input type="radio" value="-1" name="vote" <?= ($this->my_rel == -1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('reputation_badly') ?><br/>
            <input type="radio" value="-2" name="vote" <?= ($this->my_rel == -2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('reputation_verybad') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= __('vote') ?>"/>
        </div>
    </div>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
<?php endif ?>
<div class="phdr">
    <a href="<?= $this->url ?>?user=<?= $this->user['id'] ?>"><?= __('back') ?></a>
</div>