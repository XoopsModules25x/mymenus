<!-- DynamicDrive.com SimpleTreeMenu-->
<script type="text/javascript">
    ddtreemenu.closefolder = "<{$xoops_url}>/modules/mymenus/skins/treemenu/closed.gif"; //set image path to "closed" folder image
    ddtreemenu.openfolder = "<{$xoops_url}>/modules/mymenus/skins/treemenu/open.gif"; //set image path to "open" folder image
</script>

<{php}>

$number= range(1, 10000);
shuffle($number);
echo "
<ul id='treemenu".$number[0]."' class='treeview'>";
    <{/php}>

    <{foreach item=menu from = $block}>
    <{if $menu.oul}>
    <{if $menu.level == 0}>
    <{elseif $menu.level == 1}>
    <{else}>
    <ul>
        <{/if}>
        <{/if}>
        <{if $menu.oli}>
        <{if $menu.level == 0}>
        <li>
            <{else}>
            <{/if}>
            <{/if}>
            <{if $menu.level == 0}>
            <{if $menu.hassub}> <{else}> <a href="<{$menu.link}>" target="<{$menu.target}>"
                                            title="<{$menu.alt_title}>"><{$menu.title}></a></li><{/if}>
        <{if $menu.hassub}>
        <li>
            <{$menu.title}>
            <ul>
                <{/if}>
                <{else}>
                <li><a href="<{$menu.link}>" target="<{$menu.target}>" title="<{$menu.alt_title}>"><{$menu.title}></a>
                    <{/if}>
                    <{if $menu.close != ''}><{$menu.close}><{/if}>
                    <{/foreach}>
            </ul>
            <script type="text/javascript">
                //ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
                //noinspection UnterminatedStatementJS
                ddtreemenu.createTree('treemenu<{php}>echo "$number[0]";<{/php}>', true)
                ddtreemenu.flatten('treemenu<{php}>echo "$number[0]";<{/php}>', 'contact')
            </script>

