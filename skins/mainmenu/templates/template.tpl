<div id="mainmenu">
    <{foreach item=menu from=$block}>
        <{if $menu.level == 0}>
            <{assign var=myStr value="["|cat:$xoops_langcode|cat:"]"}>
            <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
                <a class="menuMain<{if $menu.css}> <{$menu.css}><{/if}><{if $menu.topselected}> maincurrent<{/if}>"
                   href="<{$menu.link}>" target="<{$menu.target}>" title="<{$menu.alt_title}>">
                    <{if $menu.image}><img src="<{$menu.image}>"><{/if}>
                    <{$menu.title}>
                </a>
                <br>
                <{if $menu.selected}>
                    <{foreach item=sub from=$block}>
                        <{if $menu.id == $sub.pid}>
                            <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
                                <a class="menuSub marg5<{if $sub.css}> <{$sub.css}><{/if}><{if $sub.selected}> maincurrent<{/if}>"
                                   href="<{$sub.link}>" target="<{$sub.target}>"
                                   title="<{$sub.alt_title}>">
                                    <{if $sub.image}><img src="<{$sub.image}>" alt="<{$sub.alt_title}>"><{/if}>
                                    <{$sub.title}>
                                </a>
                                <br>
                            <{/if}>
                        <{/if}>
                    <{/foreach}>
                <{/if}>
            <{/if}>
        <{/if}>
    <{/foreach}>
</div>
