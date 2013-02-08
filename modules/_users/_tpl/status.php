<!-- Заголовок раздела -->
<ul class="title <?= Users::$data['id'] == Vars::$USER_ID ? 'private' : 'admin' ?>">
    <li class="left"><a href="<?= Router::getUri(3) ?>option/"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('settings') ?></h1></li>
    <li class="separator"></li>
    <li class="right"><a href="#" class="slider-button"><span class="icn icn-info"></span></a></li>
</ul>

<div class="slider">
    <div class="info-block"><?= Functions::displayUser(Users::$data) ?></div>
</div>

<div class="content padding12">
    <?php if (isset($this->save)): ?>
    <div class="alert alert-success">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>
    <?= $this->form ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".slider-button").click(function () {
            $(".slider").slideToggle();
            $(this).toggleClass("close");
        });
    });
</script>