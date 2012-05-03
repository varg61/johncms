<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= lng('profile') ?></b></a> | <?= lng('statistics') ?>
</div>
<div class="user"><p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p></div>
<div class="list2">
    <div class="formblock">
        <label><?= Functions::getImage('rating.png', '', 'class="left"') . '&#160;' . lng('statistics') ?></label><br/>
        <ul>
            <?php if (Vars::$USER_RIGHTS && !$this->user['level']) : ?>
            <li><?= lng('awaiting_registration') ?></li>
            <?php endif; ?>
            <?php if (isset($this->lastvisit)) : ?>
            <li><span class="gray"><?= lng('last_visit') ?>:</span> <?= $this->lastvisit ?></li>
            <?php endif; ?>
            <li><span class="gray"><?= ($this->user['sex'] == 'm' ? lng('registered_m') : lng('registered_w')) ?>:</span> <?= date("d.m.Y (H:i)", $this->user['join_date']) ?></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= Functions::getImage('user_edit.png', '', 'class="left"') . '&#160;' . lng('activity') ?></label><br/>
        <ul>
            <li><span class="gray"><?= lng('forum') ?>:</span> <a href="<?= Vars::$URI ?>?act=activity&amp;user=<?= $this->user['id'] ?>"><?= $this->user['count_forum'] ?></a></li>
            <li><span class="gray"><?= lng('comments') ?>:</span> <a href="<?= Vars::$URI ?>?act=activity&amp;mod=comments&amp;user=<?= $this->user['id'] ?>"><?= $this->user['count_comments'] ?></a></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= Functions::getImage('award.png', '', 'class="left"') . '&#160;' . lng('achievements') ?></label><br/>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <?php foreach ($this->num as $val) : ?>
                <td width="28" align="center">
                    <small><?= $val ?></small>
                </td>
                <?php endforeach; ?>
                <td></td>
            </tr>
            <?php foreach ($this->query as $key => $val) : ?>
            <tr>
                <?php foreach ($this->num as $achieve) : ?>
                <td align="center"><?= Functions::getImage(($this->user[$key] >= $achieve ? 'green' : 'red') . '.png') ?></td>
                <?php endforeach; ?>
                <td>
                    <small><b><?= $val ?></b></small>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<div class="phdr">
    <a href="profile.php?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>