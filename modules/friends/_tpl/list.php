<div class="phdr">
    <strong>
        <?php echo lng('friends')?> <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo $this->id ?>"><?php echo $this->nickname ?></a>
    </strong>
</div>
<?php if($this->total): ?>
    <?php foreach($this->query as $row): ?>
    <div class="<?php echo $row['list'] ?>">
        <?php if($row['id'] != Vars::$USER_ID): ?>
			<?php echo $row['icon'] ?> <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo $row['id'] ?>"><?php echo $row['nickname'] . $row['online'] ?></a>
		<?php else: ?>
			<?php echo $row['icon'] ?> <strong><?php echo $row['nickname'] ?></strong><?php echo $row['online'] ?>
		<?php endif ?>
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