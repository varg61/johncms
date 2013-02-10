<!-- Заголовок раздела -->
<ul class="title admin">
    <li class="left"><a href="<?= Router::getUri(3) ?>option/"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('admin_panel') ?></h1></li>
    <li class="right"></li>
</ul>

<div class="info-block"><?= Functions::displayUser(Users::$data) ?></div>

<div class="content form-container">
    <?php if (isset($this->save)): ?>
    <div class="form-block confirm">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>
    <?= $this->form ?>
</div>