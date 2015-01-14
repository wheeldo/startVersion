var news_url="ajax/news.html";
var logs_url="ajax/logs.html";
var apps_url="ajax/apps.html";
var app_data_url="ajax/app_data.html";
var app_create_url="ajax/app_create.php";
var save_game_data="ajax/save_game_data.php";
var save_new_team="ajax/save_new_team.php";


$.ajaxSetup ({  
    cache: false  
});


$(document).ready(function() {
        load_news();
        load_logs();
        load_apps();
});

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
                search:$("#search_apps").val(),
                category:$("#app_category").val(),
                userID:userID
            },
            success: function(data, textStatus, jqXHR) {
                $("#apps_ajax").html(data);
                set_apps_search();
                setViewMore();
                setCreateGame();
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


function setCreateGame() {
    $(".create_game").unbind("click");
    $(".create_game").click(function(){
       var appID=$(this).attr("appID");
       
       $.ajax({
                 type: "post",
                 url: DATA_PATH+app_create_url,
                 data:{
                     appID:appID
                 },
                 success: function(data, textStatus, jqXHR) {
                    $("body").prepend(data);
                    $("#slider_app_"+appID).wheeldoSlider();
                    $("#slider_app_"+appID).showSlide();
                    
                    $(".next").click(function(){
                        $("#slider_app_"+appID).next();
                    });
                    
                    $(".prev").click(function(){
                        $("#slider_app_"+appID).prev();
                    });
                    
                    $(".hide").click(function(){
                        $("#slider_app_"+appID).hideSlide();
                    });
                    
                    
                    setAutoTXT();
                    setTeamHTML();
                    set_choose_recipients();
                    setSelectTeam();
                    $('#file').customFileInput();
                    init();
                    setPublish();
                 }
         }); 
    });
    
    
    
}