<div class="phdr">
    <b><?= lng('daily_statistics') ?></b>
</div>
<?php if($this->count_stat > 0): ?>
<div class="gmenu">
<h3><?= lng('stats_for')?> <?= date("d.m.y", $this->start_time_stat)?></h3>
<li><?= lng('hosts')?>: <?= $this->day_array['host']?></li>
<li><?= lng('hits')?>: <?= $this->day_array['hits']?></li>
<h3><?= lng('from_search_engines1')?>: <?= array_sum(array_slice($this->day_array, 3))?></h3>

<?php if($this->day_array['yandex'] > 0): ?>
    <?= Functions::loadModuleImage('yandex.png') ?> Yandex.ru (<?= $this->day_array['yandex']?>)<br/>
    <? endif ?>
<?php if($this->day_array['rambler'] > 0): ?>
    <?= Functions::loadModuleImage('rambler.png') ?> Rambler.ru (<?= $this->day_array['rambler']?>)<br/>
    <? endif ?>
<?php if($this->day_array['google'] > 0): ?>
    <?= Functions::loadModuleImage('google.png') ?> Google.ru (<?= $this->day_array['google']?>)<br/>
    <? endif ?>
<?php if($this->day_array['mail'] > 0): ?>
    <?= Functions::loadModuleImage('mailru.png') ?> Mail.ru (<?= $this->day_array['mail']?>)<br/>
    <? endif ?>
<?php if($this->day_array['gogo'] > 0): ?>
    <?= Functions::loadModuleImage('gogo.png') ?> Gogo.ru (<?= $this->day_array['gogo']?>)<br/>
    <? endif ?>
<?php if($this->day_array['yahoo'] > 0): ?>
    <?= Functions::loadModuleImage('yahoo.png') ?> Yahoo.com (<?= $this->day_array['yahoo']?>)<br/>
    <? endif ?>
<?php if($this->day_array['bing'] > 0): ?>
    <?= Functions::loadModuleImage('bing.png') ?> Bing.com (<?= $this->day_array['bing']?>)<br/>
    <? endif ?>
<?php if($this->day_array['nigma'] > 0): ?>
    <?= Functions::loadModuleImage('nigma.png') ?> Nigma.ru (<?= $this->day_array['nigma']?>)<br/>
    <? endif ?>
<?php if($this->day_array['qip'] > 0): ?>
    <?= Functions::loadModuleImage('qip.png') ?> Search.QIP.ru (<?= $this->day_array['qip']?>)<br/>
    <? endif ?>
<?php if($this->day_array['aport'] > 0): ?>
    <?= Functions::loadModuleImage('aport.png') ?> APORT.ru (<?= $this->day_array['aport']?>)<br/>
    <? endif ?>
</div>

<div class="menu">
<h3><?= lng('summary_statistics')?></h3>
<li><?= lng('total_hosts')?>:<b> <?= $this->searchc[1]?></b></li>
<li><?= lng('total_hits')?>: <b><?= $this->searchc[0]?></b></li>
<h3><?= Functions::loadModuleImage('search_network.png')?> <?= lng('from_search_engines1')?>: <?= array_sum(array_slice($this->searchc, 2))?></h3>
<?= Functions::loadModuleImage('yandex.png') ?>&nbsp;Yandex.ru: <b><?= $this->searchc[2]?></b><br />
<?= Functions::loadModuleImage('rambler.png') ?>&nbsp;Rambler.ru: <b><?= $this->searchc[3]?></b><br />
<?= Functions::loadModuleImage('google.png') ?>&nbsp;Google.ru: <b><?= $this->searchc[4]?></b><br />
<?= Functions::loadModuleImage('mailru.png') ?>&nbsp;Mail.ru: <b><?= $this->searchc[5]?></b><br />
<?= Functions::loadModuleImage('gogo.png') ?>&nbsp;Gogo.ru: <b><?= $this->searchc[6]?></b><br />
<?= Functions::loadModuleImage('yahoo.png') ?>&nbsp;Yahoo.com: <b><?= $this->searchc[7]?></b><br />
<?= Functions::loadModuleImage('bing.png') ?>&nbsp;Bing.com: <b><?= $this->searchc[8]?></b><br />
<?= Functions::loadModuleImage('nigma.png') ?>&nbsp;Nigma.ru: <b><?= $this->searchc[9]?></b><br />
<?= Functions::loadModuleImage('qip.png') ?>&nbsp;Search.QIP.ru: <b><?= $this->searchc[10]?></b><br />
<?= Functions::loadModuleImage('aport.png') ?>&nbsp;APORT.ru: <b><?= $this->searchc[11]?></b>
</div>

<?php if($this->grafics): ?>
<div class="bmenu"><h3><?= lng('grafics')?></h3>
<img src="<?= Vars::$HOME_URL ?>/files/temp/stat_we_host.png" alt="loading..."/><br/>
<h4><?= lng('search_traffic')?></h4>
<img src="<?= Vars::$HOME_URL ?>/files/temp/stat_we_se.png" alt="loading..."/>
</div>
    <? endif ?>
    
<? else: ?>
<div class="rmenu">
<?= lng('no_data_for_this_day')?>
</div>
<? endif ?>

<div class="phdr">
<a href="?act=allstat&amp;days=<?= $this->days ?>"><?= lng('view_of')?> <?= date("d.m.y", time() - $this->days * 24 * 3600)?></a>
</div>
<div class="gmenu">
<a href="<?= Vars::$URI ?>"><?= lng('to_statistics') ?></a>
</div>