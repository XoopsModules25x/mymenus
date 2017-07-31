<{foreach item=menu from = $block}>
<{if $menu.oul}>
<{if 0 == $menu.level}>
<ul class="sf-menu sf-navbar">
    <{else}>
    <ul>
        <{/if}>
        <{/if}>
        <{if $menu.oli}>
        <li<{if $menu.css || $menu.selected}> class="<{/if}>
            <{if $menu.css}>{<$menu.css}> <{/if}>
            <{if $menu.selected}>current<{/if}>
            <{if $menu.css || $menu.selected}>"<{/if}>>
            <{/if}>
            <{assign var=myStr value="["|cat:$xoops_langcode|cat:"]"}>
            <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
                <a href="<{$menu.link}>" target="<{$menu.target}>" title="<{$menu.alt_title}>">
                    <{if $main.image}>
                        <img src="<{$menu.image}>" alt="<{$menu.alt_title}>">
                    <{/if}>
                    <{$menu.title}>
                </a>
            <{/if}>
            <{if '' != $menu.close}><{$menu.close}><{/if}>
            <{/foreach}>
            <div style="clear:both;"></div>
