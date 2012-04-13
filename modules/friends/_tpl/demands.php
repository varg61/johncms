<div class="phdr">
    <strong>
        <?php echo lng('my_demand')?>
    </strong>
</div>
<div class="topmenu">
    <a href="<?php echo Vars::$MODULE_URI ?>?act=offers"><?php echo lng('my_offers') ?></a> <?php echo ($this->offers ? '(<span class="red">' . $this->offers . '</span>)' : '') ?>
</div>
<?php if($this->total):?>
    <?php foreach($this->query as $row): ?>
        <div class="<?php echo $row['list'] ?>">
        <?php echo $row['icon'] ?> <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo $row['id'] ?>"><?php echo $row['nickname'] ?></a>
        <div class="sub">
			<a href="<?php echo Vars::$MODULE_URI ?>?act=cancel&amp;id=<?php echo $row['id']?>"><?php echo lng('cancel') ?></a>
        </div>
        </div>
    <?php endforeach ?>
<div class="phdr">
    <?php echo lng('total') ?>: <?php echo $this->total ?>
</div>
<?php if($this->total > Vars::$USER_SET['page_size']):?>
<div class="topmenu">
    <?php echo $this->display_pagination ?>
</div>
<form action="" method="post">
    <p>
        <input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>"/>
        <input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;"/>
    </p>
</form>
<?php endif ?>
<?php else: ?>
    <div class="rmenu"><?php echo lng('demands_not') ?></div>
<?php endif ?>
<p><a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng('friends') ?></a></p>