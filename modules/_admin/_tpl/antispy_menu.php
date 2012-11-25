<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b><?= __('admin_panel') ?></b></a> | <?= __('antispy') ?>
</div>
<div class="menu">
    <div class="formblock">
        <label><?= __('antispy_scan_mode') ?></label>
        <ul>
            <li>
                <a href="<?= Vars::$URI ?>?act=scan"><?= __('antispy_dist_scan') ?></a><br/>
                <small><?= __('antispy_dist_scan_help') ?></small>
            </li>
            <li>
                <a href="<?= Vars::$URI ?>?act=snapscan"><?= __('antispy_snapshot_scan') ?></a><br/>
                <small><?= __('antispy_snapshot_scan_help') ?></small>
            </li>
            <li>
                <a href="<?= Vars::$URI ?>?act=snap"><?= __('antispy_snapshot_create') ?></a><br/>
                <small><?= __('antispy_snapshot_create_help') ?></small>
            </li>
        </ul>
    </div>
</div>
<div class="phdr"><a href="<?= Vars::$MODULE_URI ?>"><?= __('admin_panel') ?></a></div>