<{foreach item=menu from=$block}>
<{if $menu.oul && $menu.level == 0}>
<ul<{if $menucss != ''}> class="<{$menucss}>"<{/if}>>
    <{/if}>

    <{assign var=myStr value="["|cat:$xoops_langcode|cat:"]"}>
    <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
    <{if $menu.level == 0}>
    <li<{if $menu.css}> class="<{$menu.css}>"<{/if}>>
        <a<{if $menu.selected}> class="active"<{/if}> href="<{$menu.link}>"><{$menu.title}></a>
        <{if $menu.hassub}>
        <{foreach item=sub from=$block name=sublp}>
        <{if $smarty.foreach.sublp.first}>
        <ul class="dropdown-menu"><!-- sub menu --><{/if}>
            <{if ($xlanguage && (($menu.title|strstr:$myStr) || ($menu.image|strstr:$myStr)) || !$xlanguage)}>
            <{if $menu.id == $sub.pid}>
            <li<{if $sub.css}> class="<{$sub.css}>"<{/if}>>
                <a<{if $sub.selected}> class="active"<{/if}> href="<{$sub.link}>" target="<{$sub.target}>"
                                                             title="<{$sub.alt_title}>"><{$sub.title}>
                </a>
                <{if !empty($block.subsub)}>
                    <{foreach item=subsub from=$block name=subsublp}>
                        <{if $smarty.foreach.subsublp.first}>
                            <ul class="dropdown-menu"><!-- subsub menu --><{/if}>
                    <{if $sub.id == $subsub.pid}>
                        <li<{if $subsub.css}> class="<{$subsub.css}>"<{/if}>>
                        <a<{if $subsub.selected}> class="active"<{/if}> href="<{$subsub.link}>"
                                                                        target="<{$subsub.target}>"
                                                                        title="<{$subsub.alt_title}>"><{$subsub.title}>
                        </a>
                    <{/if}>
                        <{if $smarty.foreach.subsublp.last}>
                            </ul>
                        <{/if}>
                    <{/foreach}>
                <{/if}>
                <{/if}>
                <{/if}>
                <{/foreach}>
                <{/if}>
                <{/if}>
                <{/if}>
                <{if '' != $menu.close}>
                    <{$menu.close}>
                <{/if}>
                <{/foreach}>
