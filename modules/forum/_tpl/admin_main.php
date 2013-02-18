<!-- Заголовок раздела -->
<ul class="title admin">
    <li class="left"><a href="<?= Vars::$HOME_URL ?>/admin"><span class="icn icn-back"></span></a></li>
    <li class="separator"></li>
    <li class="center"><h1><?= __('forum') ?></h1></li>
    <li class="right"></li>
</ul>

<div class="info-block">
    <?= __('sections') . ':&#160;' . $this->total_sub ?><br/>
    <?= __('categories') . ':&#160;' . $this->total_cat ?><br/>
    <?= __('themes') . ':&#160;' . $this->total_thm . '&#160;|&#160;' . __('deleted') . ':&#160;' . $this->total_thm_del ?><br/>
    <?= __('posts_adm') . ':&#160;' . $this->total_msg . '&#160;|&#160;' . __('deleted') . ':&#160;' . $this->total_msg_del ?><br/>
    <?= __('votes') . ':&#160;' . $this->total_votes ?><br/>
    <?= __('files') . ':&#160;' . $this->total_files ?>
</div>
<ul class="nav">
    <li><h2><?= __('forum_management') ?></h2></li>
    <li><a href="<?= $this->uri ?>cat/"><i class="icn-settings"></i><?= __('forum_structure') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= $this->uri ?>hposts/"><i class="icn-trash"></i><?= __('hidden_posts') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->total_msg_del ?></span></a></a></li>
    <li><a href="<?= $this->uri ?>htopics/"><i class="icn-trash"></i><?= __('hidden_topics') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->total_thm_del ?></span></a></a></li>
    <li><h2><?= __('back') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>admin/"><i class="icn-shield"></i><?= __('admin_panel') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>forum/"><i class="icn-comments"></i><?= __('forum') ?><i class="icn-arrow"></i></a></li>
</ul>