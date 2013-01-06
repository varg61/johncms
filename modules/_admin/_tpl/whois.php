<ul class="nav">
    <li><h1 class="section-warning">IP WHOIS</h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->errormsg)): ?>
    <div class="form-block error">
        <?= $this->errormsg ?>
    </div>
    <?php endif ?>
    <div class="form-block">
        <?= $this->form ?>
    </div>
    <?php if (isset($this->whois)): ?>
    <div class="form-block" style="font-size: small; word-wrap: break-word">
        <pre><?= $this->whois ?></pre>
    </div>
    <?php endif ?>
</div>