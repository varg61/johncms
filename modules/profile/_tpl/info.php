<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b></a> | <?= lng('information') ?>
</div>
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
            <li><span class="gray"><?= lng('name') ?>:</span> <?= (empty($this->user['imname']) ? '' : Validate::filterString($this->user['imname'])) ?></li>
            <li><span class="gray"><?= lng('birthday') ?>:</span></li>
            <li><span class="gray"><?= lng('live') ?>:</span> <?= (empty($this->user['live']) ? '' : Validate::filterString($this->user['live'])) ?></li>
            <li><span class="gray"><?= lng('about') ?>:</span> <?= (empty($this->user['about']) ? '' : '<br />' . Functions::smileys(Validate::filterString($this->user['about'], 1, 1))) ?></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= lng('communication') ?></label>
        <ul>
            <li><span class="gray"><?= lng('phone_number') ?>:</span> <?= (empty($this->user['tel']) ? '' : Validate::filterString($this->user['tel'])) ?></li>
            <li><span class="gray">E-mail:</span>
                <?php if (!empty($this->user['email']) && $this->user['mailvis'] || Vars::$USER_RIGHTS >= 7 || $this->user['id'] == Vars::$USER_ID) : ?>
                <?= Validate::filterString($this->user['email']) . ($this->user['mailvis'] ? '' : '<span class="gray"> [' . lng('hidden') . ']</span>') ?>
                <?php endif; ?>
            </li>
            <li><span class="gray">ICQ:</span> <?= $this->user['icq'] ?></li>
            <li><span class="gray">Skype:</span> <?= Validate::filterString($this->user['skype']) ?></li>
            <li><span class="gray"><?= lng('site') ?>:</span> <?= Validate::filterString($this->user['siteurl'], 0, 1) ?></li>
        </ul>
    </div>
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>