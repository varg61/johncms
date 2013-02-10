<!-- Заголовок раздела -->
<ul class="title <?= Users::$data['id'] == Vars::$USER_ID ? 'private' : 'admin' ?>">
    <li class="left"><a href="<?= Router::getUri(3) ?>option/"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('settings') ?></h1></li>
    <li class="right"></li>
</ul>

<div class="info-block"><?= Functions::displayUser(Users::$data) ?></div>

<div class="content form-container">
    <?php if (isset($this->save)): ?>
    <div class="alert alert-success">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>
    <?= $this->form ?>
</div>