<div id="help-template" class="outer">
    <{include file=$smarty.const._MI_MYMENUS_HELP_HEADER}>
    <h4 class="odd">Theory of operation</h4>

    <p class="even">
        Mymenus separates logic from presentation almost 100%!<br>
        It is mymenus task to generate an array with menus information and let each skin decide what to do with that
        information.
        <br>
        <br>
        How can skin know when to open a new &lt;li> or &lt;ul> and when to close it?<br>
        Mymenus append that information to each menu item, that way you can know if that item should be prefixed with a
        &lt;li> or not.
        <br>
        <br>
        This is the composition of an item array:
        <br>
        <code>
            [id] => 3 (id of the menu item)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[pid] => 0 (id of the parent menu item)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[mid] => 2 (id of the menu package)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[title] => Home (title of the menu
            item)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[alt_title] => Home (alt/title of the
            menu item)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[visible] => 1 (visibility of the menu
            item, it will be 1 for all menus,you can disregard it)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[link] =>
            http://localhost/xoops-2.4.5/htdocs/ (alt/title of the menu item)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[weight] => 1 (this is for internal
            usage of the builder class, you can disregard it)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[target] => _self (to be used in link
            target element, it can be _self, _blank, etc..)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[groups] => Array (holds the groups who
            can view this link, you can disregard it)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[0]
            => 2
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[1]
            => 3
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[hooks] => Array (holds the hooks
            available to render the menu, you can disregard it)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[image] => (image to be used in the
            link, you can choose not to support it in your skin)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[css] =>&nbsp; (this is inline css for
            this item, it goes inside &lt;a style="$item.css">)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[oul] => 1 (IMPORTANT! Open UL ->
            this menu item requires skin to prepend &lt;ul> open element tag)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[oli] => 1 (IMPORTANT! Open LI ->
            this menu item requires skin to prepend &lt;li> open element tag)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[close] => (IMPORTANT! this holds
            closing tags, it will automatically generate &lt;/li>&lt;/ul> tags for you)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[cul] => (IMPORTANT! Close UL ->
            this menu item requires skin to append &lt;/ul> close element tag, you should
            use [close] instead, unless you are not supporting multilevel menus)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[cli] => (IMPORTANT! Close LI ->
            this menu item requires skin to append &lt;/li> close element tag, you should
            use [close] instead, unless you are not supporting multilevel menus)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[hassub] => 1 (informs if this menu
            item has submenus, 1 for true, 0 for false)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[level] => 0 (informs the level of
            nesting of the menu item, 0 is for root, 1 for second level, etc..)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[down_weight] => 3 (for usage in menu
            sorting in admin side, you can ignore it)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[selected] => 1 (IMPORTANT, this tells
            the skin to highlight this item)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[topselected] => 1 (Important, this
            informs the skin that the menu is of level 0(root) and it is selected, you should
            highlight it)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
        </code>
    </p>
    <h4 class="odd">Skin structure</h4>

    <p class="even">
        Skins go into<br>
        "mymenus/skins" folder or<br>
        "public_html/themes/yourtheme/menu" folder<br>
        and they should have a skin_version.php file in it<br>
        <br>
        skin_version.php structure:<br>
        <code>
            //informs where to find the template for this skin(relative to skin folder)
            $skinVersion['template'] = 'templates/template.tpl';

            //informs where to find css file/files
            $skinVersion['css'] = 'assets/css/superfish.css';
        </code>
        or
        <code>
            $skinVersion['css'] = array('css/superfish.css', css/anotherone.css);

            //informs where to find js file/files
            $skinVersion['js'] = '../../js/assets/jquery-1.3.2.min.js';
        </code>
        or
        <code>
            $skinVersion['js'] = array(
            &nbsp;&nbsp;&nbsp;&nbsp;'../../js/jquery-1.3.2.min.js'
            &nbsp;&nbsp;&nbsp;&nbsp;'../../js/assets/hoverIntent.js',
            &nbsp;&nbsp;&nbsp;&nbsp;'../../js/superfish.js'
            );

            //code to be appended in the &lt;head> theme tag
            $header = "\n" . '&lt;script type="text/javascript">';
            $header .= "\n" . '&nbsp; var $sf = jQuery.noConflict()';
            $header .= "\n" . '&nbsp; $sf(function(){';
            $header .= "\n" . '&nbsp;&nbsp;&nbsp; $sf(\'ul.sf-menu\').superfish({';
            $header .= "\n" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;delay:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1000,';
            $header .= "\n" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;animation:&nbsp;&nbsp;
            {opacity:\'show\',height:\'show\'},';
            $header .= "\n" . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;speed:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; \'fast\'';
            $header .= "\n" . '&nbsp;&nbsp;&nbsp; });';
            $header .= "\n" . '&nbsp; });';
            $header .= "\n" . '&lt;/script>';
            $skinVersion['header'] = $header;

            //you can pass any configuration from this file to the template using ['config']
        </code>
        example:
        <code>
            $skinVersion['config']['home'] = true;
            $skinVersion['config']['iconset'] = 'default';
            This can be fetched in template with &lt;{$config.home}> and &lt;{$config.iconset}>
        </code>
    </p>
    <h4 class="odd">Smarty variables available in the template</h4>

    <p class="even">
        $block - holds an array of menu items<br>
        $config - holds configuration set in skin_version.php<br>
        $skinurl - holds the url of the skin<br>
        $skinpath - holds the path of the skin<br>
    </p>
    <h4 class="odd">For Theme designers</h4>

    <p class="even">
        Since users can choose the smarty variable for each menu,<br>
        I would advise you to use &lt;{$xoops_links_navbar}> as a place holder.<br>
        If you provide a skin for your theme, ask users to:<br>
        -- --enter "xoops_links_navbar" as unique_id in block settings.<br>
        -- --set "render to smarty variable" in block settings.<br>
        -- --set "use skin from theme" in block settings.<br>
    </p>
</div>
