<p><a href="<?= Vars::$MODULE_URI ?>?act=add"><?= lng('write_message') ?></a></p>
<div class="phdr"><strong><?= lng('mail') ?></strong></div>
<div class="topmenu"><a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a></div>
<div class="list1">
    <p>
        <?= Functions::getImage('mail-inbox.png') ?>&#160;<a href="<?= Vars::$MODULE_URI ?>?act=inmess"><?= lng('inmess') ?></a>&#160;(<?= $this->inmess ?>) <span class="red"><?= $this->newmess ?></span><br/>
        <?= Functions::getImage('mail-outbox.png') ?>&#160;<a href="<?= Vars::$MODULE_URI ?>?act=outmess"><?= lng('outmess') ?></a>&#160;(<?= $this->outmess ?>)<br/>
    </p>
</div>
<div class="gmenu"><?= lng('can_write') ?>:&#160;<a href="<?php Vars::$MODULE_URI ?>?act=settings"><?= $this->receive_mail ?></a></div>
<div class="list2">
    <p>
        <?= Functions::getImage('mail-elected.png') ?>&#160;<a href="<?= Vars::$MODULE_URI ?>?act=elected"><?= lng('elected') ?></a>&#160;(<?= $this->elected ?>)<br/>
		<?= Functions::getImage('mail-files.png') ?>&#160;<a href="<?= Vars::$MODULE_URI ?>?act=files"><?= lng('files') ?></a>&#160;(<?= $this->files ?>)<br/>
        <?= Functions::getImage('mail-trash.png') ?>&#160;<a href="<?= Vars::$MODULE_URI ?>?act=basket"><?= lng('basket') ?></a>&#160;(<?= $this->delete ?>)<br/>
        <?php if (Vars::$USER_RIGHTS == 9): ?>
        <?= Functions::getImage('mail-sending-out.png') ?>&#160;<a href="<?= Vars::$MODULE_URI ?>?act=sending_out"><?= lng('sending_out') ?></a><br/>
        <?php endif ?>
    </p>
</div>