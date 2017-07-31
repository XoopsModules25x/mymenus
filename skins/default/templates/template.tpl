<span class="default_preload1"></span>
<span class="default_preload2"></span>
<{foreach item=menu from = $block}>
<{if $menu.oul}>
<{if 0 == $menu.level}>
<ul id="default_nav">
    <{elseif 1 == $menu.level}>
    <ul class="default_sub">
        <{else}>
        <ul>
            <{/if}>
            <{/if}>
            <{if $menu.oli}>
            <{if 0 == $menu.level}>
            <li class="default_top<{if $menu.css}> <{$menu.css}><{/if}>">
                <{else}>
            <li<{if $menu.css}> class="<{$menu.css}>"<{/if}>>
                <{/if}>
                <{/if}>
                <{assign var=myStr value="["|cat:$xoops_langcode|cat:"]"}>
                <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
                    <{if 0 == $menu.level}>
                        <a href="<{$menu.link}>" class="default_top_link<{if $menu.selected}> default_selected<{/if}>"
                           target="<{$menu.target}>" title="<{$menu.alt_title}>">
                            <span<{if $menu.hassub}> class="default_down"<{/if}>><{if $menu.image}><img
                                    src="<{$menu.image}>" alt="<{$menu.alt_title}>"><{/if}><{$menu.title}></span></a>
                    <{else}>
                        <a href="<{$menu.link}>"<{if $menu.hassub}> class="default_fly"<{/if}> target="<{$menu.target}>"
                           title="<{$menu.alt_title}>"><{if $menu.image}><img src="<{$menu.image}>"
                                                                              alt="<{$menu.alt_title}>"><{/if}><{$menu.title}>
                        </a>
                    <{/if}>
                <{/if}>
                <{if '' != $menu.close}><{$menu.close}><{/if}>
                <{/foreach}>
