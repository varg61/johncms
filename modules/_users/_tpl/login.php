<ul class="title<?= Vars::$USER_ID ? ' private' : '' ?>">
    <li class="center"><h1><?= (Vars::$USER_ID ? __('exit') : __('login')) ?></h1></li>
</ul>
<div class="content form-container">
    <div style="text-align: center">
        <div style="max-width: 260px; margin: 0 auto"><?= $this->form ?></div>
    </div>
</div>
