<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $this->user['id'] ?>"><b><?= lng('profile') ?></b></a> | <?= lng('information') ?>
</div>
<?php if ($this->user['id'] == Vars::$USER_ID || (Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $this->user['rights'])) : ?>
<div class="topmenu">
    <a href="profile.php?act=edit&amp;user=<?= $this->user['id'] ?>"><?= lng('edit') ?></a>
</div>
<?php endif; ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p>
</div>
<div class="list2">
    <div class="formblock">
        <label><?= lng('personal_data') ?></label>
        <ul>
            <?php if (file_exists('../files/users/photo/' . $this->user['id'] . '_small.jpg')) : ?>
            <a href="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>.jpg"><img src="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="<?= $this->user['nickname'] ?>" border="0"/></a>
            <?php endif; ?>
            <li><span class="gray"><?= lng('name') ?>:</span> <?= (empty($this->user['imname']) ? '' : $this->user['imname']) ?></li>
            <li><span class="gray"><?= lng('birthday') ?>:</span></li>
            <li><span class="gray"><?= lng('live') ?>:</span> <?= (empty($this->user['live']) ? '' : $this->user['live']) ?></li>
            <li><span class="gray"><?= lng('about') ?>:</span> <?= (empty($this->user['about']) ? '' : '<br />' . Functions::smileys(Validate::filterString($this->user['about'], 1, 1))) ?></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= lng('communication') ?></label>
        <ul>
            <li><span class="gray"><?= lng('phone_number') ?>:</span> <?= (empty($this->user['tel']) ? '' : $this->user['tel']) ?></li>
            <li><span class="gray">E-mail:</span>
                <?php if (!empty($this->user['email']) && $this->user['mailvis'] || Vars::$USER_RIGHTS >= 7 || $this->user['id'] == Vars::$USER_ID) : ?>
                <?= $this->user['email'] . ($this->user['mailvis'] ? '' : '<span class="gray"> [' . lng('hidden') . ']</span>') ?>
                <?php endif; ?>
            </li>
            <li><span class="gray">ICQ:</span> <?= $this->user['icq'] ?></li>
            <li><span class="gray">Skype:</span> <?= $this->user['skype'] ?></li>
            <li><span class="gray"><?= lng('site') ?>:</span> <?= TextParser::tags($this->user['siteurl']) ?></li>
        </ul>
    </div>
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>