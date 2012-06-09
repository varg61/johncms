<div class="phdr">
    <b><?= lng('community') ?></b>
</div>
<div class="topmenu">
    <form action="<?= Vars::$URI ?>/search" method="post">
        <p>
            <input type="text" name="search"/>
            <input type="submit" value="<?= lng('search') ?>" name="submit"/><br/>
        </p>
    </form>
</div>
<div class="menu">
    <div class="formblock">
        <label><?= lng('community') ?></label><br/>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('users.png') ?>&#160;<a href="<?= Vars::$URI ?>/search"><?= lng('users') ?></a> (<?= $this->count->users ?>)</li>
            <li><?= Functions::getIcon('user-boss.png') ?>&#160;<a href="<?= Vars::$URI ?>/search?act=adm"><?= lng('administration') ?></a></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= lng('rating') ?></label><br/>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top"><?= lng('forum') ?></a></li>
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top?act=comm"><?= lng('comments') ?></a></li>
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top?act=karma"><?= lng('karma') ?></a></li>
        </ul>
    </div>
</div>
<div class="phdr">
    <a href="index.php"><?= lng('back') ?></a>
</div>