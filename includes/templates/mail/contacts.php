<? foreach($this->query as $row): ?>
<div class="<?=$row['list']?>">
  <input type="checkbox" name="delch[]" value="<?= $row['id'] ?>"/> <?= $row['icon'] ?>
    <a href="<?= $row['url'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['count_in'] ?>&#160;/&#160;<?= $row['count_out'] ?>) <span class="red"><?= $row['count_new'] ?></span>
</div>
<? endforeach ?>
