$(document).ready(function() {
//        setAutoTXT();
//        setExpendView();
//        setReExpendView();
//        setChangeApp();
//        setChangeGame();
//        setOrgSelect();    
//        setEventLog();
//        set_menagers_panel();
//        setFeedback();
//        setDDMenu();
    
});



function setFancyBox() {
//    alert("set")
//    
    $('.fancybox-media').fancybox({
		openEffect  : 'none',
		closeEffect : 'none',
		helpers : {
			media : {}
		}
	});
        
//    return;
    
     $(".various").fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
}

function setWheeldoPopUp() {
        $(".wheeldoPopUp").click(function(){
            var type=$(this).attr("data-type");
            var player=$(this).attr("player");
            var text='<iframe class="player_iframe" src="'+player+'"></iframe>';
            $("#popUpData").html(text);
            $(".popupWrap").addClass("player");
            poupShow();
            //alert(player);
       }); 
}

function setAutoTXT() {
    $(".autoTxT").unbind("focus");
    $(".autoTxT").unbind("blur");
    $(".autoTxT").each(function (event) {
        var altTXT=$(this).attr("title");
        if($(this).val()=="") {
            $(this).val(altTXT);
            $(this).css({"color":"#8D8D8D","font-style":"italic"});
        }
        
        var type=$(this).attr("type");
        var id=$(this).attr("id");
        
        if(type=="password") {
             document.getElementById(id).setAttribute("type","text");     
        }
        
        $(this).focus(function (event) {
            
            
            
             if($(this).val()==altTXT) {
                 $(this).val("");
                 if(type=="password") {
                     document.getElementById(id).setAttribute("type","password");
                 }
             }
             
             $(this).css({"color":"#000000","font-style":"normal"});
        });
        
        $(this).blur(function (event) {
             if($(this).val()=="") {
                 $(this).val(altTXT);
                 document.getElementById(id).setAttribute("type","text");
                 $(this).css({"color":"#8D8D8D","font-style":"italic"});
             }
        });
    });  
}


function setExpendView() {
    
    $(".expand_view").unbind("click");
    $(".expand_view").click(function(){
       $(".right_info").hide();
       $('#content .data').animate({width:'1100px'}, 800,function(){
           
       });
       
       $('.app_info_wrap').animate({width:'940px'}, 800,function(){
           
       });
       
       $('.more_info').animate({width:'940px'}, 800,function(){
            
       });
       
       $(".re_expand_view").show();
       $("#app_category").css("right","32px");
        
    }); 
}

function setReExpendView() {
    
    $(".re_expand_view").unbind("click");
    $(".re_expand_view").click(function(){
       
       $('#content .data').animate({width:'810px'}, 800,function(){
           $(".right_info").show();
       });
       
       $('.app_info_wrap').animate({width:'650px'}, 800,function(){
           
       });
       
       $('.more_info').animate({width:'650px'}, 800,function(){
            
       });
       
       $(".re_expand_view").hide();
       $("#app_category").css("right","0px");
        
    }); 
}

var app_galleries=[];
function activateExploreApp(appID) {
    //alert(appID);
    $.fancybox.open(
        app_galleries[appID]    
    , {
        padding : 0  ,
        helpers : {
    		title : {
    			type : 'over'
    		}
    	}
    });
}




function getScreen() {
    return $("html").outerWidth()+"_"+$(window).height();
}


var initialScreen=getScreen();
var timer;



function poupShow(close) {
    close = typeof close !== 'undefined' ? close : true;
    
    
    if(!close) 
        $(".popupWrap").find(".close").hide();
    else 
        $(".popupWrap").find(".close").show();
        
    timer=true;
    $("#fadeBg").show();
    poupLocate();
    reLocate();
    
    
    $(".popupWrap").find(".close").unbind("click");
    $(".popupWrap").find(".close").click(function(){
        popupClose();
    });
}

function popupClose() {
    $("#popUpData").html("");
    //$(".popupWrap").css("background-image","url(../img/smileyEmbarsassed.png)");
    $(".popupWrap").removeClass("player");
    $("#fadeBg").hide();
    timer=false;
}

function reLocate() {
    if(getScreen()!=initialScreen) {
        poupLocate();
    }
    
    if(timer) timer = setTimeout(function(){ reLocate(); }, 50);
}


function poupLocate() {
    var htmlWidth=$("html").outerWidth();
    var screenHeight=$(window).height();
   
    /* left calculation */
    var width=$(".popupWrap").outerWidth();
    var popLeft=(htmlWidth/2)-(width/2);
    //////////////////////
    
    /* top calculation */
    var height=$(".popupWrap").outerHeight();
    // decrease 10% //
    
    screenHeight=screenHeight*0.8;
    var popTop=(screenHeight/2)-(height/2);
    if(popTop<5)
        popTop=5;
    /////////////////////
    
    $(".popupWrap").css({"left":popLeft+"px","top":popTop+"px"})  
}




function popText(text) {
    $("#popUpData").html(text);
    poupShow();
}


var new_players=new Array();
function add_new_player() {
    var not_valid=false;
    var name=$("#new_team_player_name").val();
    if(name=="" || name==$("#new_team_player_name").attr("title")) {
        $("#new_team_player_name").addClass("border-red");
        not_valid=true;
    }
    else {
        $("#new_team_player_name").removeClass("border-red");
    }
    
    
    var email=$("#new_team_player_email").val();
    if(!looksLikeMail(email)) {
        $("#new_team_player_email").addClass("border-red");
        not_valid=true;
    }
    else {
        $("#new_team_player_email").removeClass("border-red");
    }
    
    if(not_valid) {
        return;
    }
    
    $("#new_team_player_name").val($("#new_team_player_name").attr("title"));
    $("#new_team_player_email").val($("#new_team_player_email").attr("title"));
    
    
    var new_player=[];
    new_player[0]=name;
    new_player[1]=email;
    new_players.push(new_player);
    
    setTeamHTML();  
}


function setTeamHTML(del_op){
    del_op = typeof del_op !== 'undefined' ? del_op : true;
    $(".team_members").html("");
    var c=0;

    if(new_players.length>0) {
        for(i in new_players) {
            var user=new_players[i];
            if (typeof(user) == "function") {
                continue;
            }
            var HTML='<div class="user_email_box">';
            if(del_op) {
                HTML+='<strong>'+user[0]+'</strong> &lt;'+user[1]+'&gt; <a  href="javascript:del_user('+i+')"><img src="img/del_user.png" /></a>';
            }
            else {
                HTML+='<strong>'+user[0]+'</strong> &lt;'+user[1]+'&gt;'; 
            }
            HTML+='</div>';
            $(".team_members").append(HTML);
            c++;
        }



        if(c>0)
            $(".team_members").append('<br class="clr" />');
    
    }
    $(".team_num").html(c);
}

function del_user(index) {
    
    new_players.splice(index,1);
    $(".add_player").trigger("click");
    setTeamHTML();
}



function looksLikeMail(str) {
    var lastAtPos = str.lastIndexOf('@');
    var lastDotPos = str.lastIndexOf('.');
    return (lastAtPos < lastDotPos && lastAtPos > 0 && str.indexOf('@@') == -1 && lastDotPos > 2 && (str.length - lastDotPos) > 2);
}

function set_choose_recipients() {
    $(".recipients_th").unbind("click");
    $(".recipients_th").click(function(){
        var checked_obj=$(this).find("input[type=radio]");
        checked_obj.prop('checked',true);
        var choosen=checked_obj.val();
        $(".choose_recipients").hide();
        $("."+choosen).show();
        
        
        if(choosen!="exist")
            setTeamHTML();
        else {
            $(".team_members").html("");
            loadTeamMembers();
        }
        
        
    });   
}

function setSelectTeam() {
    var team_data=$("#selected_team").val().split("_");
    $(".team_num").html(team_data[1]);
    
    $("#selected_team").unbind("change");
    $("#selected_team").change(function(){
       var team_data=$(this).val().split("_");
       $(".team_num").html(team_data[1]);
       loadTeamMembers();
    });
}




function loadTeamMembers() {
    var team_data=$("#selected_team").val().split("_");
    if(team_data[0]=="0")
        return;
    var teamID=team_data[0];
    
    $.ajax({
            type: "post",
            dataType:"jsonp",
            url: DATA_PATH+loadTeamMembers_url,
            data:{
                teamID:teamID
            },
            success: function(data, textStatus, jqXHR) {
                
            }
    });
}

function init() {
	document.getElementById('team_upload_form').onsubmit=function() {
		document.getElementById('team_upload_form').target = 'upload_team_target'; //'upload_target' is the name of the iframe
	}
}

function initTeamUpload() {
	document.getElementById('team_upload_form').onsubmit=function() {
		document.getElementById('team_upload_form').target = 'upload_team_target'; //'upload_target' is the name of the iframe
	}
}

function uploadTeamDone() {
    new_players=[];
    //var ret = frames['upload_target'].document.getElementsByTagName("body")[0].innerHTML;
    var ret=$("#upload_team_target").contents().find("body").html();
    
    if(ret=="") {
        
    }
    else {
        new_players=eval(ret);
    }
    setTeamHTML();
}

function set_ajax_load() {
    alert('loaded'); 
    $($('#MainPopupIframe')[0].document).ready(function() {
        alert('loaded');    
    });
   // $("#upload_target").attr("onload","uploadDone()");
}


function checkPublish(copyID,appID) {
        var publish_not_ready=false;
        var dataArray=[];
        var errors=[];
        
        // get parameters //
        
        // game name //
        var game_name=$("#game_name").val();
        if(game_name=="" || game_name==$("#game_name").attr("title")) {
           publish_not_ready=true; 
           errors.push("Game name is missing.");
        }
        dataArray['game_name']=game_name;
        ///////////////
        
        // recipients type //
        var recipients_type=$("input[name='recipients']:checked").val();
        dataArray['recipients_type']=recipients_type;
        /////////////////////
        
        
        
        
        
        
        
        // get team data //
        switch(recipients_type) {
            case "new":
                var team_name=$("#new_team_name").val();
                if(team_name=="" || team_name==$("#new_team_name").attr("title")) {
                    publish_not_ready=true;
                    errors.push("Team name is missing.");
                }
                dataArray['team_name']=team_name;
                if(typeof(new_players)=="undefined" || new_players.length<1) {
                   publish_not_ready=true;
                   errors.push("You must provide at least one recipient.");
                }
                dataArray['new_players']=getNewPlayersStr(); 
            break;
            
            case "file":
                var team_name=$("#file_team_name").val();
                if(team_name=="" || team_name==$("#file_team_name").attr("title")) {
                    publish_not_ready=true;
                    errors.push("Team name is missing.");
                }
                dataArray['team_name']=team_name;
                if(typeof(new_players)=="undefined" || new_players.length<1) {
                   publish_not_ready=true;
                   errors.push("You must provide at least one recipient.");
                }
                dataArray['new_players']=getNewPlayersStr();     
            break;
            
            case "exist":
                //var team_data=$("#selected_team").val().split("_");
                var team_id=$("#selected_team").val();
                if(team_id=="0") {
                    publish_not_ready=true;
                    errors.push("Please select team.");
                }
                dataArray['team_id']=team_id;
                
            break;
        }
        ///////////////////

        
        
        // get email content //
        var email_subject=$("#email_subject").val();
        if(email_subject=="" || email_subject==$("#email_subject").attr("title")) {
           publish_not_ready=true; 
           errors.push("Email subject is missing.");
        }
        dataArray['email_subject']=email_subject;
        
        
        var email_content=$("#email_content").val();
        if(email_content=="" || email_content==$("#email_content").attr("title")) {
           publish_not_ready=true; 
           errors.push("Email content is missing.");
        }
        dataArray['email_content']=JSON.stringify(email_content);;
        
        ///////////////////////
        
        if(publish_not_ready) {
            var errorTXT='Oops... <br />';
            for(i in errors) {
                if (typeof(errors[i]) == "function") {
                    continue;
                }
                errorTXT+=errors[i]+"<br />";
            }
            popText(errorTXT);
            return false;
        }
        else {
            var dataPostArray='';
            var c=0;
            for(i in dataArray) {
                if (typeof(dataArray[i]) == "function") {
                    continue;
                }
                if(c!=0)
                dataPostArray+="$";
                dataPostArray+=i+"|=|"+dataArray[i];
                c++;
            }
            
            return dataPostArray;
        }
}

function setPublish() {
    $(".publish").click(function(){
        var copyID=$(this).attr("copyid");
        var appID=$(this).attr("appid");
        var dataPostArray=checkPublish(copyID,appID);

        
        
        if(!dataPostArray)
            return;

        
        
        $("#slider_app_"+appID).hideSlide();
        //$(".popupWrap").css("background-image"," url(../img/impatient.gif)");
        //var proccessingText='Please wait while processing...';
	var proccessingText='Great job! Your team will love this game';
        popText(proccessingText);
        
        
        $.ajax({
                type: "post",
                url: DATA_PATH+save_game_data,
                data:{
                    copyID:copyID,
                    dataArray:dataPostArray
                },
                success: function(data, textStatus, jqXHR) {
                        alert("publish end");
                        //$(".popupWrap").css("background-image","url(../img/chut2.gif)");
                        var proccessingText='Done!';
                        popText(proccessingText);
                        setTimeout("popupClose()",5000);
                        load_logs();
                        new_players.splice(0, new_players.length);
                        new_players.length=0;
                        frames['upload_target'].document.getElementsByTagName("body")[0].innerHTML="";
                }
        });
        
        
        
        
        
//        $("#wait_text").addClass("save");
//        $("#wait_text").append("Starting proccess... <br />");
//        $("#slider_app_"+appID).next();
//        
//        $("#wait_text").append("Saving game preferences... <br />");
//        $.ajax({
//                type: "post",
//                url: DATA_PATH+save_game_data,
//                data:{
//                    appID:appID,
//                    game_name:game_name
//                },
//                success: function(data, textStatus, jqXHR) {
//                        $("#wait_text").append("Game preferences saved! <br />");
//                }
//        });
        
        
    });
}


function setLoadPreviosGames() {
    $(".load_game").unbind("click");
    $(".load_game").click(function(){
        $(".load_game").unbind("click");
        var load_game_id=$(this).attr("load_game_id");
        var curr_game_id=$(this).attr("curr_game_id");
        var appID=$(this).attr("appID");
        
        var gameBox=$(this).parent();
        
        $.ajax({
                type: "post",
                url: DATA_PATH+load_previous_game,
                data:{
                    load_game_id:load_game_id,
                    curr_game_id:curr_game_id,
                    appID:appID
                },
                success: function(data, textStatus, jqXHR) {
                    var editSRC=$('#editFrame_'+appID).attr('src');
                    var splited=editSRC.split("?t=");
                    //$('#editFrame_'+appID).attr('src', splited[0]+"?t="+time());
                    $('#editFrame_'+appID).attr('src', splited[0]);
                    $(".edit_iframe").html("");
                    //$(".edit_iframe").html('<iframe src="'+splited[0]+"?t="+time()+'" id="editFrame_'+appID+'"></iframe>');
                    $(".edit_iframe").html('<iframe src="'+splited[0]+'" id="editFrame_'+appID+'"></iframe>');
                    gameBox.fadeToggle("slow");
                    setLoadPreviosGames();
                }
        }); 
        
        
        
        
        
    });  
    
    $(".load_previous_game").unbind("click");
    $(".load_previous_game").click(function(){
        $(this).parent().find(".games").fadeToggle("slow");
    });
}


function time () {
  // http://kevin.vanzonneveld.net
  // +   original by: GeekFG (http://geekfg.blogspot.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: metjay
  // +   improved by: HKM
  // *     example 1: timeStamp = time();
  // *     results 1: timeStamp > 1000000000 && timeStamp < 2000000000
  return Math.floor(new Date().getTime() / 1000);
}

var isArray = Array.isArray || function(obj) {
    return !!(obj && obj.concat && obj.unshift && !obj.callee);};


function getNewPlayersStr() {
    var str='';
    var c=0;
    for(var key in new_players) {
        var player=new_players[key];
        var name=player[0];
        var email=player[1];
        var empID=player[2];
        if(c!=0)
            str+="___";
        str+=name+"|"+email+"|"+empID;
        c++;
    }
    return str;
}


function set_ready_to_publish() {
    $(".ready_to_publish_check").unbind("click");
    $(".ready_to_publish_check").click(function(){
        var appID=$(this).attr("appID");
        var copyID=$(this).attr("copyID");
        var game_name=$("#game_name").val();
        if(game_name=="" || game_name==$("#game_name").attr("title")) {
           $("#game_name").addClass("border-red");
           return;
        }
        else {
            $("#game_name").removeClass("border-red");
        }
        
        if(!checkAppEdit())
            return;
        
        $("#slider_app_"+appID).next();
        
        
        var log_type="ready_to_publish_game";
        var log_more="App Name: "+game_name+",CopyID: "+copyID;
        $.ajax({
                type: "post",
                dataType:"jsonp",
                url: DATA_PATH+insert_log_url,
                data:{
                    type:log_type,
                    more:log_more
                },
                success: function(data, textStatus, jqXHR) {

                }
        });
        
    });
}

function setChangeApp() {
    $("#app_category").unbind("change");    
    $("#app_category").change(function(){
        load_apps();
    });
    
}

function setChangeGame() {
    $("#logs_game").unbind("change");    
    $("#logs_game").change(function(){
        load_logs();
    });
    
}

function setOrgSelect() {
    $("#org_select").unbind("change");
    $("#org_select").change(function(){
        var orgID=$(this).val();
        $.ajax({
            type: "post",
            url: DATA_PATH+setters_url,
            data:{
                key:"userOrganizationIdSelect",
                value:orgID,
                table:"users",
                id:userID,
                altIDKey:"userID"
            },
            success: function(data, textStatus, jqXHR) {
                window.location.reload();
            }
    }); 
    });
}


function setShowTeamCreateApp() {
    $(".show_team_members").unbind("click");
    $(".show_team_members").click(function(){
        var buttonObj=$(this);
        var screen=$(this).attr("screen");
        $("."+screen).find(".team_members").slideToggle("fast", function () {
            if($(this).is(":visible")) {
                buttonObj.html("Hide");
            }
            else {
                buttonObj.html("Show");
            }
        });
    });
}

function setEventLog() {
    $(".event_log[log_type]").click(function(){
        var log_type=$(this).attr("log_type");
        var log_more=$(this).attr("log_more");
        $.ajax({
                type: "post",
                dataType:"jsonp",
                url: DATA_PATH+insert_log_url,
                data:{
                    type:log_type,
                    more:log_more
                },
                success: function(data, textStatus, jqXHR) {

                }
        });
    });
}

function set_menagers_panel() {
    $("#menager_panel").find(".open_panel").unbind("click");
    $("#menager_panel").find(".open_panel").click(function(){
        if($("#menager_panel").hasClass("open")) {
            $( "#menager_panel" ).removeClass("open");
            $("#menager_panel").find(".content").slideUp("fast");
        }
        else {    
            $( "#menager_panel" ).addClass("open");
            $("#menager_panel").find(".content").slideDown("fast");
        }
        
    });
}


var closeFeedbackAllow=true;
function setFeedback() {
//    $("#feedback").hover(
//        function () {
//           openFeedback();
//        },
//        function () {
//          closeFeedback();
//    });

    $("#feedback").click(function(){
        openFeedback();
    });

    $("#feedback_post").focus(function(){
        closeFeedbackAllow=false;
    });
    
    $("#feedback_post").keyup(function(){
        closeFeedbackAllow=false;
    });
    
    $("#feedback").find(".close").click(function(){
        closeFeedbackAllow=true;
        closeFeedback();
    });
    
    
    $("#feedback_post").keyup(function(){
        closeFeedbackAllow=false;
    });
    
    $("#feedback").find(".post").click(function(){
        sendFeedback();
    });
}

function openFeedback() {
    if(!closeFeedbackAllow)
        return;
    
    $("#feedback").unbind("click");
    
    closeFeedbackAllow=false;
     $( "#feedback" ).find(".title").hide();
     $( "#feedback" ).animate({
        width: "350"
        }, 100, function() {
            
           $( "#feedback" ).find(".form").show();
           closeFeedbackAllow=true;
    });
}




function closeFeedback() {
    if(!closeFeedbackAllow)
        return;
    $( "#feedback" ).find(".form").hide();
     $( "#feedback" ).animate({
        width: "40"
        }, 100, function() {
            $( "#feedback" ).find(".title").show();
            $("#feedback").click(function(){
                openFeedback();
            });
    });
}


function setDDMenu() {
    return;
    $(".dd_wheeldo").each(function(){
    
        var triggerObj=$(this);
        var contObj=$(this).find(".ddMenuCont");
        //contObj.width(triggerObj.outerWidth()-6);
        //contObj.css({"top":triggerObj.height()+"px"});
        
        //triggerObj.prepend('<a class="trigger" href="javascript:void(0)"></a>');
        var triggerA=triggerObj.find(".trigger");
        //triggerA.width(triggerObj.outerWidth());
        //triggerA.height(triggerObj.height());
        
        triggerA.click(function(){
            contObj.toggle();
        });
    });
    
    $(".ddMenuTrigger").each(function(){
        var triggerObj=$(this);
        var contObj=$(this).find(".ddMenuCont");
        contObj.width(triggerObj.outerWidth()-6);
        contObj.css({"top":triggerObj.height()+"px"});
        
        triggerObj.prepend('<a class="trigger" href="javascript:void(0)"></a>');
        var triggerA=triggerObj.find(".trigger");
        triggerA.width(triggerObj.outerWidth());
        triggerA.height(triggerObj.height());
        
        triggerA.click(function(){
            contObj.toggle();
        });
    });

    $('html').click(function(e) {
        if(!$(e.target).hasClass("ddMenuCont") && (!$(e.target).hasClass("ddMenuTrigger") || !$(e.target).hasClass("dd_wheeldo")) && !$(e.target).hasClass("trigger") && !$(e.target).parent().hasClass("ddMenuCont") && !$(e.target).parent().parent().hasClass("ddMenuCont")) {
            $(".ddMenuCont").hide();
        }
    });

}


function remove_all_events() {
    $("html").unbind();
    $("body").unbind();
    $("div").unbind();
    $("a").unbind();
    $("p").unbind();
    $("span").unbind();
    $("ul").unbind();
    $("li").unbind();
    $("input").unbind();
    $("textarea").unbind();
    $("button").unbind();
    
    
}

function setCheckAll() {
    $(".check_all").click(function(){
        var checked=$(this).is(":checked");
        if(checked) {
            $(".row_checker").attr('checked', true);
        }
        else {
            $(".row_checker").attr('checked', false);
        }
    });
}

function wait() {    
    $(".wait").show();
    $(".fadeBg").show();
}

function stopWait() {
    $(".wait").hide();
    $(".fadeBg").hide();
}

function getDemo(appID) {
    wait();
    // send data to app and get the link //
    $.ajax({
            type: "post",
            url: DATA_PATH+get_demo_url,
            data:{
                appID:appID
            },
            success: function(data, textStatus, jqXHR) {
                OpenInNewTab(data)
                //
            }
    });
}

function OpenInNewTab(url){
//    alert(url);
    $("#demoIframe").attr("src",url);
    $( ".playNow" ).dialog({ 
        height: 750,
        width: 1050,
        closeOnEscape: true,
        open: function( event, ui ){
            $(".fadeBg").show();
        },
        close: function( event, ui ) {
            $("#demoIframe").attr("src","");
            stopWait();
        }
    });
    return;
  //window.open(url,'_newtab');  
//  var win=window.open(url, '_blank');
//  win.focus();
    //console.log(url);
    poptastic(url,1300,800);
}


var newwindow;
function poptastic(url,width,height,changeable)
{
    changeable = typeof changeable !== 'undefined' ? changeable : false;
    if(changeable)
        newwindow=window.open(url,'name','height='+height+',width='+width+',left=150,top=50');
    else
        newwindow=window.open(url,'name','height='+height+',width='+width+',left=150,top=50,directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,scrollbars=no,resizable=no');
        

    if (window.focus) {newwindow.focus()}
}



function setSaving() {
    $(".saving").fadeIn(450);
    $(".wait").fadeIn(250);
}

function savingDone() {
    $(".saving").fadeOut(450);
    $(".wait").fadeOut(250);
}


function onFrmaeLoad() {
    var ret = frames[$("#file_upload_form").attr("target")].document.getElementsByTagName("body")[0].innerHTML;
    $(".main_image").attr("src",ret);
    $(".loadImage").hide();
    $(".main_image").show();
    $(".image_wrap div.info").show();
    $("#img_bitmap").val(ret);
    $("#img_link").val("");
}

function onOrgLogoLoad() {
    var ret = frames[$("#file_upload_form").attr("target")].document.getElementsByTagName("body")[0].innerHTML;
    $(".main_image").attr("src",ret);
    $(".loadImage").hide();
    $(".main_image").show();
    $(".image_wrap div.info").show();
    $("#organizationImg").val(ret);
}


function onUploadDiscussionGroupFrmaeLoad(fileName) {
    $("#forigin").val(fileName);
    $(".loadImage").hide();
    autoSave();
}


function timeConverter(UNIX_timestamp){
 var a = new Date(UNIX_timestamp*1000);
 var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
     var year = a.getFullYear();
     var month = months[a.getMonth()];
     var date = a.getDate();
     var hour = a.getHours();
     var min = a.getMinutes() < 10 ? "0"+a.getMinutes():a.getMinutes();
     var sec = a.getSeconds();
     var time = date+','+month+' '+year+' '+hour+':'+min ;
     return time;
 }
 
 
 
alert_system = function(text) {
 $("#wait").hide();
 if($("#alert").length > 0) {
     return;
 }
 $("body").append('<div id="alert"><div id="message">'+text+' <button type="button" class="ok" onclick="$(\'#alert\').remove()">Ok</button></div></div>');
 var h = $("#message").outerHeight();
 var w = $("#message").outerWidth();
 $("#message").css({
     "margin-top":"-" + h/1.2 + "px",
     "margin-left":"-" + w/2 + "px"
 });

};


function updateTokensCounter() {
    $.ajax({
            type: "post",
            url: DATA_PATH+'gt',
            data:{
               op:"getTokensLeft" 
            },
            success: function(data, textStatus, jqXHR) {
                //alert(data.tokens);
                if(data.tokens==1) {
                    $(".tokens").addClass("one");
                }
                
                $(".token_c").html(data.tokens);
                if(data.tokens==0) {
                    $(".tokens").addClass("max");
                }
            }
    });
}

