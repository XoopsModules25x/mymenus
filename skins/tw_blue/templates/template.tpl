<div class="wrapper1">
    <div class="wrapper2">
        <div class="nav-wrapper">
            <div class="nav-left"></div>
            <div class="nav">
                <ul id="navigation">
                    <{foreach item=menu from=$block}>
                        <{assign var=myStr value="["|cat:$xoops_langcode|cat:"]"}>
                        <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
                            <{if 0 == $menu.level}>
                                <li<{if $menu.css || $menu.selected}> class="<{/if}>
                                    <{if $menu.selected}>active<{/if}>
                                    <{if $menu.css}> <{$menu.css}><{/if}>
                                    <{if $menu.css || $menu.selected}>"<{/if}>>
                                    <a href="<{$menu.link}>" target="<{$menu.target}>" title="<{$menu.alt_title}>">
                                        <span class="menu-left"></span>
                                        <span class="menu-mid">
<{if $menu.image}>
    <img src="<{$menu.image}>" alt="<{$menu.alt_title}>">
<{/if}>
                                            <{$menu.title}>
</span>
                                        <span class="menu-right"></span>
                                    </a>
                                    <{if $menu.hassub}>
                                        <div class="sub">
                                            <ul>
                                                <{foreach item=sub from=$block}>
                                                    <{if $sub.pid == $menu.id}>
                                                        <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
                                                            <li<{if $sub.class}> class="<{$sub.css}>"<{/if}>>
                                                                <a href="<{$sub.link}>" target="<{$sub.target}>"
                                                                   title="<{$sub.alt_title}>">
                                                                    <{if $sub.image}><img src="<{$sub.image}>"
                                                                                          alt="<{$sub.alt_title}>">
                                                                    <{/if}>
                                                                    <{$sub.title}>
                                                                </a>
                                                            </li>
                                                        <{/if}>
                                                    <{/if}>
                                                <{/foreach}>
                                            </ul>
                                            <div class="btm-bg"></div>
                                        </div>
                                    <{/if}>
                                </li>
                            <{/if}>
                        <{/if}>
                    <{/foreach}>
                </ul>
            </div>
            <div class="nav-right"></div>
        </div>
    </div>
</div>
