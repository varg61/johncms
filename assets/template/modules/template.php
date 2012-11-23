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
    <meta name="keywords" content="<?= Validate::checkout(Vars::$SYSTEM_SET['keywords']) ?>"/>
    <meta name="description" content="<?= Validate::checkout(Vars::$SYSTEM_SET['description']) ?>"/>
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
<div id="basement" class="align-center">
    <div class="site-copyright"><?= Validate::checkout(Vars::$SYSTEM_SET['copyright'], 1, 1, 1) ?></div>
    <div class="counters"><?= Functions::displayCounters() ?></div>
    <div>Generation: <?= round((microtime(TRUE) - START_TIME), 4) ?> sec<br/>Memory: <?= round((memory_get_usage() - START_MEMORY) / 1024, 2) ?> kb</div>
    <div class="copyright"><a href="http://johncms.com">JohnCMS</a></div>
</div>
</body>
</html>