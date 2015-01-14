var insert_log_url="ajax/insert_log.aspx";

$(document).ready(function() {
        setAutoTXT();
         $("#loginForm").keypress(function(e) {
                if(e.which == 13) {
                    loginCheck();
                }
            });
            
            $("#password_recovery").hide();
});

function setAutoTXT() {
    $(".systemInput").unbind("focus");
    $(".systemInput").unbind("blur");
    $(".systemInput").each(function (event) {
        var obj=$(this);
        var altTXT=$(this).attr("title");
        var type=$(this).attr("type");
        var id=$(this).attr("id");
        
        
        
        var tooltipHTML='<div class="tootltip_'+id+'" style="position:absolute;padding-left:10px;width:'+(obj.outerWidth()-5)+'px;height:'+obj.outerHeight()+'px;line-height:'+obj.outerHeight()+'px;z-index:1;background-color:transparent;font-style: italic;color: #8D8D8D;font-size:13px;">';
        tooltipHTML+=altTXT;
        tooltipHTML+='</div>';
        
        obj.parent().prepend(tooltipHTML);
        
        
        if($(this).val()!="") {
            $(".tootltip_"+id).hide();
            $(this).addClass("ok");
        }
        else {
            $(".tootltip_"+id).show();
            $(this).removeClass("ok");
        }
        
        
        
        $(this).keyup(function(){
            if($(this).val()!="") {
                $(".tootltip_"+id).hide();
                $(this).addClass("ok");
            }
            else {
                $(".tootltip_"+id).show();
                $(this).removeClass("ok");
            }
        });
        
        
        
        $(this).change(function(){
            if($(this).val()!="") {
                $(".tootltip_"+id).hide();
                $(this).addClass("ok");
            }
            else {
                $(".tootltip_"+id).show();
                $(this).removeClass("ok");
            }
        });
        
        $(this).blur(function(){
            if($(this).val()!="") {
                $(".tootltip_"+id).hide();
                $(this).addClass("ok");
            }
            else {
                $(".tootltip_"+id).show();
                $(this).removeClass("ok");
            }
        });

    });  
}

function loginCheck() {    
    var email=$("#email").val();
    var password=$("#password").val();
    var remember_me=$("#remember_me").is(":checked");
    
    var hash="";
    if(window.location.hash) {
        hash=window.location.hash;
    }
    
    var formOK=true;
    var errors=[];
    
    
    
    var filter_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!filter_email.test($("#email").val())) {
        formOK=false;
        errors.push("Please insert a valid email address!");
    }
    
    
    if(password.length<6) {
        formOK=false;
        errors.push("Password minimum length is 6!");
    }
    
    
    $(".notes").html("");
    for(i in errors) {
        var note=errors[i];
        $(".notes").append("<div>*"+note+"</div>");
    }
    
    
    if(formOK) {
        $.ajax({
                type: "post",
                dataType:"jsonp",
                url: "/ls",
                data:{
                    email:email,
                    password:password,
                    remember_me:remember_me,
                    hash:hash
                },
                success: function(data, textStatus, jqXHR) {

                }
        }); 
    }
}



function moveToSystem() {
        var hash="";
        if(window.location.hash) {
            hash=window.location.hash;
        }
        $.ajax({
                type: "post",
                dataType:"jsonp",
                url: insert_log_url,
                data:{
                    type:"login"
                },
                complete: function() {
   
                    window.location.href=address+hash;
                }
        }); 
}

function password_recovery() {
    
     $('#loginForm').fadeOut('fast', function() {
        $('#password_recovery').fadeIn("slow");
        $('#password_recovery').css("opacity","1");
    }); 
}

function login() {
   $('#password_recovery').fadeOut('fast', function() {
        $('#loginForm').fadeIn("slow");  
    });  
}

function recoveryCheck(){
   var email=$("#email_recovery").val();
    var formOK=true;
    var errors=[];
    
    
    
    var filter_email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!filter_email.test($("#email_recovery").val())) {
        formOK=false;
        errors.push("Please insert a valid email address!");
    }

    
    
    $(".notes_recovery").html("");
    for(i in errors) {
        var note=errors[i];
        $(".notes_recovery").append("<div>*"+note+"</div>");
    }
    
    
    if(!formOK)
        return;
    
    $(".hideRecover_button").hide();
    $(".wait").show();
    if(formOK) {
        $.ajax({
                type: "post",
                dataType:"jsonp",
                url: "/rp",
                data:{
                    email:email
                },
                complete: function(data, textStatus, jqXHR) {
                    $(".hideRecover_button").show();
                    $(".wait").hide();
                }
        }); 
    }
   
}