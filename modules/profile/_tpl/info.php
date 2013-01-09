<div class="phdr">
    <a href="<?= $this->url ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? __('user_profile') : __('my_profile')) ?></b></a> | <?= __('information') ?>
</div>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p>
</div>
<div class="list2">
    <div class="formblock">
        <label><?= __('personal_data') ?></label>
        <ul>
            <?php if (file_exists('../files/users/photo/' . $this->user['id'] . '_small.jpg')) : ?>
            <a href="<?= Vars::$HOME_URL ?>files/users/photo/<?= $this->user['id'] ?>.jpg"><img src="<?= Vars::$HOME_URL ?>files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="<?= $this->user['nickname'] ?>" border="0"/></a>
            <?php endif; ?>
            <?php if (Vars::$USER_RIGHTS && !$this->user['level']) : ?>
            <li><?= __('awaiting_registration') ?></li>
            <?php endif; ?>
            <li><span class="gray"><?= ($this->user['sex'] == 'm' ? __('registered_m') : __('registered_w')) ?>:</span> <?= Functions::displayDate($this->user['join_date']) ?></li>
            <?php if (time() > $this->user['last_visit'] + 300) : ?>
            <li><span class="gray"><?= __('last_visit') ?>:</span> <?= Functions::displayDate($this->user['last_visit']) ?></li>
            <?php endif; ?>
            <li><span class="gray"><?= __('name') ?>:</span> <?= (empty($this->user['imname']) ? '' : Validate::checkout($this->user['imname'])) ?></li>
            <li><span class="gray"><?= __('birthday') ?>:</span></li>
            <li><span class="gray"><?= __('live') ?>:</span> <?= (empty($this->user['live']) ? '' : Validate::checkout($this->user['live'])) ?></li>
            <li><span class="gray"><?= __('about') ?>:</span> <?= (empty($this->user['about']) ? '' : '<br />' . Functions::smilies(Validate::checkout($this->user['about'], 1, 1))) ?></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= __('communication') ?></label>
        <ul>
            <li><span class="gray"><?= __('phone_number') ?>:</span> <?= (empty($this->user['tel']) ? '' : Validate::checkout($this->user['tel'])) ?></li>
            <li><span class="gray">E-mail:</span>
                <?php if (!empty($this->user['email']) && $this->user['mailvis'] || Vars::$USER_RIGHTS >= 7 || $this->user['id'] == Vars::$USER_ID) : ?>
                <?= Validate::checkout($this->user['email']) . ($this->user['mailvis'] ? '' : '<span class="gray"> [' . __('hidden') . ']</span>') ?>
                <?php endif; ?>
            </li>
            <li><span class="gray">ICQ:</span> <?= $this->user['icq'] ?></li>
            <li><span class="gray">Skype:</span> <?= Validate::checkout($this->user['skype']) ?></li>
            <li><span class="gray"><?= __('site') ?>:</span> <?= Validate::checkout($this->user['siteurl'], 0, 1) ?></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= __('activity') ?></label><br/>
        <ul>
            <li><span class="gray"><?= __('forum') ?>:</span> <a href="<?= $this->url ?>?act=activity&amp;user=<?= $this->user['id'] ?>"><?= $this->user['count_forum'] ?></a></li>
            <li><span class="gray"><?= __('comments') ?>:</span> <a href="<?= $this->url ?>?act=activity&amp;mod=comments&amp;user=<?= $this->user['id'] ?>"><?= $this->user['count_comments'] ?></a></li>
        </ul>
    </div>
</div>