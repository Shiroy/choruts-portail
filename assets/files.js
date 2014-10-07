/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$("#new-dir-btn").click(function (event)
{
    $("#new-dir").css("display", "block");
    event.preventDefault();
});

$("#new-dir-cancel").click(function (event)
{
    $("#new-dir").css("display", "none");
    event.preventDefault();
});

$(".file-link").click(function(event)
{
    var file_name = $(this).text();
    var file_ext = file_name.split('.').pop();
    var playable_ext = ["mp3", "ogg", "wav"];
    if(playable_ext.indexOf(file_ext) > -1)
    {
        event.preventDefault();
        $("#player").attr("src", $(this).attr("href"));
        $("#player").get(0).load();
        $("#player").get(0).play();
    }
});