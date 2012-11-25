<ul class="nav">
    <li><h1><?= __('tags') ?></h1></li>
    <li><div class="block-even">
        <table class="table-list">
            <tr>
                <td align="right">[php]...[/php]</td>
                <td><?= __('tag_code') ?></td>
            </tr>
            <tr>
                <td align="right"><span style="text-decoration: underline; color:blue;"><?= __('link') ?></span></td>
                <td>[url=http://site_url] .]<span style="color:blue"><?= __('tags_link_name') ?></span>[/url]</td>
            </tr>
            <tr>
                <td align="right">[b]...[/b]</td>
                <td><b><?= __('tag_bold') ?></b></td>
            </tr>
            <tr>
                <td align="right">[i]...[/i]</td>
                <td><span style="font-style: italic;"><?= __('tag_italic') ?></span></td>
            </tr>
            <tr>
                <td align="right">[u]...[/u]</td>
                <td><u><?= __('tag_underline') ?></u></td>
            </tr>
            <tr>
                <td align="right">[s]...[/s]</td>
                <td><strike><?= __('tag_strike') ?></strike></td>
            </tr>
            <tr>
                <td align="right">[red]...[/red]</td>
                <td><span style="color:red"><?= __('tag_red') ?></span></td>
            </tr>
            <tr>
                <td align="right">[green]...[/green]</td>
                <td><span style="color:green"><?= __('tag_green') ?></span></td>
            </tr>
            <tr>
                <td align="right">[blue]...[/blue]</td>
                <td><span style="color:blue"><?= __('tag_blue') ?></span></td>
            </tr>
            <tr>
                <td align="right">[color=]...[/color]</td>
                <td><?= __('color_text') ?></td>
            </tr>
            <tr>
                <td align="right">[bg=][/bg]</td>
                <td><?= __('color_bg') ?></td>
            </tr>
            <tr>
                <td align="right">[c]...[/c]</td>
                <td><span class="quote"><?= __('tag_quote') ?></span></td>
            </tr>
            <tr>
                <td align="right" valign="top">[*]...[/*]</td>
                <td><span class="bblist"><?= __('tag_list') ?></span></td>
            </tr>
        </table>
    </div></li>
</ul>
<div class="phdr">
    <a href="<?= (isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL) ?>"><?= __('back') ?></a>
</div>