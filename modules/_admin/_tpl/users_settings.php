<!-- Заголовок раздела -->
<ul class="title admin">
    <li class="left"><a href="<?= Router::getUri(2) ?>"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('community') ?></h1></li>
    <li class="right"></li>
</ul>

<div class="content padding12">
    <?php if (isset($this->save)) : ?>
    <div class="alert alert-success">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>
    <?php if (isset($_GET['default'])) : ?>
    <div class="alert">
        <?= __('settings_default') ?>
    </div>
    <?php endif ?>
    <?= $this->form ?>
</div>