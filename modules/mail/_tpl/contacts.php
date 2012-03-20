<?php foreach($this->query as $row): ?>
<div class="<?php echo $row['list'] ?>">
 <input type="checkbox" name="delch[]" value="<?php echo $row['id'] ?>"/> <?php echo $row['icon'] ?>
  <a href="<?php echo $row['url'] ?>"><?php echo $row['nickname'] ?></a> <?php echo $row['online'] ?> (<?php echo $row['count_in'] ?>&#160;/&#160;<?php echo $row['count_out'] ?>) <span class="red"><?php echo $row['count_new'] ?></span>
</div>
<? endforeach ?>
