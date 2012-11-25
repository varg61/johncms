<ul class="nav">
    <li><h1 class="section-warning"><?= __('acl') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->saved)) : ?>
    <div class="form-block "><?= __('settings_saved') ?></div>
    <?php endif ?>

    <form method="post" action="<?= Vars::$URI ?>">
        <div class="form-block ">
            <label><?= __('forum') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="forum" <?= (isset(Vars::$ACL['forum']) && Vars::$ACL['forum'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_enabled') ?></label><br/>
            <label class="small"><input type="radio" value="1" name="forum" <?= (isset(Vars::$ACL['forum']) && Vars::$ACL['forum'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_authorised') ?></label><br/>
            <label class="small"><input type="radio" value="3" name="forum" <?= (isset(Vars::$ACL['forum']) && Vars::$ACL['forum'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= __('read_only') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="forum" <?= (!isset(Vars::$ACL['forum']) || !Vars::$ACL['forum'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_disabled') ?></label>
            <br/><br/>
            <label><?= __('photo_albums') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="album" <?= (isset(Vars::$ACL['album']) && Vars::$ACL['album'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_enabled') ?></label><br/>
            <label class="small"><input type="radio" value="1" name="album" <?= (isset(Vars::$ACL['album']) && Vars::$ACL['album'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_authorised') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="album" <?= (!isset(Vars::$ACL['album']) || !Vars::$ACL['album'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_disabled') ?></label><br/>
            <label class="small"><input name="albumcomm" type="checkbox" value="1" <?= (isset(Vars::$ACL['albumcomm']) && Vars::$ACL['albumcomm'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('comments') ?></label>
            <br/><br/>
            <label><?= __('guestbook') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="guestbook" <?= (isset(Vars::$ACL['guestbook']) && Vars::$ACL['guestbook'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_enabled_for_guests') ?></label><br/>
            <label class="small"><input type="radio" value="1" name="guestbook" <?= (isset(Vars::$ACL['guestbook']) && Vars::$ACL['guestbook'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_enabled') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="guestbook" <?= (!isset(Vars::$ACL['guestbook']) || !Vars::$ACL['guestbook'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_disabled') ?></label>
            <br/><br/>
            <label><?= __('library') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="library" <?= (isset(Vars::$ACL['library']) && Vars::$ACL['library'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_enabled') ?></label><br/>
            <label class="small"><input type="radio" value="1" name="library" <?= (isset(Vars::$ACL['library']) && Vars::$ACL['library'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_authorised') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="library" <?= (!isset(Vars::$ACL['library']) || !Vars::$ACL['library'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_disabled') ?></label><br/>
            <label class="small"><input name="libcomm" type="checkbox" value="1" <?= (isset(Vars::$ACL['libcomm']) && Vars::$ACL['libcomm'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('comments') ?></label>
            <br/><br/>
            <label><?= __('downloads') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="downloads" <?= (isset(Vars::$ACL['downloads']) && Vars::$ACL['downloads'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_enabled') ?></label><br/>
            <label class="small"><input type="radio" value="1" name="downloads" <?= (isset(Vars::$ACL['downloads']) && Vars::$ACL['downloads'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_authorised') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="downloads" <?= (!isset(Vars::$ACL['downloads']) || !Vars::$ACL['downloads'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('access_disabled') ?></label><br/>
            <label class="small"><input name="downcomm" type="checkbox" value="1" <?= (isset(Vars::$ACL['downcomm']) && Vars::$ACL['downcomm'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('comments') ?></label>
            <br/><br/>
            <label><?= __('statistic') ?></label><br/>
            <label class="small"><input type="radio" value="3" name="stat" <?= (isset(Vars::$ACL['stat']) && Vars::$ACL['stat'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= __('stat_enable_for_all') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="stat" <?= (isset(Vars::$ACL['stat']) && Vars::$ACL['stat'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= __('stat_enable_for_aut') ?></label><br/>
            <label class="small"><input type="radio" value="1" name="stat" <?= (isset(Vars::$ACL['stat']) && Vars::$ACL['stat'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= __('stat_enable_for_adm') ?></label><br/>
            <label class="small"><input type="radio" value="0" name="stat" <?= (!isset(Vars::$ACL['stat']) || !Vars::$ACL['stat'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('stat_disable') ?></label>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value=" <?= __('save') ?> "/>
            <a class="btn" href="<?= Vars::$MODULE_URI ?>"><?= __('back') ?></a>
        </div>
    </form>
</div>