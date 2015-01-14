$(document).ready(function() {
        setAutoTXT();
        setExpendView();
        
});


function setAutoTXT() {
    $(".autoTxT").unbind("focus");
    $(".autoTxT").unbind("blur");
    $(".autoTxT").each(function (event) {
        var altTXT=$(this).attr("title");
        if($(this).html()=="") {
            $(this).val(altTXT)
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
        });
        
        $(this).blur(function (event) {
             if($(this).val()=="") {
                 $(this).val(altTXT);
                 document.getElementById(id).setAttribute("type","text");
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


var new_players=[];
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

function setTeamHTML(){
    $(".team_members").html("");
    var c=0;
    for(i in new_players) {
        var user=new_players[i];

        var HTML='<div class="user_email_box">' +
            '<strong>'+user[0]+'</strong> &lt;'+user[1]+'&gt; <a href="javascript:del_user('+i+')"><img src="img/del_user.png" /></a>' +
        '</div>';
        $(".team_members").append(HTML);
        c++;
    }
    
    if(c>0)
        $(".team_members").append('<br class="clr" />');
    
    
    $(".team_num").html(c);
}

function del_user(index) {
    new_players.splice(index,1);
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
    });   
}

function setSelectTeam() {
    var team_data=$("#selected_team").val().split("_");
    $(".team_num").html(team_data[1]);
    
    $("#selected_team").unbind("change");
    $("#selected_team").change(function(){
       var team_data=$(this).val().split("_");
       $(".team_num").html(team_data[1]);
    });
}


function init() {
	document.getElementById('file_upload_form').onsubmit=function() {
		document.getElementById('file_upload_form').target = 'upload_target'; //'upload_target' is the name of the iframe
	}
}

function uploadDone() {
    var ret = frames['upload_target'].document.getElementsByTagName("body")[0].innerHTML;
    new_players=eval(ret);
    setTeamHTML();
}

function set_ajax_load() {
    alert('loaded'); 
    $($('#MainPopupIframe')[0].document).ready(function() {
        alert('loaded');    
    });
   // $("#upload_target").attr("onload","uploadDone()");
}

function setPublish() {
    $(".publish").click(function(){
        var appID=$(this).attr("appID");
        
        
        
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
        
        
        if(publish_not_ready) {
            alert("not ready yet...");
            return;
        }
        
        
        $("#wait_text").addClass("save");
        $("#wait_text").append("Starting proccess... <br />");
        $("#slider_app_"+appID).next();
        
        $("#wait_text").append("Saving game preferences... <br />");
        $.ajax({
                type: "post",
                url: DATA_PATH+save_game_data,
                data:{
                    appID:appID,
                    game_name:game_name
                },
                success: function(data, textStatus, jqXHR) {
                        $("#wait_text").append("Game preferences saved! <br />");
                }
        });
        
        // get team data //
        switch(recipients_type) {
            case "new":
                var team_name=$("#new_team_name").val();
                if(team_name=="" || team_name==$("#new_team_name").attr("title")) {
                    publish_not_ready=true;
                    errors.push("Team name is missing.");
                }
                dataArray['team_name']=team_name;
                if(typeof(new_players)=="undefined" || new_players.length>0) {
                   publish_not_ready=true;
                   errors.push("You must provide at least one recipient");
                }
                dataArray['new_players']=new_players;  
                
                
                
                $("#wait_text").append("Saving new team data... <br />");
                $.ajax({
                        type: "post",
                        url: DATA_PATH+save_new_team,
                        data:{
                            appID:appID,
                            new_players:new_players
                        },
                        success: function(data, textStatus, jqXHR) {
                            $("#wait_text").append("New team data saved! <br />");
                            $("#wait_text").removeClass("save");
                        }
                });
                
                
            break;
            
            case "exist":
                var team_data=$("#selected_team").val().split("_");
                var team_id=team_data[0];
                if(team_id=="0") {
                    publish_not_ready=true;
                    errors.push("Select team.");
                }
                dataArray['team_id']=team_id;
                
            break;
        }
        ///////////////////
        console.log(errors);
        console.log(dataArray);
        
        
        
        
        
        
        
    });
}










