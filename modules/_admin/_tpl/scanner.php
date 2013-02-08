<!-- Заголовок раздела -->
<ul class="title admin">
    <li class="left"><a href="<?= Router::getUri(2) ?>"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('antispy') ?></h1></li>
    <li class="right"></li>
</ul>

<div class="content padding12">
    <?php if (isset($this->errormsg)): ?>
    <div class="alert alert-danger">
        <?= $this->errormsg ?>
    </div>
    <?php elseif (isset($this->ok)): ?>
    <div class="alert alert-success">
        <?= $this->ok ?>
    </div>
    <?php endif ?>

    <?= $this->form ?>

    <?php if (isset($this->files)): ?>
    <div class="alert alert-danger">
        <?= __('antispy_dist_bad_help') ?><br/>
        <?= __('total') ?>: <?= count($this->files) ?><br/><br/>
        <?php foreach ($this->files as $file): ?>
        <div style="font-size: small; font-weight: bold; padding-bottom: 4px">
            <?= htmlspecialchars($file['file_path']) ?>
            <div style="font-size: x-small; font-weight: normal; color: #696969">
                <?= __('date') . ': ' . $file['file_date'] ?><br/>
                <?= __('size') . ': ' . round($file['file_size'] / 1024, 2)?> kB
            </div>
        </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>
</div>