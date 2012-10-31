<ul class="nav">
    <li><h1><?= lng('mail') ?></h1></li>
</ul>
<div class="toolbar-top">
	<a class="btn btn-primary btn-mini" href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</div>
<ul class="nav">
	<li><a href="<?= Vars::$MODULE_URI ?>?act=inmess"><?= lng('inmess') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter( 'inmess' ) ?><?= $this->newmess ? '+' . $this->newmess : '' ?></span></a></li>
	<li><a href="<?= Vars::$MODULE_URI ?>?act=outmess"><?= lng('outmess') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter( 'outmess' ) ?></span></a></li>
	<li><a href="<?= Vars::$MODULE_URI ?>?act=elected"><?= lng('elected') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter( 'elected' ) ?></span></a></li>
	<li><a href="<?= Vars::$MODULE_URI ?>?act=files"><i class="icn-upload"></i><?= lng('files') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter( 'files' ) ?></span></a></li>
	<li><a href="<?= Vars::$MODULE_URI ?>?act=basket"><i class="icn-trash"></i><?= lng('basket') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter( 'delete' ) ?></span></a></li>
	<li><a href="<?php Vars::$MODULE_URI ?>?act=settings"><i class="icn-settings-red"></i><?= lng('settings') ?><i class="icn-arrow right"></i></a></li>
</ul>
<div class="btn-panel">
    <a class="btn" href="<?= Vars::$MODULE_URI ?>?act=add"><i class="icn-edit"></i><?= lng('write_message') ?></a>
</div>