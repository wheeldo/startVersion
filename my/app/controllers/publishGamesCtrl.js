var publishGamesCtrl = function ($scope,   $modalInstance, f_data) {
    
   $scope.teamsList=f_data.teamsList;
   $scope.selectedTeam=0;
   
   $scope.appID=f_data.appID;
   $scope.copyID=f_data.copyID;
   $scope.copyData=f_data.copyData;
   
   
   
   $scope.initCKEmail = function(id) {
        $( '#' +id).ckeditor({
                    customConfig: DATA_PATH+"vendor/ckeditor/config_min.js"
                                }); 
        var editor = CKEDITOR.instances[id];
        editor.on('blur', function(event) {
            //$scope.editData.game.instructions=CKEDITOR.instances.email_content.getData();
        });
        
        
        
        

        


    };
    
    $scope.email_lang_changed = function() {
//        CKEDITOR.remove(CKEDITOR.instances.email_content);
//        var lang=$scope.email_langs[$("#email_lang").val()].code;
//        if(lang=="he") {
//            CKEDITOR.config.customConfig = DATA_PATH+"vendor/ckeditor/config_min_he.js"
//        }
//        else {
//            CKEDITOR.config.customConfig = DATA_PATH+"vendor/ckeditor/config_min.js"
//        }
//        
//        CKEDITOR.replace('email_content');
    };
    
    
    
    
    
   
   
//   $scope.email_langs={
//     en:"English",
//     he:"Hebrew"
//   };
   $scope.email_langs=[
       {
           code:"en",
           name:"English"
       },
       {
           code:"he",
           name:"Hebrew"
       }
   ];
   
   $scope.email_lang=$scope.email_langs[0].code;
   
   
   var res_tokens=getTokensLeft();
   var max_tokens=res_tokens.tokens;
   
   
   //console.log(f_data);
   
   $scope.new_player = {
       name:"",
       email:""
   };

    if(hateIe) {

    }
    

    
    
    $scope.initCtrl = function() {
        setTeamHTML();
        set_choose_recipients();
        $('#file').customFileInput();
        setShowTeamCreateApp();
        initTeamUpload();
    };
    
    $scope.initSelect = function() {
        var mainObj=$(".custom_select")
        var labelObj=$(".custom_select").find(".label");
        var openObj=$(".custom_select").find(".open_a");
        var optionsWrapObj=$(".custom_select").find(".options_wrap");
        var optionsObj=$(".custom_select").find(".option");
        
        var css=mainObj.attr("custom_select");
        
        openObj.click(function(){

            optionsWrapObj.toggle();
        });

        
        optionsObj.click(function(){
            var data_label=$(this).attr("data_label");
            var data_value=$(this).attr("data_value");
            labelObj.find(".text").html(data_label);
            selectedTeam=data_value;
            optionsWrapObj.hide();
            setSelectTeamChange();
        });
        
        function handler( event ) {
            var target = $( event.target );
            if ( !target.is(".label") &&  !target.is(".custom_select") && !target.is(".option") && !target.is(".open_a")) {
                optionsWrapObj.hide();
            }
        }
        $( "body" ).click( handler );
        //alert(css);
    };

    
    
    
    $scope.cancel = function () {
        savingDone();
        $("#saveAndPublishButton").removeClass("disabled");
        $modalInstance.dismiss('cancel');
    };
    
    
    setSelectTeamChange = function () {
        
        
        

        var value=selectedTeam;
        
        var team_data=value.split("_");
        $(".team_num").html(team_data[1]);
        $("#selected_team").val(team_data[0]);
        
        setLimitTokens(team_data[1]);
        
        $.ajax({
            type: "post",
            dataType:"jsonp",
            url: DATA_PATH+loadTeamMembers_url,
            data:{
                teamID:team_data[0]
            },
            success: function(data, textStatus, jqXHR) {
            }
        });

    };
    
    $scope.del_user2 = function(index) {
        new_players.splice(index,1);
        setLimitTokens(new_players.length);
        setTeamHTML();
    };
    
    $scope.addPlayer = function() {
        setTimeout(function(){
            setLimitTokens(new_players.length);
        },200);
       
    };

    setLimitTokens = function(want_to_use) {
        if(tm) {
            $("#publish_button").attr("disabled",false);
            
            var h='Publish and use '+want_to_use+' '+(want_to_use>1?"tokens":"token");
            
            if(want_to_use>max_tokens) {
                 h="You dont have enough tokens";
                 $("#publish_button").attr("disabled",true);
             }
             
            $("#publish_button").html(h);
        }
    };
    
    
    $scope.publish = function() {
        var email_lang=$("#email_lang").val();
        //  check for tokens:
        want_to_use=new_players.length;
        if(tm && want_to_use>max_tokens) {
            setLimitTokens(want_to_use);
            return; 
        }
        
        /////////////////////
        
        
        var dataPostArray=checkPublish($scope.copyID,$scope.appID);
        
        
//        console.log(dataPostArray);
//        
//        return;

        if(!dataPostArray)
            return;
        
        $(".wait_for_publish").show();
        
       
        var publishRes="sdsd";
        
        
        
        
        $.ajax({
                type: "post",
                url: DATA_PATH+save_game_data,
                data:{
                    copyID:$scope.copyID,
                    dataArray:dataPostArray,
                    email_lang:email_lang
                },
                success: function(data, textStatus, jqXHR) {
                        insertLog("Publish","Copy: "+$scope.copyID);
                        setTimeout("publishDone()",3000);

                }
        });
        //$modalInstance.dismiss('cancel');
    };
    
    publishDone = function() {
        $(".wait_for_publish").hide();
        savingDone();
        $modalInstance.dismiss('cancel');
        updateTokensCounter();
        window.location.href="/#/";
    };

    
};



