<div class="phdr"><a href="index.php"><b><?= Vars::$LNG['community'] ?></b></a> | <?= Vars::$LNG['users_list'] ?></div>
<div class="topmenu"><?= Functions::displayMenu($menu) ?></div>
<?php if ($total > Vars::$USER_SET['page_size']) { ?>
<div class="topmenu"><?= Functions::displayPagination('userlist.php?', Vars::$START, $total, Vars::$USER_SET['page_size']) ?></div>
<?php }
if ($total) { ?>
<?php } ?>