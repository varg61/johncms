<?php if (isset($this->list)): ?>
    <?php foreach ($this->list as $key => $val): ?>
        <div class="block-<?= $key % 2 ? 'odd' : 'even' ?>">
            <?= $val['text'] ?>
        </div>
    <?php endforeach ?>
<?php else: ?>
    <div>Список пустой</div>
<?php endif ?>