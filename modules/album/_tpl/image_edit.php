<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=show&amp;al=<?= $this->album ?>&amp;user=<?= $this->user['id'] ?>"><b><?= lng('photo_album') ?></b></a> | <?= lng('image_edit') ?>
</div>
<?php if (isset($this->save)) : ?>
    <div class="gmenu">
        <p><?= lng('data_saved') ?></p>
    </div>
<?php endif ?>
<form action="<?= Vars::$URI ?>?act=image_edit&amp;img=<?= $this->img ?>&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label><?= lng('image') ?></label><br/>
            <img src="<?= Vars::$HOME_URL ?>/files/users/album/<?= $this->user['id'] ?>/<?= $this->tmb_name ?>"/>
        </div>
        <div class="formblock">
            <label><?= lng('description') ?></label><br/>
            <textarea name="description" rows="<?= Vars::$USER_SET['field_h'] ?>"><?= Validate::filterString($this->description) ?></textarea><br/>
            <div class="desc"><?= lng('not_mandatory_field') ?>, max. 500</div>
        </div>
    </div>
    <div class="gmenu">
        <div class="formblock">
            <label><?= lng('brightness') ?></label><br/>
            <table border="0" cellspacing="0" cellpadding="0" style="text-align:center">
                <tr>
                    <td><input type="radio" name="brightness" value="1"/></td>
                    <td><input type="radio" name="brightness" value="2"/></td>
                    <td><input type="radio" name="brightness" value="0" checked="checked"/></td>
                    <td><input type="radio" name="brightness" value="3"/></td>
                    <td><input type="radio" name="brightness" value="4"/></td>
                </tr>
                <tr>
                    <td>-2</td>
                    <td>-1</td>
                    <td>0</td>
                    <td>+1</td>
                    <td>+2</td>
                </tr>
            </table>
        </div>
        <div class="formblock">
            <label><?= lng('contrast') ?></label><br/>
            <table border="0" cellspacing="0" cellpadding="0" style="text-align:center">
                <tr>
                    <td><input type="radio" name="contrast" value="1"/></td>
                    <td><input type="radio" name="contrast" value="2"/></td>
                    <td><input type="radio" name="contrast" value="0" checked="checked"/></td>
                    <td><input type="radio" name="contrast" value="3"/></td>
                    <td><input type="radio" name="contrast" value="4"/></td>
                </tr>
                <tr>
                    <td>-2</td>
                    <td>-1</td>
                    <td>0</td>
                    <td>+1</td>
                    <td>+2</td>
                </tr>
            </table>
        </div>
        <div class="formblock">
            <label><?= lng('image_rotate') ?></label><br/>
            <input type="radio" name="rotate" value="0" checked="checked"/>&#160;<?= lng('image_rotate_not') ?><br/>
            <input type="radio" name="rotate" value="2"/>&#160;<?= lng('image_rotate_right') ?><br/>
            <input type="radio" name="rotate" value="1"/>&#160;<?= lng('image_rotate_left') ?></p>
        </div>
        <div class="formblock">
            <small><?= lng('image_edit_warning') ?></small>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=show&amp;al=<?= $this->album ?>&amp;user=<?= $this->user['id'] ?>"><?= lng('cancel') ?></a>
</div>