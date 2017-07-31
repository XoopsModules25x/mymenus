/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Modules Javascript
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Bleekk
 */

$(document).ready(function () {
    $('ol.sortable').nestedSortable({
        handle: 'div',
        items: 'li',
        tolerance: 'pointer',
        toleranceElement: '> div',
        placeholder: 'ui-state-highlight',
        helper: 'clone',
        opacity: .6,
        revert: 250,
        tabSize: 25,
        maxLevels: 3,
        isTree: true,
        expandOnHover: 700,
        startCollapsed: true,
        update: function (event, ui) {
            var list = $(this).nestedSortable('serialize');
            $.post('links.php', {op: "order", mod: list}, function (o) {
                console.log(o);
            }, 'json');
        }
    });

    $('.disclose').on('click', function () {
        $(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
    });

    $("#addform").dialog({
        autoOpen: false,
        height: 480,
        width: 550,
        modal: true
    });


    $("#new-link")
        .button()
        .click(function () {
            $("#addform").dialog("open");
        });

});

function itemOnOff(id) {
    $("#hidden-result_ " + id).show();
    var $id = '#id-' + id;
    $("#result").load("links.php?id=" + id + "&op=toggle", function (response, status, xhr) {
        if (status == "error") {
            var msg = "Sorry but there was an error: ";
            $("#error").html(msg + xhr.status + " " + xhr.statusText);
        } else {
            $("#hidden-result_" + id).hide();
            $('#id-' + id).toggleClass("icon-0 icon-1");
        }

    });
}

function showWindow(id, menuid) {
    $("#hidden-result_" + id).show();
    $("#result").load("links.php?op=edit&id=" + id + "&mid=" + menuid + "", function (response, status, xhr) {
        if (status == "error") {
            var msg = "Sorry but there was an error: ";
            $("#error").html(msg + xhr.status + " " + xhr.statusText);
        } else {
            $("#hidden-result_" + id).hide();
        }
    }).dialog({
        width: 550,
        modal: true,
        close: function () {
            $("#result").empty();
        }
    });
}
