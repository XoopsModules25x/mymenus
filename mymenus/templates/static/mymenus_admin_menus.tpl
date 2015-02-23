<script type="text/javascript">
    function admin_showDiv(type, id) {
        divs = document.getElementsByTagName('div');
        for (i = 0; i < divs.length; i++) {
            if (/opt_divs/.test(divs[i].className)) {
                divs[i].style.display = 'none';
            }
        }
        if (!id)id = '';
        document.getElementById(type + id).style.display = 'block';
        document.anchors.item(type + id).scrollIntoView();
    }
</script>

<div style="margin-top:0; float: right; width:400px;" align="right">
    <form action="admin_menus.php?op=list" method="POST">
        <input type="text" name="query" id="query" size="30" value="<{$query}>"/>
        <input type="submit" name="btn" value="<{$smarty.const._SEARCH}>"/>
        <input type="submit" name="btn1" value="<{$smarty.const._CANCEL}>" onclick="document.getElementById('query').value='';"/>
    </form>
</div>

<table width="100%" cellspacing="1" cellpadding="0" class="outer">
    <tr align="center">
        <th><{$smarty.const._AM_MYMENUS_MENU_TITLE}></th>
        <th width="15%"><{$smarty.const._OPTIONS}></th>
    </tr>
    <{if $count > 0}>
        <{foreach item=obj key=key from=$objs}>
            <tr class="<{cycle values="even,odd"}>" align="center">
                <td align="left"><{$obj.title}></td>
                <td>
                    <a href="admin_menus.php?op=edit&amp;id=<{$obj.id}>"><img src="<{xoModuleIcons16 edit.png}>" title="<{$smarty.const._EDIT}>" alt="<{$smarty.const._EDIT}>"/></a>
                    <a href="admin_menus.php?op=del&amp;id=<{$obj.id}>"><img src="<{xoModuleIcons16 delete.png}>" title="<{$smarty.const._DELETE}>" alt="<{$smarty.const._DELETE}>"/></a>
                    <a href="admin_links.php?op=list&amp;mid=<{$obj.id}>"><img src="<{xoModuleIcons16 forward.png}>" title="<{$smarty.const._AM_MYMENUS_ACTION_GOTO_MENU}>" alt="<{$smarty.const._AM_MYMENUS_ACTION_GOTO_MENU}>"/></a>
                </td>
            </tr>
        <{/foreach}>
    <{else}>
        <tr>
            <td class="head" colspan="2" align="center"><{$smarty.const._AM_MYMENUS_MSG_NOTFOUND}></td>
        </tr>
    <{/if}>
    <tr>
        <td class="head" colspan="2" align="right">
            <{$pag}>
            <input type="button" onclick="admin_showDiv('addform','','hiddendiv'); return false;" value="<{$smarty.const._ADD}>"/>
        </td>
    </tr>
    <tr>
        <td class="head" colspan="2" align="right">
            <{$pag}>
        </td>
    </tr>

</table>
<br/>
<a name="addform_anchor"></a>
<div id="addform" class="hiddendiv" style="display:none;"><{$addform}></div>
