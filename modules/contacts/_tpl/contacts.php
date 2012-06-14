<?php foreach ($this->query as $row): ?>
<div class="<?= $row['list'] ?>">
    <input type="checkbox" name="delch[]" value="<?= $row['id'] ?>"/> <?= $row['icon'] ?>
    <a href="<?= $row['url'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?><?php if (Vars::$ACT != 'new'): ?> (<?= $row['count'] ?>)<?php endif ?> <span class="red"><?= $row['count_new'] ?></span>
</div>
<? endforeach ?>
