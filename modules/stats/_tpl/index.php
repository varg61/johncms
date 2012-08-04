<?php if(!Vars::$SYSTEM_SET['stat']): ?>
<div class="rmenu"><b><?=  lng('module_is_disabled') ?></b></div>
<? endif ?>
<div class="phdr">
    <b><?= lng('statistics') ?></b>
</div>

<div class="gmenu"><h3><?= Functions::getIcon('statistic.png')?> <?= lng('general_information')?></h3>
<li><?= lng('hits_today')?>: <?= statistic::$hity ?></li>
<li><?= lng('hosts_today')?>: <?= statistic::$hosty ?></li>
<li><?= lng('hits_on_robots')?>: <?= $this->count_stat[0] ?></li>
<li><?= lng('hits_without_robots')?>: <?= $this->hitnorobot ?></li>

<?php if($this->max_host): ?>

<li><?= lng('host_record')?> (<b><?= $this->maxhost['host'] ?></b>) <?= lng('was')?> <b><?= statistic::month($this->max_host_time) . lng('year')?></b></li>
<li><?= lng('hits_record')?> (<b><?=  $this->maxhits['hits'] ?></b>) <?= lng('was')?> <b><?= statistic::month($this->max_hits_time) . lng('year')?></b></li>

<? endif ?>

<li><?= lng('hits_per_visitor')?>: 
<?= statistic::$hosty > 0 ? round($this->hitnorobot / statistic::$hosty) : lng('no_data')?></li></div>

<div class="menu"><h3><?= Functions::getIcon('stats.png')?> <?= lng('detailed_statistics')?></h3>

<li><a href="?act=hosts"><?= lng('hosts')?></a> (<?= statistic::$hosty?>)</li>
<li><a href="?act=opsos"><?= lng('operators')?></a> (<?= $this->count_stat[4] ?>)</li>
<li><a href="?act=country"><?= lng('countries')?></a> (<?= $this->count_stat[3] ?>)</li>
<li><a href="?act=robots"><?= lng('robots')?></a> (<?= $this->count_stat[5] ?>)</li>
<li><a href="?act=users"><?= lng('users')?></a> (<?= $this->count_stat[6] ?>)</li>
<li><a href="?act=stat_search"><?= lng('from_search_engines')?></a> (<?= $this->count_stat[2] ?> | <?= round($this->searchpercent, 2) ?>%)</li>
<li><a href="?act=phones"><?= lng('phones_browsers')?></a></li>
<li><a href="?act=os"><?= lng('operating_systems')?></a></li>
<li><a href="?act=referer"><?= lng('whence_come')?></a> (<?= $this->count_stat[7] ?>)</li>
<li><a href="?act=point_in"><?= lng('entry_points')?></a></li>
<li><a href="?act=pop"><?= lng('popular_searches')?></a> (<?=  $this->count_stat[1] ?>)</li>
<li><a href="http://www.cy-pr.com/analysis/<?= $this->my_url['host'] ?>">SEO <?= lng('site_analysis')?></a></li>
<li><a href="?act=allstat"><?= lng('daily_statistics')?></a></li></div>

<?php if (Vars::$USER_RIGHTS >= 9): ?>
<div class="bmenu"><a href="?act=ip_base&amp;action=base"><?= lng('database_management_ip')?></a></div>
<? endif ?>
