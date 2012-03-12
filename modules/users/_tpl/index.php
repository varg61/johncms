<div class="phdr">
    <b><?= lng('community') ?></b>
</div>
<div class="gmenu">
    <form action="<?= Vars::$URI ?>/search" method="post">
        <p>
        <h3><?= lng('search') ?></h3>
        <input type="text" name="search"/>
        <input type="submit" value="<?= lng('search') ?>" name="submit"/><br/>
        <small><?= lng('search_nick_help') ?></small>
        </p>
    </form>
</div>
<div class="menu">
    <p>
    <h3><?= Functions::getImage('users.png') . '&#160;' . lng('community') ?></h3>
    <ul>
        <li><a href="<?= Vars::$URI ?>/list"><?= lng('users') ?></a> (<?= $this->count->users ?>)</li>
        <li><a href="<?= Vars::$URI ?>/list?act=adm"><?= lng('administration') ?></a></li>
    </ul>
    </p>
    <p>
    <h3><?= Functions::getImage('rating.png') . '&#160;' . lng('users_top') ?></h3>
    <ul>
        <li><a href="<?= Vars::$URI ?>/top"><?= lng('forum') ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=comm"><?= lng('comments') ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=karma"><?= lng('karma') ?></a></li>
    </ul>
    </p>
</div>
<div class="phdr">
    <a href="index.php"><?= lng('back') ?></a>
</div>