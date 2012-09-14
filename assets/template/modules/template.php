<?php
defined('_IN_JOHNCMS') or die('Error: restricted access');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="HandheldFriendly" content="true"/>
    <meta name="MobileOptimized" content="width"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <meta name="keywords" content="<?= Vars::$SYSTEM_SET['meta_key'] ?>"/>
    <meta name="description" content="<?= Vars::$SYSTEM_SET['meta_desc'] ?>"/>
    <?= $this->loadCSS() ?>
    <link rel="shortcut icon" href="<?= Vars::$HOME_URL ?>/favicon.ico"/>
    <link rel="alternate" type="application/rss+xml" title="<?= lng('site_news', 1) ?>" href="http://localhost/johncms/rss"/>
    <title><?= isset($this->title) ? $this->title : Vars::$SYSTEM_SET['copyright'] ?></title>
    <?php if (!Vars::$IS_MOBILE): ?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <?php endif ?>
</head>
<body>
<div id="container">
    <a name="top"></a>
    <?= $this->includeTpl('toolbar-top', 1) ?>
    <?= $this->contents ?>
    <?= $this->includeTpl('toolbar-bottom', 1) ?>
</div>
</body>
</html>