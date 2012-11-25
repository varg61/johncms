<?php if(!isset(Vars::$ACL['stat']) || !Vars::$ACL['stat']): ?>
<div class="rmenu"><b><?=  __('module_is_disabled') ?></b></div>
<? endif ?>
<ul class="nav">
    <li><h1><?= __('statistics') ?></h1></li>
    <li>
        <div class="form-container">
            <div class="form-block">
                <ul>
                    <li><?= __('hits_today')?>: <?= statistic::$hity ?></li>
                    <li><?= __('hosts_today')?>: <?= statistic::$hosty ?></li>
                    <li><?= __('hits_on_robots')?>: <?= $this->count_stat[0] ?></li>
                    <li><?= __('hits_without_robots')?>: <?= $this->hitnorobot ?></li>
                    <?php if($this->max_host): ?>
                    <li><?= __('host_record')?> (<b><?= $this->maxhost['host'] ?></b>) <?= __('was')?> <b><?= statistic::month($this->max_host_time) . __('year')?></b></li>
                    <li><?= __('hits_record')?> (<b><?=  $this->maxhits['hits'] ?></b>) <?= __('was')?> <b><?= statistic::month($this->max_hits_time) . __('year')?></b></li>
                    <? endif ?>
                    <li><?= __('hits_per_visitor')?>: <?= statistic::$hosty > 0 ? round($this->hitnorobot / statistic::$hosty) : __('no_data')?></li>
                </ul>
            </div>
        </div>
    </li>
    <li><h2><?= __('detailed_statistics')?></h2></li>
    <li><a href="?act=hosts"><?= __('hosts')?><i class="icn-arrow"></i><span class="badge badge-right"><?= statistic::$hosty?></span></a></li>
    <li><a href="?act=opsos"><?= __('operators')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[4] ?></span></a></li>

    <li><a href="?act=country"><?= __('countries')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[3] ?></span></a></li>
    <li><a href="?act=robots"><?= __('robots')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[5] ?></span></a></li>
    <li><a href="?act=users"><i class="icn-man-woman"></i><?= __('users')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[6] ?></span></a></li>
    <li><a href="?act=stat_search"><?= __('from_search_engines')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[2] ?> | <?= round($this->searchpercent, 2) ?>%</span></a></li>
    <li><a href="?act=referer"><?= __('whence_come')?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->count_stat[7] ?></span></a></li>
    <li><a href="?act=phones"><?= __('phones_browsers')?><i class="icn-arrow"></i></a></li>
    <li><a href="?act=os"><?= __('operating_systems')?><i class="icn-arrow"></i></a></li>
    <li><a href="?act=point_in"><?= __('entry_points')?><i class="icn-arrow"></i></a></li>
    <li><a href="?act=pop"><?= __('popular_searches')?><i class="icn-arrow"></i></span></a></li>
    <li><a href="?act=allstat"><i class="icn-piechart"></i><?= __('daily_statistics')?><i class="icn-arrow"></i></a></li>
    <li><a href="http://www.cy-pr.com/analysis/<?= $this->my_url['host'] ?>">SEO <?= __('site_analysis')?><i class="icn-arrow"></i></a></li>
</ul>
<?php if (Vars::$USER_RIGHTS >= 9): ?>
<div class="bmenu"><a href="?act=ip_base&amp;action=base"><?= __('database_management_ip')?></a></div>
<? endif ?>