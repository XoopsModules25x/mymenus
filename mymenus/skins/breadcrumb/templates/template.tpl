<ul class="mymenus-breadcrumb" style="background-image:url('<{$skinurl}>/<{$config.iconset}>/bc_bg.png');">
<{if $config.home}>
    <li>
        <a href="<{$xoops_url}>" alt="<{$smarty.const._MB_MYMENUS_HOME}>" title="<{$smarty.const._MB_MYMENUS_HOME}>" style="background-image:url('<{$skinurl}>/<{$config.iconset}>/bc_separator.png');">
        <img class="mymenus-breadcrumb-home" src="<{$skinurl}>/<{$config.iconset}>/home.png" alt="<{$smarty.const._MB_MYMENUS_HOME}>" />
        </a>
    </li>
<{/if}>
<{foreach item=menu from=$block}>
<{    assign var=myStr value="["|cat:$xoops_langcode|cat:"]"}>
<{    if ($xlanguage && (($menu.title|strstr:$myStr) OR ($menu.image|strstr:$myStr)) OR !$xlanguage)}>
<{        if $menu.selected && !$menu.topselected}>
    <li><a href="<{$menu.link}>" target="<{$menu.target}>" alt="<{$menu.alt_title}>" title="<{$menu.alt_title}>" style="background-image:url('<{$skinurl}>/<{$config.iconset}>/bc_separator.png');">
        <{$menu.title}>
        </a>
    </li>
<{        /if}>
<{        if $menu.topselected}>
    <li><{$menu.title}></li>
<{        /if}>
<{    /if}>
<{/foreach}>
</ul>
