<div id="help-template" class="outer">
    <{include file=$smarty.const._MI_MYMENUS_HELP_HEADER}>
    <h4 class="odd">Important to know:</h4><br>
    <br>
    Links and images are relative to the root of your site:<br>
    modules/profile<br>
    search.php<br>
    uploads/blank.gif<br>
    <br>
    For linking to external sites you need to use complete url:<br>
    http://www.xuups.com<br>
    <br>
    <br>
    You can use DECORATORS for links, images, title, and alt_title.<br>
    The decorators follow this syntax:<br>
    {decorator|value}<br>
    <br>
    There are 6 decorators available:<br>
    USER -> gets info for the user that is seeing the page<br>
    OWNER -> gets info for the user that match uid on the url(if given)<br>
    URI -> gets info about the url arguments<br>
    MODULE -> gets dynamic menu from a module (Used in title field only)<br>
    SMARTY -> gets smarty variables<br>
    CONSTANT -> gets defined constants<br>
    <br>
    Some syntax examples<br>
    {USER|UNAME} gets the username of this user, returns anonymous if not a
    user<br>
    {USER|UID} gets the uid of this user, returns 0 if not a user<br>
    {USER|REGDATE} gets the regdate of this user, returns empty if not a
    user<br>
    {USER|any other field of the user table} yes! You can get what you need!<br>
    <br>
    Some special fields you may use<br>
    {USER|PM_NEW} Show number of private messages not read<br>
    {USER|PM_READED}<br>
    {USER|PM_TOTAL}<br>
    <br>
    The same is valid for OWNER:<br>
    {OWNER|UNAME}<br>
    {OWNER|UID}<br>
    etc..<br>
    <br>
    And you can get any parameter on the uri with:<br>
    {URI|UID}<br>
    {URI|ID}<br>
    {URI|SEARCH}<br>
    {URI|ITEMID}<br>
    {URI|CATID}<br>
    etc...<br>
    <br>
    Example of links using decorators:<br>
    modules/profile/userinfo.php?uid={USER|UID}<br>
    modules/yogurt/pictures.php?uid={OWNER|UID}<br>
    <br>
    Example on titles using decorators:<br>
    {USER|UNAME}<br>
    {OWNER|UNAME} profile<br>
    You have searched for {URI|SEARCH}<br>
    <br>
    Populating menus with modules information:<br>
    {MODULE|NEWS}<br>
    {MODULE|XHELP}<br>
    {MODULE|MYLINKS}<br>
    {MODULE|TDMDOWNLOADS}<br>
    <br>
    Using smarty information:<br>
    {SMARTY|xoops_uname}<br>
    {SMARTY|xoops_avatar}<br>
    <br>
    Using constants information:<br>
    {CONSTANT|XOOPS_URL}/myimages/image.gif<br>
    {CONSTANT|XOOPS_ROOT_PATH}<br>
    <br>
</div>
