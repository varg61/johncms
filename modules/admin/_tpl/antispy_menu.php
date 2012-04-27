<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b><?= lng('admin_panel') ?></b></a> | <?= lng('antispy') ?>
</div>
<div class="menu">
    <div class="formblock">
        <label><?= lng('antispy_scan_mode') ?></label>
        <ul>
            <li>
                <a href="<?= Vars::$URI ?>?act=scan"><?= lng('antispy_dist_scan') ?></a><br/>
                <small><?= lng('antispy_dist_scan_help') ?></small>
            </li>
            <li>
                <a href="<?= Vars::$URI ?>?act=snapscan"><?= lng('antispy_snapshot_scan') ?></a><br/>
                <small><?= lng('antispy_snapshot_scan_help') ?></small>
            </li>
            <li>
                <a href="<?= Vars::$URI ?>?act=snap"><?= lng('antispy_snapshot_create') ?></a><br/>
                <small><?= lng('antispy_snapshot_create_help') ?></small>
            </li>
        </ul>
    </div>
</div>
<div class="phdr"><a href="<?= Vars::$MODULE_URI ?>"><?= lng('admin_panel') ?></a></div>