<div class="phdr"><b><?= Vars::$LNG['community'] ?></b></div>
<div class="gmenu">
    <form action="<?= Vars::$URI ?>/search" method="post">
        <p>
        <h3><?= Vars::$LNG['search'] ?></h3>
        <input type="text" name="search"/>
        <input type="submit" value="<?= Vars::$LNG['search'] ?>" name="submit"/><br/>
        <small><?= Vars::$LNG['search_nick_help'] ?></small>
        </p>
    </form>
</div>
<div class="menu">
    <p>
    <h3><?= Functions::getImage('users.png') . '&#160;' . Vars::$LNG['community'] ?></h3>
    <ul>
        <li><a href="<?= Vars::$URI ?>/list"><?= Vars::$LNG['users'] ?></a> (<?= $this->count->users ?>)</li>
        <li><a href="<?= Vars::$URI ?>/list?act=adm"><?= Vars::$LNG['administration'] ?></a></li>
    </ul>
    </p>
    <p>
    <h3><?= Functions::getImage('rating.png') . '&#160;' . Vars::$LNG['users_top'] ?></h3>
    <ul>
        <li><a href="<?= Vars::$URI ?>/top"><?= Vars::$LNG['forum'] ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=comm"><?= Vars::$LNG['comments'] ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=karma"><?= Vars::$LNG['karma'] ?></a></li>
    </ul>
    </p>
</div>
<div class="phdr"><a href="index.php"><?= Vars::$LNG['back'] ?></a></div>