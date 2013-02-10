<!-- Заголовок раздела -->
<ul class="title admin">
    <li class="left"><a href="<?= Router::getUri(2) ?>"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1>IP WHOIS</h1></li>
    <li class="right"></li>
</ul>

<div class="content form-container">
    <?php if (isset($this->errormsg)): ?>
    <div class="alert alert-danger">
        <?= $this->errormsg ?>
    </div>
    <?php endif ?>
    <?= $this->form ?>
    <?php if (isset($this->whois)): ?>
    <div class="alert alert-info" style="font-size: x-small; word-wrap: break-word">
        <pre><?= $this->whois ?></pre>
    </div>
    <?php endif ?>
</div>