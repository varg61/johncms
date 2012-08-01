<div class="phdr">
    <b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b>
</div>
<?php if (isset($this->menu)) : ?>
<div class="topmenu">
    <?= $this->menu ?>
</div>
<?php endif; ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<div class="menu">
    <div class="formblock">
        <label><?= lng('reputation') ?></label>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="rel_a_font">
                    <?= $this->rel_count ?>
                </td>
                <td align="right">
                    <?php if (Vars::$USER_ID): ?>
                    <div class="bar" style="width: 8px; height: 6px">
                        <?php if ($this->my_rel == 2): ?>
                        <div class="bar_a" style="width: 100%"></div>
                        <?php endif ?>
                    </div>
                    <div class="bar" style="width: 8px; height: 6px">
                        <?php if ($this->my_rel == 1): ?>
                        <div class="bar_b" style="width: 100%"></div>
                        <?php endif ?>
                    </div>
                    <div class="bar" style="width: 8px; height: 6px">
                        <?php if ($this->my_rel == 0): ?>
                        <div class="bar_c" style="width: 100%"></div>
                        <?php endif ?>
                    </div>
                    <div class="bar" style="width: 8px; height: 6px">
                        <?php if ($this->my_rel == -1): ?>
                        <div class="bar_d" style="width: 100%"></div>
                        <?php endif ?>
                    </div>
                    <div class="bar" style="width: 8px; height: 6px">
                        <?php if ($this->my_rel == -2): ?>
                        <div class="bar_e" style="width: 100%"></div>
                        <?php endif ?>
                    </div>
                    <?php endif ?>
                </td>
                <td width="90%">
                    <div class="bar" style="height: 6px">
                        <div class="bar_a" style="width: <?= $this->bar['a'] ?>%"></div>
                    </div>
                    <div class="bar" style="height: 6px">
                        <div class="bar_b" style="width: <?= $this->bar['b'] ?>%"></div>
                    </div>
                    <div class="bar" style="height: 6px">
                        <div class="bar_c" style="width: <?= $this->bar['c'] ?>%"></div>
                    </div>
                    <div class="bar" style="height: 6px">
                        <div class="bar_d" style="width: <?= $this->bar['d'] ?>%"></div>
                    </div>
                    <div class="bar" style="height: 6px">
                        <div class="bar_e" style="width: <?= $this->bar['e'] ?>%"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <small>&#160;<a href="<?= Vars::$URI ?>?act=reputation&amp;user=<?= $this->user['id'] ?>"><?= lng('details') ?></a></small>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="list2">
    <div class="formblock">
        <label><?= lng('information') ?></label>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('property.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=info&amp;user=<?= $this->user['id'] ?>"><?= lng('personal_data') ?></a></li>
            <li><?= Functions::getIcon('block.png') ?>&#160;<a href="profile.php?act=ban&amp;user=<?= $this->user['id'] ?>"><?= lng('infringements') ?></a> (<?= $this->bancount ?>)</li>
            <li><?= Functions::getIcon('comments-edit.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=activity&amp;user=<?= $this->user['id'] ?>"><?= lng('activity') ?></a></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= lng('personal') ?></label>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('photo-album.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/album?act=list&amp;user=<?= $this->user['id'] ?>"><?= lng('photo_album') ?></a>&#160;(<?= $this->total_photo ?>)</li>
            <li><?= Functions::getIcon('comments.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=guestbook&amp;user=<?= $this->user['id'] ?>"><?= lng('guestbook') ?></a>&#160;(<?= $this->user['comm_count'] ?>)</li>
            <li><?= Functions::getIcon('friend.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/friends?user=<?= $this->user['id'] ?>"><?= lng('friends') ?></a>&#160;(<?= Functions::friendsCount($this->user['id']) ?>)</li>
        </ul>
    </div>
</div>
<?php if (Vars::$USER_ID && Vars::$USER_ID != $this->user['id']): ?>
<div class="menu">
    <div class="formblock">
        <label><?= lng('friendship') ?></label>
        <ul style="list-style: none; padding-left: 0">
            <?php if (empty($this->banned) && $this->friend == 2): ?>
            <li><?= Functions::getIcon('friend-ok.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/friends?act=ok&amp;id=<?= $this->user['id'] ?>"><?= lng('friends_demands_ok') ?></a></li>
            <li><?= Functions::getIcon('friend-cancel.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/friends?act=no&amp;id=<?= $this->user['id'] ?>"><?= lng('friends_demands_no') ?></a></li>
            <?php endif ?>
            <?php if (empty($this->banned) && $this->friend == 3): ?>
            <li><?= Functions::getIcon('friend-cancel.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/friends?act=cancel&amp;id=<?= $this->user['id'] ?>"><?= lng('friends_demands_cancel') ?></a></li>
            <?php endif ?>
            <?php if (empty($this->banned) && $this->friend == 0): ?>
            <li><?= Functions::getIcon('friend-add.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/friends?act=add&amp;id=<?= $this->user['id'] ?>"><?= lng('friends_add') ?></a></li>
            <?php endif ?>
            <?php if ($this->friend == 1): ?>
            <li><?= Functions::getIcon('friend-del.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/friends?act=delete&amp;id=<?= $this->user['id'] ?>"><?= lng('friends_delete') ?></a></li>
            <?php endif ?>
        </ul>
    </div>
    <div class="formblock">
        <label><?= lng('mail') ?></label>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('cards-address-block.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/contacts?act=select&amp;mod=banned&amp;id=<?= $this->user['id'] ?>"><?= ($this->banned == 1 ? lng('contact_delete_ignor') : lng('contact_add_ignor')) ?></a></li>
            <?php if (empty($this->banned)): ?>
            <li><?= Functions::getIcon($this->num_cont ? 'cards-address-del.png' : 'cards-address-add.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/contacts?act=select&amp;mod=contact&amp;id=<?= $this->user['id'] ?>"><?= ($this->num_cont ? lng('contact_delete') : lng('contact_add')) ?></a></li>
            <?php endif ?>
            <?php if (empty($this->banned)): ?>
            <li><?= Functions::getIcon('mail-edit.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/mail?act=messages&amp;id=<?= $this->user['id'] ?>"><?= lng('contact_write') ?></a></li>
            <?php endif ?>
        </ul>
    </div>
</div>
<?php endif ?>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/users"><?= lng('users') ?></a>
</div>