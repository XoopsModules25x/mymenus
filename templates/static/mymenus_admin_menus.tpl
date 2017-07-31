<fieldset>
    <legend><{$smarty.const._AM_MYMENUS_MENUS_LIST}></legend>
    <{if $menusCount == 0}>
        <{$smarty.const._AM_MYMENUS_MSG_NOTFOUND}>
    <{else}>
        <table class='outer width100' cellspacing='1'>
            <tr class='odd'>
                <td>
                    <form id='filter_form' name='filter_form' action='' method='post' enctype='multipart/form-data'>
                        <{$smarty.const._AM_MYMENUS_MENU_TITLE}>
                        <{$filter_menus_title_condition_select}>
                        <input id='filter_menus_title' type='text' value='<{$filter_menus_title}>' maxlength='255'
                               size='25' title='' name='filter_menus_title'>
                        &nbsp;
                        <input type='submit' id='filter_submit' class='formButton'
                               title='<{$smarty.const._AM_MYMENUS_BUTTON_FILTER}>'
                               value='<{$smarty.const._AM_MYMENUS_BUTTON_FILTER}>'
                               name='filter_submit'>
                        <input type='hidden' id='op' name='op' value='list'>
                        <input type='hidden' id='filter_op' name='apply_filter' value='1'>
                    </form>
                </td>
            </tr>
        </table>
        <table class="outer">
            <tr>
                <td align='left' colspan='3'>
                    <{if ($apply_filter == false)}>
                        <{$smarty.const._AM_MYMENUS_MENUS_COUNT|replace:'%menusCount':$menusCount}>
                    <{else}>
                        <{$smarty.const._AM_MYMENUS_MENUS_COUNT_OF|replace:'%menusCount':$menusCount|replace:'%menusFilterCount':$menusFilterCount}>
                    <{/if}>
                </td>
            </tr>
            <tr align="center">
                <th><{$smarty.const._AM_MYMENUS_MENU_TITLE}></th>
                <th><{$smarty.const._AM_MYMENUS_MENU_CSS}></th>
                <th width="15%"><{$smarty.const._OPTIONS}></th>
            </tr>
            <{foreach item=menu key=key from=$menus}>
                <tr class="<{cycle values="even,odd"}>" align="center">
                    <td align="left"><{$menu.title}></td>
                    <td align="left"><{$menu.css}></td>
                    <td>
                        <a href="?op=edit&amp;id=<{$menu.id}>"><img src="<{xoModuleIcons16 edit.png}>"
                                                                    title="<{$smarty.const._EDIT}>"
                                                                    alt="<{$smarty.const._EDIT}>"></a>
                        <a href="?op=delete&amp;id=<{$menu.id}>"><img src="<{xoModuleIcons16 delete.png}>"
                                                                      title="<{$smarty.const._DELETE}>"
                                                                      alt="<{$smarty.const._DELETE}>"></a>
                        <a href="links.php?op=list&amp;mid=<{$menu.id}>"><img src="<{xoModuleIcons16 forward.png}>"
                                                                              title="<{$smarty.const._AM_MYMENUS_ACTION_GOTO_MENU}>"
                                                                              alt="<{$smarty.const._AM_MYMENUS_ACTION_GOTO_MENU}>"></a>
                    </td>
                </tr>
            <{/foreach}>
        </table>
        <{$pagenav}>
    <{/if}>
</fieldset>
