<ul class="nav">
    <?php if (Vars::$USER_ID && $this->user['id'] == Vars::$USER_ID): ?>
    <li><h1 class="section-personal"><?= __('my_profile') ?></h1></li>
    <?php else: ?>
    <li><h1><?= __('user_profile') ?></h1></li>
    <?php endif ?>
</ul>
<div class="user-block"><?= Functions::displayUser($this->user) ?></div>
<ul class="nav">
    <li><h2><?= __('reputation') ?></h2></li>
    <li>
        <a href="#">
            <div id="rep-container">
                <div id="rep-row">
                    <div id="rep-counter" class="rel_a_font"><?= $this->rel_count ?></div>
                    <div id="rep-selector">
                        <div class="progress">
                            <?php if ($this->my_rel == 2): ?>
                            <div class="bar" style="width: 100%"></div>
                            <?php endif ?>
                        </div>
                        <div class="progress progress-green">
                            <?php if ($this->my_rel == 1): ?>
                            <div class="bar" style="width: 100%"></div>
                            <?php endif ?>
                        </div>
                        <div class="progress progress-gray">
                            <?php if ($this->my_rel == 0): ?>
                            <div class="bar" style="width: 100%"></div>
                            <?php endif ?>
                        </div>
                        <div class="progress progress-orange">
                            <?php if ($this->my_rel == -1): ?>
                            <div class="bar" style="width: 100%"></div>
                            <?php endif ?>
                        </div>
                        <div class="progress progress-red">
                            <?php if ($this->my_rel == -2): ?>
                            <div class="bar" style="width: 100%"></div>
                            <?php endif ?>
                        </div>
                    </div>
                    <div id="rep-diagram">
                        <div class="progress">
                            <div class="bar" style="width: <?= $this->bar['a'] ?>%"></div>
                        </div>
                        <div class="progress progress-green">
                            <div class="bar" style="width: <?= $this->bar['b'] ?>%"></div>
                        </div>
                        <div class="progress progress-gray">
                            <div class="bar" style="width: <?= $this->bar['c'] ?>%"></div>
                        </div>
                        <div class="progress progress-orange">
                            <div class="bar" style="width: <?= $this->bar['d'] ?>%"></div>
                        </div>
                        <div class="progress progress-red">
                            <div class="bar" style="width: <?= $this->bar['e'] ?>%"></div>
                        </div>
                    </div>
                    <div id="rep-arrow"><i class="icn-arrow right"></i></div>
                </div>
            </div>
        </a>
    </li>
    <li><h2><?= __('information') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>?act=info&amp;user=<?= $this->user['id'] ?>"><i class="icn-info"></i><?= __('personal_data') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>?act=activity&amp;user=<?= $this->user['id'] ?>"><i class="icn-piechart"></i><?= __('activity') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="profile.php?act=ban&amp;user=<?= $this->user['id'] ?>"><i class="icn-violations"></i><?= __('infringements') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->bancount ?></span></a></li>

    <li><h2><?= __('personal') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>/album?act=list&amp;user=<?= $this->user['id'] ?>"><i class="icn-image"></i><?= __('photo_album') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->total_photo ?></span></a></li>
    <li><a href="<?= Vars::$URI ?>?act=guestbook&amp;user=<?= $this->user['id'] ?>"><i class="icn-dialogue"></i><?= __('guestbook') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->user['comm_count'] ?></span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/friends?id=<?= $this->user['id'] ?>"><i class="icn-man-woman"></i><?= __('friends') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Functions::friendsCount($this->user['id']) ?></span></a></li>

    <?php if (Vars::$USER_ID && Vars::$USER_ID != $this->user['id']): ?>
    <li><h2><?= __('mail') ?></h2></li>
        <?php if (empty($this->banned)): ?>
        <li><a href="<?= Vars::$HOME_URL ?>/mail?act=messages&amp;id=<?= $this->user['id'] ?>"><i class="icn-envelope"></i><?= __('contact_write') ?><i class="icn-arrow right"></i></a></li>
        <li><a href="<?= Vars::$HOME_URL ?>/contacts?act=select&amp;mod=contact&amp;id=<?= $this->user['id'] ?>"><i class="icn-addressbook"></i><?= ($this->num_cont ? __('contact_delete') : __('contact_add')) ?><i class="icn-arrow right"></i></a></li>
        <?php endif ?>
    <li><a href="<?= Vars::$HOME_URL ?>/contacts?act=select&amp;mod=banned&amp;id=<?= $this->user['id'] ?>"><i class="icn-block"></i><?= (isset($this->banned) && $this->banned == 1 ? __('contact_delete_ignor') : __('contact_add_ignor')) ?><i class="icn-arrow right"></i></a></li>
    <?php endif ?>

    <?php if (Vars::$USER_RIGHTS >= 3 && $this->user['id'] != Vars::$USER_ID): ?>
    <li><h2><?= __('administration') ?></h2></li>
        <?php if (Vars::$USER_RIGHTS >= 7): ?>
        <li><a href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><i class="icn-settings-red"></i><?= __('settings') ?><i class="icn-arrow right"></i></a></li>
        <?php endif ?>
    <li><a href=""><i class="icn-ban-red"></i><?= __('ban_do') ?><i class="icn-arrow right"></i></a></li>
    <?php endif ?>
</ul>