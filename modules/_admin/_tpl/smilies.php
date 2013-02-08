<!-- Заголовок раздела -->
<ul class="title admin">
    <li class="left"><a href="<?= Router::getUri(2) ?>"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('smilies') ?></h1></li>
    <li class="right"></li>
</ul>

<div class="content padding12">
    <?php if (isset($this->error)): ?>
    <div class="alert alert-danger">
        <?= $this->error ?>
    </div>
    <?php elseif (isset($this->save)): ?>
    <div class="alert alert-success">
        <?= $this->save ?>
    </div>
    <?php endif ?>
    <?= $this->form ?>
</div>