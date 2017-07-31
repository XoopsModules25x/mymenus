<style type="text/css">
    .icon-1:before, .icon-0:before {
        content: "\2212";
    }

    .icon-1 {
        width: 16px;
        height: 16px;
        background: url('<{xoModuleIcons16 1.png}>') 0 0 no-repeat transparent !important;
        border: 0 none !important;
        padding: 0;
        margin: 1px 2px 0 0 !important;
        cursor: pointer;
    }

    .icon-0 {
        width: 16px;
        height: 16px;
        background: url('<{xoModuleIcons16 0.png}>') 0 0 no-repeat transparent !important;
        border: 0 none !important;
        padding: 0;
        margin: 1px 2px 0 0 !important;
        cursor: pointer;
    }
</style>

<div id="result" style="display: none;"></div>

<div class="width100">
    <div class="floatleft">
        <button role="button" class="ui-button" id="new-link">
            <{$smarty.const._AM_MYMENUS_ACTION_ADD_LINK}>
        </button>
    </div>
    <div class="floatright">
        <form method="POST" action="links.php?op=list" id="changemenu">
            <select name="mid" id="mid" class="select-options" onchange="this.form.submit()">
                <{foreach item=title from=$menus_list key=id }>
                    <option value="<{$id}>"
                            <{if $mid == $id}> selected<{/if}>><{$title}></option>
                <{/foreach}>
            </select>
            <!--
               <button role="button" class="ui-xbutton">
                <{$smarty.const._AM_MYMENUS_ACTION_GOTO_MENU}>
            </button>
 -->
        </form>
    </div>
    <div class="clear"></div>
</div>

<div class="width100">
    <ol class="sortable">
        <{foreach item=menu from=$menus}>
            <{if $menu.level == 0}>
                <li id="mod_<{$menu.id}>" class="ui-state-default">
                    <div>
                        <table class="width100">
                            <tr>
                                <td width="12%">
                                    <img style="padding: 0 5px 0 2px;"
                                         src="<{xoAppUrl}>modules/mymenus/assets/images/actions/move_vertical-16.png">
                                    <{$menu.title}>
                                </td>
                                <td width="17%" class="discrete">
                                    <{$menu.link}>
                                </td>
                                <td width="6%" class="discrete">
                                    <{$smarty.const._AM_MYMENUS_MENU_GROUPS}>:&nbsp;
                                    <{foreach item=group from=$menu.groups name=groupsloop}>
                                        <{$group}><{if !$smarty.foreach.groupsloop.last}>,<{/if}>
                                    <{/foreach}>
                                </td>
                                <td width="1%" class="discrete">
                            <span id="hidden-result_<{$menu.id}>" style="display:none;">
                                <img style="margin: 3px 4px 0 0;" src="../assets/images/loading.gif" title="loading"
                                     alt="loading">
                            </span>
                                    <input id="id-<{$menu.id}>" type="button"
                                           title="<{$smarty.const._AM_MYMENUS_ACTION_TOGGLE}>"
                                           class="toggleBtn icon-<{$menu.visible}>"
                                           onclick="itemOnOff(<{$menu.id}>)" readonly="readonly">
                                </td>
                                <td width="3%" class="discrete">
                                    <a href="#" onclick="showWindow(<{$menu.id}>, <{$mid}>)">
                                        <img style="padding: 1px 2px 0 0;" src="<{xoModuleIcons16 edit.png}>"
                                             title="<{$smarty.const._EDIT}>" alt="<{$smarty.const._EDIT}>">
                                    </a>
                                    <a href="links.php?mid=<{$menu_id}>&amp;op=delete&amp;id=<{$menu.id}>">
                                        <img style="padding: 1px 2px 0 0;" src="<{xoModuleIcons16 delete.png}>"
                                             title="<{$smarty.const._DELETE}>" alt="<{$smarty.const._DELETE}>">
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- sub -->
                    <{foreach item=smenu from=$menus}>
                        <{if ($smenu.pid != 0) && ($menu.id == $smenu.pid)}>
                            <ol>
                                <li id="mod_<{$smenu.id}>" class="ui-state-default">
                                    <div>
                                        <table width="100%">
                                            <tr>
                                                <td width="12%">
                                                    <img style="padding: 0 5px 0 2px;"
                                                         src="<{xoAppUrl}>modules/mymenus/assets/images/actions/move_vertical-16.png">
                                                    <{$smenu.title}>
                                                </td>
                                                <td width="17%" class="discrete">
                                                    <{$smenu.link}>
                                                </td>
                                                <td width="6%" class="discrete">
                                                    <{$smarty.const._AM_MYMENUS_MENU_GROUPS}>:&nbsp;
                                                    <{foreach item=group from=$smenu.groups name=groupsloop}>
                                                        <{$group}><{if !$smarty.foreach.groupsloop.last}>,<{/if}>
                                                    <{/foreach}>
                                                </td>
                                                <td width="1%" class="discrete">
                                    <span id="hidden-result_<{$smenu.id}>" style="display:none;">
                                        <img style="margin: 3px 4px 0 0;" src="../assets/images/loading.gif"
                                             title="loading" alt="loading">
                                    </span>
                                                    <input id="id-<{$smenu.id}>" type="button"
                                                           title="<{$smarty.const._AM_MYMENUS_ACTION_TOGGLE}>"
                                                           class="toggleBtn icon-<{$smenu.visible}>"
                                                           onclick="itemOnOff(<{$smenu.id}>)">
                                                </td>
                                                <td width="3%" class="discrete">
                                                    <a href="#" onclick="showWindow(<{$smenu.id}>)">
                                                        <img style="padding: 1px 2px 0 0;"
                                                             src="<{xoModuleIcons16 edit.png}>"
                                                             title="<{$smarty.const._EDIT}>"
                                                             alt="<{$smarty.const._EDIT}>">
                                                    </a>
                                                    <a href="links.php?mid=<{$smenu_id}>&amp;op=delete&amp;id=<{$smenu.id}>">
                                                        <img style="padding: 1px 2px 0 0;"
                                                             src="<{xoModuleIcons16 delete.png}>"
                                                             title="<{$smarty.const._DELETE}>"
                                                             alt="<{$smarty.const._DELETE}>">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- sub sub -->
                                    <{foreach item=ssmenu from=$menus}>
                                        <{if ($ssmenu.pid != 0) && ($ssmenu.pid == $smenu.id)}>
                                            <ol>
                                                <li id="mod_<{$ssmenu.id}>" class="ui-state-default">
                                                    <div>
                                                        <table width="100%">
                                                            <tr>
                                                                <td width="12%">
                                                                    <img style="padding: 0 5px 0 2px;"
                                                                         src="<{xoAppUrl}>modules/mymenus/assets/images/actions/move_vertical-16.png">
                                                                    <{$ssmenu.title}>
                                                                </td>
                                                                <td width="17%" class="discrete">
                                                                    <{$ssmenu.link}>
                                                                </td>
                                                                <td width="6%" class="discrete">
                                                                    <{$smarty.const._AM_MYMENUS_MENU_GROUPS}>:&nbsp;
                                                                    <{foreach item=group from=$ssmenu.groups name=groupsloop}>
                                                                        <{$group}><{if !$smarty.foreach.groupsloop.last}>,<{/if}>
                                                                    <{/foreach}>
                                                                </td>
                                                                <td width="1%" class="discrete">
                                            <span id="hidden-result_<{$ssmenu.id}>" style="display:none;">
                                                <img style="margin: 3px 4px 0 0;" src="../assets/images/loading.gif"
                                                     title="loading" alt="loading">
                                            </span>
                                                                    <input id="id-<{$ssmenu.id}>" type="button"
                                                                           title="<{$smarty.const._AM_MYMENUS_ACTION_TOGGLE}>"
                                                                           class="toggleBtn icon-<{$ssmenu.visible}>"
                                                                           onclick="itemOnOff(<{$ssmenu.id}>)">
                                                                </td>
                                                                <td width="3%" class="discrete">
                                                                    <a href="#" onclick="showWindow(<{$ssmenu.id}>)">
                                                                        <img style="padding: 1px 2px 0 0;"
                                                                             src="<{xoModuleIcons16 edit.png}>"
                                                                             title="<{$smarty.const._EDIT}>"
                                                                             alt="<{$smarty.const._EDIT}>">
                                                                    </a>
                                                                    <a href="links.php?mid=<{$ssmenu_id}>&amp;op=delete&amp;id=<{$ssmenu.id}>">
                                                                        <img style="padding: 1px 2px 0 0;"
                                                                             src="<{xoModuleIcons16 delete.png}>"
                                                                             title="<{$smarty.const._DELETE}>"
                                                                             alt="<{$smarty.const._DELETE}>">
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </li>
                                            </ol>
                                        <{/if}>
                                    <{/foreach}>
                                </li>
                            </ol>
                        <{/if}>
                    <{/foreach}>
                </li>
            <{/if}>
        <{/foreach}>
    </ol>
</div>

<div style="clear:both;"></div>
<br>
<a name="addform_anchor"></a>
<div id="addform" class="hiddendiv" style="display:none;"><{$addform}></div>
