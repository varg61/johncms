<ul class="nav">
    <li><h1 class="section-warning"><?= __('firewall') ?></h1></li>
</ul>
<div class="toolbar-top">
    <a class="btn<?= !Vars::$MOD ? '' : ' btn-primary' ?> btn-mini" href="<?= Vars::$URI ?>?act=firewall"><?= __('settings') ?></a>
    <a class="btn<?= Vars::$MOD == 'black' ? '' : ' btn-primary' ?> btn-mini" href="<?= Vars::$URI ?>?act=firewall&amp;mod=black"><?= __('black_list') ?></a>
    <a class="btn<?= Vars::$MOD == 'white' ? '' : ' btn-primary' ?> btn-mini" href="<?= Vars::$URI ?>?act=firewall&amp;mod=white"><?= __('white_list') ?></a>
</div>