function loginScripts() {
    $(document).ready(function(){
        $("#submit_login").unbind("click");
        $("#submit_login").click(function(){
            $.ajax({
		url: '/index.php/login/loginCheck',
		type: "post",
                dataType: 'json',
		data: {
                    email:$("#email").val(),
                    password:$("#password").val()
                },
		success: function(data){
                    if(data.status=="ok") {
                        window.location.href="/";
                    }
                    else {
                        data.error?throw_error(data.error):throw_error(false);
                    }
		},
		error:function(data){
		}
	  });
        });
    });
}

function throw_error(error) {
    $("#em_data").html(error);
    $(".alert").show();

}


if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
         for (var i = (start || 0), j = this.length; i < j; i++) {
             if (this[i] === obj) { return i; }
         }
         return -1;
    }
}


function isArray(h){
    if((h.length!=undefined&&h[0]!=undefined)||(h.length===0&&h[0]===undefined)){
        return true;
    }
    else{ return false; }
}


function test_functions() {
    $(document).ready(function(){
        $(".check_func").unbind("click");
        $(".check_func").click(function(){
            var func=$(this).attr("func");
            var data={};
            
            $("."+func).each(function(){
                var key=$(this).attr("name");
                data[key]=$(this).val();
            });
            
            data.func=func;
            $("."+func+"_res").removeClass("success");
            $("."+func+"_res").removeClass("error");
            
            
            $.ajax({
		url: '/index.php/bluesnap/test_func',
		type: "post",
                dataType: 'json',
		data: data,
		success: function(data){
                    console.log(data);
                    if(data.status=="ok") {
                        $("."+func+"_res").addClass("success");
                        $("."+func+"_res").html(data.res);
                    }
                    else {
                        $("."+func+"_res").addClass("error");
                        $("."+func+"_res").html(data.error);
                    }
		},
		error:function(data){
                    
		}
	  });

        });
    });
}