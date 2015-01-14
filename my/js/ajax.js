var logout_url="ajax/checkLogin.aspx";
var news_url="ajax/news.html";
var logs_url="ajax/logs.aspx";
var apps_url="ajax/apps.aspx";
var app_data_url="ajax/app_data.aspx";
var app_create_url="ajax/app_create.aspx";
var save_game_data="ajax/save_game_data.aspx";
var app_edit_url="ajax/app_edit.aspx";
var save_app_edit_url="ajax/save_app_edit.aspx";
var save_new_team="ajax/save_new_team.aspx";
var load_previous_game="ajax/load_previous_game.aspx";
var setters_url="ajax/setters.aspx";
var app_report_url="ajax/get_report_url.aspx";
var terminate_url="ajax/terminate.aspx";
var loadTeamMembers_url="ajax/loadTeamMembers.aspx";
var update_copy_name_url="ajax/updateCopyName.aspx";
var insert_log_url="ajax/insert_log.aspx";
var users_logs_url="ajax/users_logs.aspx";
var feedback_url="ajax/send_feedback.aspx";
var get_demo_url="ajax/get_demo.aspx";


$.ajaxSetup ({  
    cache: false  
});


$(document).ready(function() {
//        load_news();
//        load_logs();
//        load_apps();
//        set_users_logs();

});


function logoutListen() {
    return;
    $.ajax({
            type: "post",
            url: DATA_PATH+logout_url,
            success: function(data, textStatus, jqXHR) {
                if(data=="0") {
                   logOut();
                }
                else
                    setTimeout("logoutListen()",10000);
                
            }
    }); 
}

var loaderHTML='<div class="loader"><table><tr><td><img src="img/loader.gif" /></td></tr></table></div>';
function load_news() {
    $("#news").html(loaderHTML);
    $.ajax({
            type: "post",
            url: DATA_PATH+news_url,
            success: function(data, textStatus, jqXHR) {
                var HTML='';
                HTML+='<marquee  behavior="scroll" direction="up" scrollamount="1" onmouseover="this.stop();" onmouseout="this.start();">' +
                    data +
                        '</marquee>';
                
                $("#news").html(HTML);
                
            }
    });   
}


function load_logs() {
    $("#logs_ajax").html(loaderHTML);
    $.ajax({
            type: "post",
            url: DATA_PATH+logs_url,
            data:{
                search:$("#search_logs").val(),
                game:$("#logs_game").val(),
                userID:userID
            },
            success: function(data, textStatus, jqXHR) {
                $("#logs_ajax").html(data);
                set_logs_search();
                set_edit_game();
                myScroll = new iScroll('logs_ajax');
                set_app_report();
                set_terminate();
            }
    });   
}

function set_logs_search() {
    $("#search_logs").unbind("keyup");
    $("#search_logs").keyup(function(){
        load_logs();
    });
}

function load_apps() {
    $("#apps_ajax").html(loaderHTML);
    $.ajax({
            type: "post",
            url: DATA_PATH+apps_url,
            data:{
                search:$("#search_app").val(),
                category:$("#app_category").val(),
                userID:userID
            },
            success: function(data, textStatus, jqXHR) {
                $("#apps_ajax").html(data);
                set_apps_search();
                setViewMore();
                setCreateGame();
                setFancyBox();
                setEventLog();
                setWheeldoPopUp();
            }
    });   
}

function set_apps_search() {
    $("#search_app").unbind("keyup");
    $("#search_app").keyup(function(){
        load_apps();
    });
}

function setViewMore() {
   $(".learn_more").unbind("click");
   $(".learn_more").click(function(){
       
       var appID=$(this).attr("app_id");
       var app_loaded=$("#app_loaded_"+appID).val();
       
       
       if(app_loaded==0) {
        $.ajax({
                 type: "post",
                 url: DATA_PATH+app_data_url,
                 data:{
                     appID:appID
                 },
                 success: function(data, textStatus, jqXHR) {
                     var HTML='<div class="learn_more_arrow"></div>';
                     HTML+=data;
                     $("#more_info_"+appID).html(HTML);
                    $("#more_info_"+appID).slideToggle("fast");
                    $("#app_loaded_"+appID).val("1");
                    
                    
                    $(".close_more").unbind("click");
                    $(".close_more").click(function(){
                        $(this).parent().slideUp("fast");
                    });
                 }
         });
       }
       else {
           $("#more_info_"+appID).slideToggle("fast");
       }  
   });
}



var waitForUpdate=true;
var lastKeyUp=0;

function setCreateGame() {
    $(".create_game").html("Create game");
    $(".create_game").css("background-color","#53B710");
    $(".create_game").unbind("click");
    $(".create_game").click(function(){
        var appID=$(this).attr("appID");
        var button_obj=$(this);
        //button_obj.html("Please wait...");
        button_obj.html('<img src="'+DATA_PATH+'img/createAppLoader.gif" alt="Please wait..." />');
        $(".create_game").unbind("click");
        button_obj.removeClass("button_app");
        button_obj.removeClass("button_green");
        button_obj.addClass("createWait");
        button_obj.css("background-color","transparent");

        var as_service=$(this).attr("as_service");
        if(as_service=="1") {
            window.location.href="/#/createGame/"+appID+"/0";
            return;
        }
        

       $.ajax({
                 type: "post",
                 url: DATA_PATH+app_create_url,
                 data:{
                     appID:appID
                 },
                 success: function(data, textStatus, jqXHR) {
                    //myScroll = new iScroll('editFrame_'+appID);
                    $("body").prepend(data);

                    $("#slider_app_"+appID).wheeldoSlider();
                    $("#slider_app_"+appID).showSlide();

                    $(".next").click(function(){
                        $(".next").unbind("click");
                        $("#slider_app_"+appID).next();
                    });
                    
                    $(".prev").click(function(){
                        $("#slider_app_"+appID).prev();
                    });
                    
                    $(".hide").click(function(){
                        load_logs();
                        $("#slider_app_"+appID).hideSlide();
                    });
 
                
                    $(".cancel").click(function(){
                        var copyID=$(this).attr("copyID");
                        $.ajax({
                                 type: "post",
                                 url: DATA_PATH+terminate_url,
                                 data:{
                                   copyID:copyID
                                 },
                                 success: function(data, textStatus, jqXHR) {
                                       $("#slider_app_"+appID).hideSlide();
                                 }
                         }); 
                    });
                    

                    $(".app_name_input").keyup(function(){
                        var copyID=$(this).attr("copyID");
                        var value=$(this).val();
                        var seconds=new Date().getTime();
                        $(this).attr("lastKeyUp",seconds);
//                        if(seconds-lastKeyUp>2000) {
//                            
//                        }
                        lastKeyUp=seconds;
                        

                        $.ajax({
                                 type: "post",
                                 url: DATA_PATH+update_copy_name_url,
                                 data:{
                                   copyID:copyID,
                                   name:value
                                 },
                                 success: function(data, textStatus, jqXHR) {
                                       
                                 }
                         }); 
                    });
                    

                    button_obj.removeClass("createWait");
                    button_obj.addClass("button_app");
                    button_obj.addClass("button_green");
                    setCreateGame();
                    setEventLog();
                    
                    
                    
                    setAutoTXT();
                    setTeamHTML();
                    set_choose_recipients();
                    setSelectTeam();
                    $('#file').customFileInput();
                    init();
                    
                    setPublish();
                    setLoadPreviosGames();
                    set_ready_to_publish();
                    setShowTeamCreateApp();
                    set_terminate();

                 }
         }); 
    });
    
    
    
}



function set_edit_game() {
    $(".edit_game").html("Edit Game");
    $(".edit_game").css("background-color","#55C0EC");
    $(".edit_game").unbind("click");
    $(".edit_game").click(function(){
        var copyID=$(this).attr("copyID");
        var appID=$(this).attr("appID");
        var button_obj=$(this);

        button_obj.html('<img src="'+DATA_PATH+'img/editAppLoader.gif" alt="Please wait..." />');
        $(".edit_game").unbind("click");
        button_obj.removeClass("button_app");
        button_obj.removeClass("button_blue");
        button_obj.addClass("editWait");
        button_obj.css("background-color","transparent");

        var as_service=$(this).attr("as_service");
        if(as_service=="1") {
            window.location.href="/#/editGame/"+appID+"/"+copyID;
            return;
        }
       
       
       
       $.ajax({
                 type: "post",
                 url: DATA_PATH+app_edit_url,
                 data:{
                     copyID:copyID
                 },
                 success: function(data, textStatus, jqXHR) {
                    $("body").prepend(data);
                    $("#slider_app_"+copyID).wheeldoSlider();
                    $("#slider_app_"+copyID).showSlide();
                    
                    $(".next").click(function(){
                        $("#slider_app_"+copyID).next();
                    });
                    
                    $(".prev").click(function(){
                        $("#slider_app_"+copyID).prev();
                    });
                    
                    $(".hide").click(function(){
                        $("#slider_app_"+copyID).hideSlide();
                    });
                    
                    
                    button_obj.removeClass("editWait");
                    button_obj.addClass("button_blue");
                    button_obj.addClass("button_app");
                    set_edit_game();
                    
                    setAutoTXT();
                    set_save_edit();
                 }
         }); 
        
    });
}

function set_save_edit() {
    $(".save_edit").unbind("click");
    $(".save_edit").click(function(){
       var copyID=$(this).attr("copyID");
       var name=$("#game_name").val();
       
       
        $("#slider_app_"+copyID).hideSlide();
        $(".popupWrap").css("background-image"," url(../img/impatient.gif)");
        var proccessingText='Please wait while something...';
        popText(proccessingText);
        
        
        $.ajax({
                 type: "post",
                 url: DATA_PATH+save_app_edit_url,
                 data:{
                   copyID:copyID,
                   name:name
                 },
                 success: function(data, textStatus, jqXHR) {
                        $(".popupWrap").css("background-image","url(../img/chut2.gif)");
                        var proccessingText='Done!';
                        popText(proccessingText);
                        setTimeout("popupClose()",5000);
                        load_logs();
                 }
         });
    });
}



function set_app_report() {
    $(".app_report").unbind("click");
    $(".app_report").click(function(){
        var copyID=$(this).attr("copyID");
        var appID=$(this).attr("appID");
        var as_service=$(this).attr("as_service");
        
        
        if(as_service=="1") {
            window.location.href=DATA_PATH+"#/report/"+appID+"/"+copyID;
            return;
        }
        $.ajax({
                 type: "post",
                 url: DATA_PATH+app_report_url,
                 data:{
                   copyID:copyID
                 },
                 success: function(data, textStatus, jqXHR) {
                        loadAppCopyReport(data);
                 }
         });
    });
}

function set_terminate() {
    $(".terminate").unbind("click");
    $(".terminate").click(function(){
        if(!confirm("Sure?"))
            return;
        var copyID=$(this).attr("copyID");
        $.ajax({
                 type: "post",
                 url: DATA_PATH+terminate_url,
                 data:{
                   copyID:copyID
                 },
                 success: function(data, textStatus, jqXHR) {
                       load_logs(); 
                 }
         });
    });
}


function loadAppCopyReport(data) {
    $(".data").fadeOut("slow",function(){
        $(".reportsData").fadeIn("slow");
        $("#reportFrame").attr("src",data.url);
        
        $("#appReportName").html(data.name);
        $("#startedOn").html(data.startDate);
        $("#playingTeam").html(data.teamName);
        $("#reportAppIcon").attr("src",data.icon);
        var height=$(".right_info").height()-110;
        $("#reportFrame").height(height)
    });
}


function logOut() {
        $.ajax({
                type: "post",
                dataType:"jsonp",
                url: insert_log_url,
                data:{
                    type:"logout"
                },
                complete: function() {
                    window.location.href="/logout";
                }
        }); 
}

function set_users_logs() {
    $(".users_logs").unbind("click");
    $(".users_logs").click(function(){
    $.ajax({
            type: "post",
            url: DATA_PATH+users_logs_url,
            success: function(data, textStatus, jqXHR) {
                   $(".users_logs_data").html(data);
                   sorttable.makeSortable($(".sortable"));
                   $(".data").fadeOut("fast",function(){
                       
                    $(".users_logs_data").fadeIn("fast");
                    
                });
            }
         });
    });
}

function sendFeedback() {
    var feedback=$("#feedback_post").val();
    if(feedback==""||feedback==$("#feedback_post").attr("title")) {
        alert("Please insert your feedback!");
        return;
    }
    
    
    $.ajax({
            type: "post",
            url: DATA_PATH+feedback_url,
            data:{
                feedback:$("#feedback_post").val()
            },
            success: function(data, textStatus, jqXHR) {
                alert("Thank you for your feedback.");
                closeFeedbackAllow=true;
                closeFeedback();
                $("#feedback_post").val("");
                setAutoTXT();
            }
         });
}