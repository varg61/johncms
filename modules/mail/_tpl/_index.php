<ul class="nav">
    <li><h1 class="section-personal"><?= __('mail') ?></h1></li>
</ul>
<div class="toolbar-top">
    <a class="btn btn-primary btn-mini" href="<?= Vars::$HOME_URL ?>contacts/"><?= __('contacts') ?></a>
</div>
<ul class="nav">
    <li><a href="<?= $this->link ?>?act=inmess"><i class="icn-inbox"></i><?= __('inmess') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter('inmess') ?><?= $this->newmess ? '+' . $this->newmess : '' ?></span></a></li>
    <li><a href="<?= $this->link ?>?act=outmess"><i class="icn-outbox"></i><?= __('outmess') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter('outmess') ?></span></a></li>
    <li><a href="<?= $this->link ?>?act=elected"><i class="icn-favmail"></i><?= __('elected') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter('elected') ?></span></a></li>
    <li><a href="<?= $this->link ?>?act=files"><i class="icn-attach"></i><?= __('files') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter('files') ?></span></a></li>
    <li><a href="<?= $this->link ?>?act=basket"><i class="icn-trash"></i><?= __('basket') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Mail::counter('delete') ?></span></a></li>
    <li><a href="<?php $this->link ?>?act=settings"><i class="icn-settings"></i><?= __('settings') ?><i class="icn-arrow right"></i></a></li>
</ul>
<div class="btn-panel">
    <a class="btn" href="<?= $this->link ?>?act=add"><i class="icn-edit"></i><?= __('write_message') ?></a>
</div>