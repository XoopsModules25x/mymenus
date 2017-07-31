<{foreach item=menu from = $block}>
<{if $menu.oul}>
<{if $menu.level == 0}>
<ul class="nav navbar-nav<{if $menu.css}> <{$menucss}><{/if}>">
    <{elseif $menu.level == 1}>
    <ul class="dropdown-menu<{if $menu.css}> <{$menucss}><{/if}>">
        <{else}>
        <ul class="dropdown-menu sub-menu<{if $menu.css}> <{$menucss}><{/if}>">
            <{/if}>
            <{/if}>
            <{if $menu.oli}>
            <{assign var=myStr value="["|cat:$xoops_langcode|cat:"]"}>
            <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>

            <{if $menu.hassub && $menu.level == 0}>
            <li class="dropdown<{if $menu.selected}> active<{/if}><{if $menu.css}> <{$menu.css}><{/if}>">
                <a href="<{$menu.link}>" target="<{$menu.target}>" title="<{$menu.alt_title}>">
                    <{if $menu.image}><img class="menu-image" src="<{$menu.image}>" alt="<{$menu.alt_title}>"> <{/if}>
                    <{$menu.title}> <b class="caret"></b>
                </a>
                <{elseif $menu.hassub && $menu.level == 1}>
            <li<{if $menu.selected || $menu.css}> class="<{/if}>
<{if $menu.selected}>active<{/if}>
<{if $menu.css}> <{$menu.css}><{/if}>
<{if $menu.selected || $menu.css}>"<{/if}>>
                <a href="<{$menu.link}>" target="<{$menu.target}>" title="<{$menu.alt_title}>">
                    <{if $menu.image}><img class="menu-image" src="<{$menu.image}>" alt="<{$menu.alt_title}>"> <{/if}>
                    <{$menu.title}> <i class="glyphicon glyphicon-arrow-right"></i>
                </a>
                <{else}>
            <li<{if $menu.selected || $menu.css}> class="<{/if}>
<{if $menu.selected}>active<{/if}>
<{if $menu.css}> <{$menu.css}><{/if}>
<{if $menu.selected || $menu.css}>"<{/if}>>
                <a href="<{$menu.link}>" target="<{$menu.target}>" title="<{$menu.alt_title}>">
                    <{if $menu.image}><img class="menu-image" src="<{$menu.image}>" alt="<{$menu.alt_title}>"> <{/if}>
                    <{$menu.title}>
                </a>
                <{/if}>
                <{/if}>
                <{/if}>
                <{if $menu.close != ''}><{$menu.close}><{/if}>
                <{/foreach}>
