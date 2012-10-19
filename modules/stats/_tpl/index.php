<?php if(!isset(Vars::$ACL['stat']) || !Vars::$ACL['stat']): ?>
<div class="rmenu"><b><?=  lng('module_is_disabled') ?></b></div>
<? endif ?>
<ul class="nav">
    <li><h1><?= lng('statistics') ?></h1></li>
    <li>
        <div class="form-container">
            <div class="form-block">
                <ul>
                    <li><?= lng('hits_today')?>: <?= statistic::$hity ?></li>
                    <li><?= lng('hosts_today')?>: <?= statistic::$hosty ?></li>
                    <li><?= lng('hits_on_robots')?>: <?= $this->count_stat[0] ?></li>
                    <li><?= lng('hits_without_robots')?>: <?= $this->hitnorobot ?></li>
                    <?php if($this->max_host): ?>
                    <li><?= lng('host_record')?> (<b><?= $this->maxhost['host'] ?></b>) <?= lng('was')?> <b><?= statistic::month($this->max_host_time) . lng('year')?></b></li>
                    <li><?= lng('hits_record')?> (<b><?=  $this->maxhits['hits'] ?></b>) <?= lng('was')?> <b><?= statistic::month($this->max_hits_time) . lng('year')?></b></li>
                    <? endif ?>
                    <li><?= lng('hits_per_visitor')?>: <?= statistic::$hosty > 0 ? round($this->hitnorobot / statistic::$hosty) : lng('no_data')?></li>
                </ul>
            </div>
        </div>
    </li>
    <li><h2><?= lng('detailed_statistics')?></h2></li>
    <li><a href="?act=hosts"><?= lng('hosts')?><i class="icn-arrow"></i><span class="badge badge-right"><?= statistic::$hosty?></span></a></li>
    <li><a href="?act=opsos"><?= lng('operators')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[4] ?></span></a></li>

    <li><a href="?act=country"><?= lng('countries')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[3] ?></span></a></li>
    <li><a href="?act=robots"><?= lng('robots')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[5] ?></span></a></li>
    <li><a href="?act=users"><i class="icn-man-woman"></i><?= lng('users')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[6] ?></span></a></li>
    <li><a href="?act=stat_search"><?= lng('from_search_engines')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[2] ?> | <?= round($this->searchpercent, 2) ?>%</span></a></li>
    <li><a href="?act=referer"><?= lng('whence_come')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[7] ?></span></a></li>
    <li><a href="?act=phones"><?= lng('phones_browsers')?><i class="icn-arrow"></i></a></li>
    <li><a href="?act=os"><?= lng('operating_systems')?><i class="icn-arrow"></i></a></li>
    <li><a href="?act=point_in"><?= lng('entry_points')?><i class="icn-arrow"></i></a></li>
    <li><a href="?act=pop"><?= lng('popular_searches')?><i class="icn-arrow"></i></span></a></li>
    <li><a href="?act=allstat"><i class="icn-piechart"></i><?= lng('daily_statistics')?><i class="icn-arrow"></i></a></li>
    <li><a href="http://www.cy-pr.com/analysis/<?= $this->my_url['host'] ?>">SEO <?= lng('site_analysis')?><i class="icn-arrow"></i></a></li>
</ul>
<?php if (Vars::$USER_RIGHTS >= 9): ?>
<div class="bmenu"><a href="?act=ip_base&amp;action=base"><?= lng('database_management_ip')?></a></div>
<? endif ?>