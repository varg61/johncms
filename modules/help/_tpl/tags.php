<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>"><b>F.A.Q.</b></a> | <?= lng('tags') ?>
</div>
<div class="menu"><p>
    <table cellpadding="3" cellspacing="0">
        <tr>
            <td align="right"><h3>BBcode</h3></td>
            <td></td>
        </tr>
        <tr>
            <td align="right">[php]...[/php]</td>
            <td><?= lng('tag_code') ?></td>
        </tr>
        <tr>
            <td align="right"><a href="#"><?= lng('link') ?></a></td>
            <td>[url=http://site_url] .]<span style="color:blue"><?= lng('tags_link_name') ?></span>[/url]</td>
        </tr>
        <tr>
            <td align="right">[b]...[/b]</td>
            <td><b><?= lng('tag_bold') ?></b></td>
        </tr>
        <tr>
            <td align="right">[i]...[/i]</td>
            <td><i><?= lng('tag_italic') ?></i></td>
        </tr>
        <tr>
            <td align="right">[u]...[/u]</td>
            <td><u><?= lng('tag_underline') ?></u></td>
        </tr>
        <tr>
            <td align="right">[s]...[/s]</td>
            <td><strike><?= lng('tag_strike') ?></strike></td>
        </tr>
        <tr>
            <td align="right">[red]...[/red]</td>
            <td><span style="color:red"><?= lng('tag_red') ?></span></td>
        </tr>
        <tr>
            <td align="right">[green]...[/green]</td>
            <td><span style="color:green"><?= lng('tag_green') ?></span></td>
        </tr>
        <tr>
            <td align="right">[blue]...[/blue]</td>
            <td><span style="color:blue"><?= lng('tag_blue') ?></span></td>
        </tr>
        <tr>
            <td align="right">[color=]...[/color]</td>
            <td><?= lng('color_text') ?></td>
        </tr>
        <tr>
            <td align="right">[bg=][/bg]</td>
            <td><?= lng('color_bg') ?></td>
        </tr>
        <tr>
            <td align="right">[c]...[/c]</td>
            <td><span class="quote"><?= lng('tag_quote') ?></span></td>
        </tr>
        <tr>
            <td align="right" valign="top">[*]...[/*]</td>
            <td><span class="bblist"><?= lng('tag_list') ?></span></td>
        </tr>
    </table>
    </p></div>
<div class="phdr">
    <a href="<?= htmlspecialchars($_SERVER['HTTP_REFERER']) ?>"><?= lng('back') ?></a>
</div>