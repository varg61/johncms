<div class="phdr">
    <strong>
        <?php echo lng('friends')?>
    </strong>
</div>
<div class="topmenu">
    <a href="<?php Vars::$MODULE_URI ?>?act=demands"><?php echo lng('my_demand') ?></a> <?php echo ($this->demands ? '(<span class="red">' . $this->demands . '</span>)' : '') ?>| <a href="<?php Vars::$MODULE_URI ?>?act=offers"><?php echo lng('my_offers') ?></a> <?php echo ($this->offers ? '(<span class="red">' . $this->offers . '</span>)' : '') ?><?php echo ($this->total ? ' | <a href="' . Vars::$MODULE_URI . '?act=online">' . lng('online') . '</a>' : '')?>
</div>

<?php if($this->total):?>
    <?php foreach($this->query as $row): ?>
        <div class="<?php echo $row['list'] ?>">
        <?php echo $row['icon'] ?> <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo $row['id'] ?>"><?php echo $row['nickname'] ?></a><?php echo $row['online'] ?>
        <div class="sub">
            <a href="<?php echo Vars::$MODULE_URI ?>?act=delete&amp;id=<?php echo $row['id']?>"><?php echo lng('delete') ?></a>
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
    <div class="rmenu"><?php echo lng('friends_not') ?></div>
<?php endif ?>