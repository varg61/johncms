<form action="<?= Vars::$URI ?>?act=edit<?= (Vars::$ID ? '&amp;id=' . Vars::$ID : '') ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label><?= lng('link') ?></label><br/>
            <input type="checkbox" name="show" <?= ($this->res['show'] ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('link_direct') ?><br/>
            <input type="text" name="link" value="<?= htmlentities($this->res['link'], ENT_QUOTES, 'UTF-8') ?>"/><br/>
            <div class="desc">
                <?= lng('link_direct_help') ?>
            </div>
        </div>
        <div class="formblock">
            <label><?= lng('title') ?></label><br/>
            <input type="text" name="name" value="<?= htmlentities($this->res['name'], ENT_QUOTES, 'UTF-8') ?>"/><br/>
            <div class="desc">
                <?= lng('link_add_name_help') ?>
            </div>
        </div>
        <div class="formblock">
            <label><?= lng('color') ?></label><br/>
            <input type="text" name="color" size="6" value="<?= $this->res['color'] ?>"/><br/>
            <div class="desc">
                <?= lng('link_add_color_help') ?>
            </div>
        </div>
        <div class="formblock">
            <label><?= lng('transitions') ?></label><br/>
            <input type="text" name="count" size="6" value="<?= $this->res['count_link'] ?>"/><br/>
            <div class="desc">
                <?= lng('link_add_trans_help') ?>
            </div>
        </div>
        <div class="formblock">
            <label><?= lng('days') ?></label><br/>
            <input type="text" name="day" size="6" value="<?= $this->res['day'] ?>"/><br/>
            <div class="desc">
                <?= lng('link_add_days_help') ?>
            </div>
        </div>
    </div>
    <div class="gmenu">
        <div class="formblock">
            <label><?= lng('to_show') ?></label><br/>
            <input type="radio" name="view" value="0" <?= (!$this->res['view'] ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('to_all') ?><br/>
            <input type="radio" name="view" value="1" <?= ($this->res['view'] == 1 ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('to_guest') ?><br/>
            <input type="radio" name="view" value="2" <?= ($this->res['view'] == 2 ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('to_users') ?>
        </div>
        <div class="formblock">
            <label><?= lng('arrangement') ?></label><br/>
            <input type="radio" name="type" value="0" <?= (!$this->res['type'] ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('endwise') ?><br/>
            <input type="radio" name="type" value="1" <?= ($this->res['type'] == 1 ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('above_content') ?><br/>
            <input type="radio" name="type" value="2" <?= ($this->res['type'] == 2 ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('below_content') ?><br/>
            <input type="radio" name="type" value="3" <?= ($this->res['type'] == 3 ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('below') ?>
        </div>
        <div class="formblock">
            <label><?= lng('placing') ?></label><br/>
            <input type="radio" name="layout" value="0" <?= (!$this->res['layout'] ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('link_add_placing_all') ?><br/>
            <input type="radio" name="layout" value="1" <?= ($this->res['layout'] == 1 ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('link_add_placing_front') ?><br/>
            <input type="radio" name="layout" value="2" <?= ($this->res['layout'] == 2 ? 'checked="checked"' : '') ?>/>&nbsp;<?= lng('link_add_placing_child') ?>
        </div>
        <div class="formblock">
            <label><?= lng('links_allocation') ?></label><br/>
            <input type="checkbox" name="bold" <?= ($this->res['bold'] ? 'checked="checked"' : '') ?>/>&nbsp;<b><?= lng('font_bold') ?></b><br/>
            <input type="checkbox" name="italic" <?= ($this->res['italic'] ? 'checked="checked"' : '') ?>/>&nbsp;<i><?= lng('font_italic') ?></i><br/>
            <input type="checkbox" name="underline" <?= ($this->res['underline'] ? 'checked="checked"' : '') ?>/>&nbsp;<u><?= lng('font_underline') ?></u>
        </div>
    </div>
    <div class="phdr"><input type="submit" name="submit" value="<?= (Vars::$ID ? lng('edit') : lng('add')) ?>"/>
    </div>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
<p>
    <a href="<?= Vars::$URI ?>"><?= lng('advertisement') ?></a><br/>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('admin_panel') ?></a>
</p>