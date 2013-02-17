<!-- Заголовок раздела -->
<ul class="title admin">
    <li class="left"><a href="<?= Vars::$HOME_URL ?>/admin"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('downloads') ?></h1></li>
    <li class="right"></li>
</ul>
<div class="content form-container">
    <?php if (isset($this->save)) : ?>
    <div class="form-block confirm">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>
    <div class="form-block ">
        <?= $this->form ?>
    </div>
</div>