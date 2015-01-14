//This controller retrieves data from the customersService and associates it with the $scope
//The $scope is ultimately bound to the customers view

app.controller('gameboardController', function ($scope,$http, WheeldoService) {

    //I like to have an init() for controllers that need to perform some initialization. Keeps things in
    //one place...not required though especially in the simple example below
    init();
    var apps;
    
    
    function getApps() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST', apps_url, false);  // `false` makes the request synchronous
        request.send(null);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    }
    $scope.runIntro=0;
    function init() {
    }
    
     
     
    
    $scope.runIntro = function() {
        //setWheeldoPopUp();
        //$(".system_demo").trigger("click");
    };
    
    $scope.apps_test=[
        1,2,3,4
    ];
    
    $scope.str_test="test string";
    
    $scope.init = function () {
        //remove_all_events();
        //load_news();
        load_logs();
        load_apps();
        set_users_logs();
        setAutoTXT();
        setExpendView();
        setReExpendView();
        setChangeApp();
        setChangeGame();
        setOrgSelect();    
        setEventLog();
        set_menagers_panel();
        setFeedback();
        setDDMenu();
        setWheeldoPopUp();
        logoutListen();
    };
 
});


app.controller('purchaseController', function ($scope, $http, WheeldoService, $modal , $filter) {
    $scope.purchase_init = function() {
      $('#alert').remove()  
    };
    
    var userFull=getUserFullDetails();
    
    $scope.buyNow = function(id) {
        var parameters={};
        parameters.contractId=id;
        parameters.firstName=userFull.firstName;
        parameters.lastName=userFull.lastName;
        parameters.companyName=userFull.companyName;
        parameters.email=userFull.email;
        parameters.currency='USD';
        parameters.custom1=userFull.orgID;
        parameters.contractId=id;
        
        
        var req='';
        var c=0;
        for(p in parameters) {
            if(c!=0)
                req+='&';
            
            req+=p+"="+parameters[p];
            c++;
        }

        var url="https://www.plimus.com/jsp/buynow.jsp?"+req;
        window.location.href=url;
    };

});


app.controller('teamsController', function ($scope, $http, WheeldoService, $modal , $filter) {
    var total_users;
    var users;
    
    $scope.pager_min=0;
    $scope.pager_max=66;
    $scope.users_loaded=false;


    $scope.init = function () {
        //remove_all_events();
        loadTeams();
        setTeamNameByID();
        setFeedback();
        total_users=getTotal();
        $scope.total_users=total_users;
        $scope.reverse=true;
        $scope.finish_load='loaded';
        $scope.sortBy('name');  
        loadUsers();
        set_ddMenuCont(); 
        
        $.ajax({
            url: DATA_PATH+'app/controllers/addTeamCtrl.js',
            dataType: "script"
        });
        
        $.ajax({
            url: DATA_PATH+'app/controllers/csvUploaderCtrl.js',
            dataType: "script"
        });
    };
    
    
    var teamNameByID={};
    loadTeams = function() {
        $scope.teams=getTeamsListNoC();
        setTeamNameByID();
    };
    
    
    setTeamNameByID = function() {
        teamNameByID={};
        for(i in $scope.teams) {
            var team=$scope.teams[i];
            teamNameByID[team.teamID]=team.teamName;
        }
    };
    
    
    var selectedUsers=[];
    var checkAll=false;
    
    $scope.selectUser = function(index) {
        //alert(index);
        updateChecked();
    };
    
    updateChecked = function() {
        var testAr=$filter('filter')($scope.users, {checked:true});
        $scope.current_check=testAr.length;
        return testAr.length;
    };
    
    $scope.selectAllEmployees = function() {
        var check=!(updateChecked()==$scope.users.length);
        for(i in $scope.users) {
            $scope.users[i].checked=check;
            
        }
        $(".check_all").attr("checked",check);
        updateChecked();
        
    };
    
    
    
//    $scope.$watch('search', function(newVal, oldVal) {
//      console.log("new value in filter box:", newVal);
//      
//      var filteredArray = $filter('filter')($scope.users, newVal);
//      console.log(filteredArray);
//    });
    
    $scope.checkAll = function() {
        checkAll=!checkAll;
        
        var filteredArray = $filter('orderBy')($scope.users, $scope.predicate);
        filteredArray =  $filter('filter')(filteredArray, $scope.search);
        filteredArray =  $filter('filter')(filteredArray, $scope.filterMultiple);
        filteredArray =  $filter('limitTo')(filteredArray, $scope.limitMax);
        filteredArray =  $filter('limitTo')(filteredArray, $scope.users_per_page);

        checkAll=$(".check_all").is(":checked");
        
        for(i in filteredArray) {
            var id=filteredArray[i].id;
            filteredArray[i].checked=checkAll;
        }
        updateChecked();
        
        if(checkAll) {
            $("#multi_check").slideDown("slow");
        }

        
    };
    
    $scope.hideSelectAll = function() {
        $("#multi_check").slideUp("fast");
    };
    
    $scope.setTeamsDD = function() {
       $(".dropdown-menu").on("click", function(e){
            e.stopPropagation();
        });
    };
    
    applaySP = function() {
        //$('.scroll-pane').jScrollPane();
    };
    
    $scope.setScrollPane = function() {
        setTimeout(applaySP);
    }
    
    $scope.check_assign_team = function(index) {
        if($scope.teams[index].check_status==1) 
            $scope.teams[index].check_status=0;
        else 
            $scope.teams[index].check_status=1;
    };
    
    $scope.updateTeamsInAssigmant = function() {
        var userMarked=$filter('filter')($scope.users, {checked:true});
        var teamsRes={};
        for(i in userMarked) {
            var teams=userMarked[i].teams;
            for(j in teams){
                var id=teams[j].id;
                if(teamsRes[id]) 
                    teamsRes[id]++;
                else 
                    teamsRes[id]=1;
            }
        }
        
        // reset marks:
        for(i in $scope.teams) {
            $scope.teams[i].check_status=0;
        }
        
        
        
        for(k in teamsRes) {
            // mark full:
            if(teamsRes[k]==userMarked.length) {
                markTeam(k,1);
            }
            else {
                markTeam(k,2);
            }
        }
    };
    
    markTeam = function(teamID,status) {
        for(i in $scope.teams) {
            if($scope.teams[i].teamID==teamID) {
                $scope.teams[i].check_status=status;
            }
        }
    };
    
    $scope.applyAssignTeams = function() {
        var data={};
        data['users']=[];
        data['keep']=[];
        data['set']=[];
        
        var selctedAr=$filter('filter')($scope.users, {checked:true});
        for(i in selctedAr) {
            data['users'].push(selctedAr[i].id);
        }
        
        for(i in $scope.teams) {
            var team=$scope.teams[i];
            if(team.check_status==1) {
                data['set'].push(team.teamID);
            }
            
            if(team.check_status==2) {
                data['keep'].push(team.teamID);
            }
        }

        var res=setTeamOp("assignTeams",makeDataReadyToSend(data));
        if(res.status=="ok") {
          applyAssignInObject(data);
           setTimeout(closeTeams);
          if(res.deleteFile)
              updateCacheFile();
        }
        else {
            alert("Error!");
        }
    };
    
    applyAssignInObject = function(data) {
        var indexesToApply=[];
        for(i in $scope.users) {
            var user=$scope.users[i];
            if(data.users.indexOf(user.id)>-1) {
                indexesToApply.push(i);
            }
        }

        
        for(j in indexesToApply) {
            
            var index=indexesToApply[j];
            var teams=$scope.users[index].teams;
            // delete unkeeped team:
            
            for(k in teams) {
                var team=teams[k];
                if(data.keep.indexOf(team.id)<0) {
                    $scope.users[index].teams.splice(k, 1);
                }
            }
            
            var existingTeamsForUser=[];
            for(m in $scope.users[index].teams) {
                var team=$scope.users[index].teams[m];
                existingTeamsForUser.push(team.id);
            }
            
            // assign new teams:
            for(l in data.set) {
                var teamID=data.set[l];
                var teamName=teamNameByID[teamID];
                if(data.keep.indexOf(teamID)<0 && existingTeamsForUser.indexOf(teamID)<0) {
                    $scope.users[index].teams.push({id:teamID,name:teamName});
                }
                
            }
        }    
    };
    
    $scope.deleteTeam = function(teamID,teamName) {
        if(!confirm('Delete "'+teamName+'"?'))
            return;
        
        var data={};
        data.teamID=teamID;
        var res=setTeamOp("deleteTeam",makeDataReadyToSend(data));
        if(res.status=="ok") {
          deleteTeamFromScope(teamID);
          if(res.deleteFile)
              updateCacheFile();
        }
        else {
            alert("Error!");
        }
    };
    
    $scope.addNewTeam = function() {
        setTimeout(closeTeams);
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/addTeam.html?t='+t,
          keyboard:true,
          backdrop:'static',
          windowClass: 'addTeamModal',
          controller: addTeamCtrl,
          resolve: {
              f_data:function () {
                return 0;
              }
          }
        });
          
        modalInstance.result.then(function (new_team) {
            createNewTeam(new_team.name);
        }, function () {
            
        }); 
    };
    
    createNewTeam = function(name) {
        var data={};
        data.teamName=name;
        var res=setTeamOp("newTeam",makeDataReadyToSend(data));
        if(res.status=="ok") {
            loadTeams();
        }
        else {
            alert("Error!");
        }
    };
    
    closeTeams = function() {
        $(".dropdown-toggle").trigger("click");
    };
    
    
    $scope.uploadCsv = function() {
        var selected_team_to_users=0;
        var f_data={};
        f_data.teams=$scope.teams;
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/selectTeam.html?t='+t,
          keyboard:true,
          backdrop:'static',
          windowClass: 'addTeamModal',
          controller: selectTeamCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
          
        modalInstance.result.then(function (selected_team) {
            uploadUsersFile(selected_team);
        }, function () {
            return;
        }); 
    }
    
    uploadUsersFile = function(teamID) {
        var data_expected=[
          {
              field:"userName",
              name:"Name",
              type:"text",
              max_length:200
          },
          {
              field:"userEmail",
              name:"Email",
              type:"text",
              max_length:200
          },
          {
              field:"userDepartment",
              name:"Entity",
              type:"text",
              max_length:200
          },
          {
              field:"userPosition",
              name:"Location",
              type:"text",
              max_length:200
          },
          {
              field:"userLevel",
              name:"Level",
              type:"text",
              max_length:200
          },
          {
              field:"userEmpID",
              name:"Employee ID",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_1",
              name:"C1",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_2",
              name:"C2",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_3",
              name:"C3",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_4",
              name:"C4",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_5",
              name:"C5",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_6",
              name:"C6",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_7",
              name:"C7",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_8",
              name:"C8",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_9",
              name:"C9",
              type:"text",
              max_length:200
          },
          {
              field:"general_field_10",
              name:"C10",
              type:"text",
              max_length:200
          }
         
      ];
      var t=new Date().getTime();
       var f_data={};
      f_data.data_expected=data_expected;
      f_data.exp_file="http://my.wheeldo.com/getFile/WheeldoTeamFileExample.csv";
      var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/csvUploader.html?t='+t,
          keyboard:true,
          backdrop:'static',
          windowClass: 'usersModal',
          controller: csvUploaderCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
        
        modalInstance.result.then(function (csvResult) {
            var data={};
            data.teamID=teamID;
            data.users=[];
               for(i in csvResult[0]) {
                    var new_user={};
                    for(j in data_expected) {
                        var field=data_expected[j].field;
                        if(csvResult[j]) {
                            new_user[field] = csvResult[j][i];
                        }
                        else {
                            //alert("You have some error in field - "+field);
                        }
                        
                    }
                    data.users.push(new_user);
                }
                
   
            var res=setTeamOp("insertUsers",makeDataReadyToSend(data));
            if(res.status=="ok") {
              window.location.reload();
            }
            else {
                alert("Error!");
            }
        }, function () {
            
        });
    };

    
    deleteTeamFromScope = function(teamID) {
        // delete from users:
        for(i in $scope.users) {
            var teams=$scope.users[i].teams;
            for(j in teams) {
                var team=teams[j];
                if(team.id==teamID) {
                    $scope.users[i].teams.splice(j, 1);
                }
            }  
        }
        
        // delete from team list:
        for(k in $scope.teams) {
            var team=$scope.teams[k];
            if(team.teamID==teamID) {
                $scope.teams.splice(k, 1);
            }
            
        }
    };

    
    makeDataReadyToSend = function(editData) {
      var editDataJson=JSON.stringify(editData);
      editDataJson=editDataJson.replace(/'/g,"\\\"");
      editDataJson=editDataJson.replace(/&/g,"___amp___");
      return editDataJson;
    };
    
    $scope.removeUsers = function() {
      var selctedAr=$filter('filter')($scope.users, {checked:true});
      if(selctedAr.length==0)
          return;
      if(!confirm("Are you sure you want to remove "+selctedAr.length+" users marked?"))  
          return;
      
      var ids=[];
      for(i in selctedAr) {
          ids.push(selctedAr[i].id);
      }
      var res=setTeamOp("remove",makeDataReadyToSend(ids));
      if(res.status=="ok") {
        for(i in selctedAr) {
            deleteUser(selctedAr[i].id);
        }
        updateCacheFile();
      }
      else {
          alert("Error!");
      }
      
    };
    
    deleteUser = function(userID){
        for(i in $scope.users) {
            if($scope.users[i].id==userID) {
                $scope.users.splice(i, 1);
            }
        }
    };
    
    
    var saveing_status=0;
    var saving_time_ping=0;
    updateCacheFile = function() {
        return;
        saveing_status=0;
        $(".creating_cache_bar").show();
        saving_time_ping=checkPing();
        moveSaving1();
        updateTeamCacheFile();
    };
    
    
    moveSaving1 = function() {
        saveing_status++;
        $(".proggress_bar").css("width",saveing_status+"%");
        if(saveing_status==100) {
            saveing_status=0;
            $(".creating_cache_bar").hide();
        }
        else {
            setTimeout( moveSaving1, total_users*(0.46*saving_time_ping)/100 );
        }
    };

    
    $scope.setCheckUser = function() {

    };
    
    
    //$scope.filterMultiple={name:'aviad'};
    $scope.filterMultiple={};
    
    
    
    
    filterMultiple = function() {
        //$scope.filterMultiple={name:'aviad'};
    };
    
    
    // pager
    
    var users_per_page=30;
    var current_page=1;
    
    set_pager = function(data) {
        var total_users=data.length;
        var max_user_place=current_page*users_per_page;
        var limit_min=-users_per_page;
        var limit_max=max_user_place;
        
        if(max_user_place>total_users) {
            limit_max=total_users;
            var gap=max_user_place-total_users;
            limit_min=-(users_per_page-gap);
        }
        $scope.limitMax=limit_max;
        $scope.users_per_page=limit_min;
        
        setPagerAmount(total_users,max_user_place,current_page);
        
        $scope.total=total_users;
    };
    
    setPagerAmount = function(total_users,max_user_place,current_page) {
        var max=current_page*users_per_page;
        if(max>total_users)
            max=total_users;
        var min=(current_page-1)*users_per_page+1;
        $scope.amount=min+"-"+max;
    };
    
    // pager variables //
    
    
    /////////////////////
    
    $scope.goNextPage = function() {
        var total_users=users_data.length;
        var max_allowed=Math.ceil(total_users/users_per_page);
        
        if(current_page>=max_allowed)
            return;
        current_page++;
        set_pager(users_data);
    };   
    
    
    $scope.goPerviousPage = function() {
        if(current_page<2)
            return;
        current_page--;
        set_pager(users_data);
    };
    ////////
    
    
   
    $scope.after_load='none';
    $scope.please_wait='block';
    
    
    microtime = function () {
        return new Date().getTime();
    };

    var self=this;
    $scope.ping_run=false;
    checkPing = function(un_sync) {
        
        
        un_sync = typeof un_sync !== 'undefined' ? un_sync : false;
        
        //console.log($scope.ping_run);
        
        if($scope.ping_run)
            return;
        
        if(un_sync) {
            $scope.ping_run=true;
        }
        
        
       // console.log(self.ping_run);
        //console.log("in");
        var start=microtime();
        var res;
        var request = new XMLHttpRequest();
        request.open('POST', '/app/ajax/ping.aspx', un_sync);
        request.send(null);
        if (request.status === 200) {
            res=microtime()-start;
            if(un_sync) {
                self.factor=res;
                $scope.ping_run=false;
            }

            //res=res/1000;
        }
        
        
        return res;  
    };
    
    
    getTotal = function() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST', '/app/ajax/teams_total.aspx', false);  // `false` makes the request synchronous
        request.send(null);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
            //$scope.please_wait='none';
        }
        return res;
    };

    

    this.factor=0;
    var stopLoading=false;
    loadWait = function() {
        
        
        
            
            var progressbar = $( "#progressbar" ),
            progressLabel = $( ".progress-label" );
            progressbar.progressbar({
            value: false,
            change: function() {
            progressLabel.text( progressbar.progressbar( "value" ) + "%" );
            },
            complete: function() {
        
            progressLabel.text( "Loading finished, Please wait while the process is finished" );
                
            }
            });
            function progress() {
                
                
                
                var val = progressbar.progressbar( "value" ) || 0;
                progressbar.progressbar( "value", val + 1 );
                
                
                
                
                if(val%5==0 && self.factor!=0) {
                   //console.log("check ping");
                   //checkPing(true); 
                }
                
                if(stopLoading)
                    self.factor=0.1;
                
                //console.log(self.factor);
                //console.log("waiting "+(total_users*(0.42*self.factor)/100)/1000+" sec");
                if ( val < 99 ) {
                    setTimeout( progress, total_users*(0.46*self.factor)/100 );
                }
                else {
                    $scope.please_wait='none';
                    $scope.after_load='block';
                }
            }
            setTimeout( progress, 10 );
           
    };
    
    
    var users_data;
    loadUsers = function() {
        loadWait();
        if(self.factor==0) {
            self.factor=checkPing();
        }
        $http({method: 'POST', url: '/app/ajax/teams.aspx'}).
        success(function(data, status, headers, config) {
            createUsersView(data);
            $scope.users_loaded=true;
            users_data=data;
            set_pager(data);
            
        }).
        error(function(data, status, headers, config) {
    
        });
    };
    
    
    
    createUsersView = function(users_data) {
        users=users_data;
        $scope.users=users;
        stopLoading=true;
        $scope.please_wait='none';
        $scope.after_load='block';
            
    };
    
    
    $scope.sortBy = function(key) {
        $scope.predicate = key; $scope.reverse=!$scope.reverse;
        
        
        $(".sort").removeClass("asc");
        $(".sort").removeClass("desc");
        
        if($scope.reverse) {
            $("."+key).addClass("desc");
        }
        else {
            $("."+key).addClass("asc");
        }
        
    };
    
    
    set_ddMenuCont = function() {
        $(".dd_wheeldo").each(function(index){
            var obj_0=$(this);
            $(this).find("input[type=checkbox]").change(function(){
               obj_0.find(".value").val("");
               obj_0.find(".value").val(set_ddMenuCont_val(obj_0));
               obj_0.find(".group_header").html(set_ddMenuCont_label(obj_0));
               filterMultiple();
            });
        });
        
        
    };
    
    
    set_ddMenuCont_val = function(obj_0) {
        var ret="";
        var counter=0;
        obj_0.find("input[type=checkbox]").each(function(index){
            if($(this).is(":checked")) {
                if(counter!=0)
                    ret+=",";
                ret+=$(this).val();
                counter++;
            }
        });
        if(counter==0)
            ret="0";
        return ret;
    };
    
    set_ddMenuCont_label = function(obj_0) {
        var ret="";
        var counter=0;
        var label_text="";
        obj_0.find("input[type=checkbox]").each(function(index){
            if($(this).is(":checked")) {
                if(counter==0)
                    label_text=$(this).attr("def_val");
                counter++;
            }
        });

        if(counter==0)
            ret=obj_0.find(".group_header").attr("def_val");
        else if(counter==1)
            ret=label_text;
        else 
            ret=counter+" "+"checked";
        
        return ret;
    };
    
    
});


app.controller('usersLogsController', function ($scope, $http, WheeldoService) {
    function getLogs() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST','/app/ajax/users_logs.aspx' , false);  // `false` makes the request synchronous
        request.send(null);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    }
    
    
    function getOrgs() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST','/app/ajax/orgs_logs.aspx' , false);  // `false` makes the request synchronous
        request.send(null);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    }

    
    
    
    $scope.init = function () {
        $scope.users_logs=getLogs();
        $scope.orgs=getOrgs();
        setCheckAll();
        $scope.sortBy('time');
    };
    
    
    $scope.sortBy = function(key) {
        $scope.predicate = key; $scope.reverse=!$scope.reverse;
        
        
        $(".sort").removeClass("asc");
        $(".sort").removeClass("desc");
        
        if($scope.reverse) {
            $("."+key).addClass("desc");
        }
        else {
            $("."+key).addClass("asc");
        }
        
    };
    
    
    $scope.$watch('org_filter', function() {
        
    });
    
});


app.controller('developerController', function ($scope, $http, WheeldoService) {
    init();
    function init() {
        $scope.token=token;
        $scope.userID=userID;
    };
});


app.controller('usersManageController', function ($scope, $http, WheeldoService) {
    init();
    var users={};
    function init() {
        users=getUsers();
        $scope.users=users;
        $scope.orgs=getOrgs();
        
    };
    
    
    $scope.init_users = function(){
        setActions();
    };
    
    function getUsers() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST','/app/ajax/users_manage.aspx' , false);  // `false` makes the request synchronous
        request.send(null);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    };
    
    
    function getOrgs() {
        var res;
        var request = new XMLHttpRequest();
        request.open('POST','/app/ajax/orgs.aspx' , false);  // `false` makes the request synchronous
        request.send(null);
        if (request.status === 200) {
            res=jQuery.parseJSON(request.responseText);
        }
        return res;
    };
    
    
    function setActions() {
        $(".edit_details").click(function(){
            var userID=$(this).attr("userID");
            edit_details(userID);
        });
        
        $(".show_logs").click(function(){
            var userID=$(this).attr("userID");
            show_logs(userID);
        });
        
        $(".reset_password").click(function(){
            var userID=$(this).attr("userID");
            reset_password(userID);
        });
    };
    
    
    
    function show_logs(userID) {
        alert(userID);
    };
    
    function edit_details(userID) {
        var user_actions=$(".dialog");
        
        
        var edited_user={};
        for(i in $scope.users){
            var user=$scope.users[i];
            if(user.userID==userID)
                edited_user=user;
        }
        
        
        

        //console.log(edited_user);
        $scope.$apply(function () {
            $scope.userOrganizationID=edited_user.userOrganizationID;
            $scope.edited_user=edited_user;
        });
        
        user_actions.dialog({modal: true, width: 640, height: 540, closeText: "x", dialogClass: 'fixed-dialog',
            open: function(event, ui) {
                
            },
            close: function(event, ui) {

            }
        });
    };
    function reset_password(userID) {
        alert(userID);
    };
});

app.controller('createGameController', function ($scope, $timeout, $routeParams, $location, $http, $modal, WheeldoService) {
    
    $scope.setEditScreenFunctions = function() {
//       $(window).bind('keydown', function(event) {
//            if (event.ctrlKey || event.metaKey) {
//                switch (String.fromCharCode(event.which).toLowerCase()) {
//                case 's':
//                    event.preventDefault();
//                    alert('saved');
//                    return false;
//                    break;
//                case 'f':
//                    event.preventDefault();
//                    alert('ctrl-f');
//                    break;
//                case 'g':
//                    event.preventDefault();
//                    alert('ctrl-g');
//                    break;
//                }
//            }
//        });
        $(document).bind("keydown", function(e) {
          if(e.ctrlKey && (e.which == 83)) {
            e.preventDefault();
            setTimeout(function() {
              $scope.saveData();
            }, 100);
            return false;
          }
        });
    };
    
    $scope.loadScroller = function() {
        $('.scroll-pane').jScrollPane();
    };
    
    $scope.loadScroller();
    
    $.ajax({
        url: DATA_PATH+'app/controllers/loadPreviousGamesCtrl.js',
        dataType: "script"
    });
    
    $.ajax({
        url: DATA_PATH+'app/controllers/publishGamesCtrl.js',
        dataType: "script"
    });
    
    $.ajax({
        url: DATA_PATH+'app/controllers/addPlayerCtrl.js',
        dataType: "script"
    });
    
    $.ajax({
        url: DATA_PATH+'app/controllers/csvUploaderCtrl.js',
        dataType: "script"
    });
    
    var copyID=$routeParams.copyID;
    if(copyID==0) {
        if(hateIe) {
            $scope.newCopyID=getNewCopyID($routeParams.app);
            copyID=$scope.newCopyID.copyID;
            $location.path("/createGame/"+$routeParams.app+"/"+copyID);
        }
        else {
            $scope.newCopyID=getNewCopyID($routeParams.app);
            copyID=$scope.newCopyID.copyID;
            window.location.href="/#/createGame/"+$routeParams.app+"/"+copyID;
        }
        
        
    }
    else {
        init();
        $scope.appData=getAppInfo($routeParams.app);
        $scope.copyData=getCopyInfo($routeParams.copyID);
        $scope.editData=getEditData($routeParams.app,$routeParams.copyID);
        $scope.showAddPlayer=$scope.copyData.appCopyTerminate=="0"?true:false;
    }
    
    var t=new Date().getTime();
    $scope.template = "app/templates/app_edit_"+$routeParams.app+".html?t="+t;
    
    function init() {
        $scope.app=$routeParams.app;
        $scope.finish_load='loaded';
    };
   
    $scope.setSaveCopyName = function() {
        $("#game_name").keyup(function(){
            var value=$(this).val();
            var seconds=new Date().getTime();
            $(this).attr("lastKeyUp",seconds);

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
    };
    
    $scope.bindQuize = function() {
        $(".show_options").unbind("click");
        $(".show_options").click(function(){
            var q_hidden=$(this).attr("q_hidden");
            var q_hidden_obj=$(".q_hidden[q_hidden="+q_hidden+"]");
            $(this).toggleClass("open");
            q_hidden_obj.toggle();
        });
    };
    
    $scope.cancelAddQuestion = function() {
      if(!confirm("Sure?"))
          return;
      closeNewQ();
    };
    
    clearDialog = function() {
        // empty fields:
        $("#bonus").val(0);
        $(".bonus").removeClass("checked");
        $("#answer").val("A");

        $(".wrongWright").removeClass("right");
        $(".wrongWright[answer=A]").addClass("right");


        $("#question").val("");
        $("#A").val("");
        $("#B").val("");
        $("#C").val("");
        $("#D").val("");
    };
    
    closeNewQ = function() {
        clearDialog();
        $("#addQuestion").dialog("close");
    };
    
    $scope.saveAddQuestionTriviaOld = function(q_index) {
        alert(q_index);
        var new_question={};
        new_question.q_data={};
        new_question.q_data.question=$("#question").val();
        new_question.q_data.bonus=$("#bonus").val();
        new_question.q_data.answer=$("#answer").val();
        new_question.q_data.A=$("#A").val();
        new_question.q_data.B=$("#B").val();
        new_question.q_data.C=$("#C").val();
        new_question.q_data.D=$("#D").val();
        
        if(!$("#question").hasClass("ng-valid")) {
            alert("Please fill the question!");
            return;
        }
        
        var filledQuestion=[];
        
        for(var i=1;i<=4;i++) {
            var let=getLetNum(i);  
            if($("#"+let).hasClass("ng-valid")) 
                filledQuestion.push(i);
        }
        
        if(filledQuestion.length<2) {
            alert("Please insert at least 2 answers!");
            return;
        }
        
        var answer=getNumLet($("#answer").val());
        
        if(filledQuestion.indexOf(answer)<0) {
            alert("Please check the correct answer for your question!");
            return;
        }
        
        //validation done
        
        if(q_index==-1) {
            $scope.editData.quiz.push(new_question);
        }
        else {
            $scope.editData.quiz[q_index]=new_question;
        }
        closeNewQ();
        
    };
    
    
    var show_bonus=false;
    var hide_more=false;
    $scope.addTriviaQuestion = function() {
       clearDialog();
       openAddQTriviaDialog(-1);
    };
    

    openAddQTriviaDialog = function(q_index,q,waiting_list) {
        if(trivia) {
           show_bonus=true;
           hide_more=true;
       }
        
        var q_alt={
            q_id:0,
            question:'',
            extra:'',
            more_link:'',
            answer:'A',
            is_bonus:0,
            A:'',
            B:'',
            C:'',
            D:'',
            userID:0
        };
        
        q = typeof q !== 'undefined' ? q : q_alt;
        
        var f_data=[];
        f_data.q_index=q_index;
        f_data.q=q;
        f_data.show_bonus=show_bonus;
        f_data.hide_more=hide_more;
        
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/TriviaAddQ.html?t='+t,
          keyboard:false,
          backdrop:'static',
          controller: triviaQCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
          
        modalInstance.result.then(function (new_question) {
           
           if(q_index==-1) {
               if(waiting_list) {
                   
                   $scope.editData.waitForApprooval.push(new_question);
               }
               else{
                   $scope.editData.quiz.push(new_question);
               }
            }
            else {
                if(waiting_list) {
                   $scope.editData.waitForApprooval[q_index].q_data=new_question.q_data;
               }
               else{
                   $scope.editData.quiz[q_index]=new_question;
               }
                
            }
            autoSave();
        }, function () {
            
        });
    };
   
    
    
    $scope.editQTrivia = function(index,waiting_list) {
        waiting_list = typeof waiting_list !== 'undefined' ? waiting_list : false;
        
        if(waiting_list) {
            var q=$scope.editData.waitForApprooval[index].q_data;
        }
        else {
            var q=$scope.editData.quiz[index].q_data;
        }

        openAddQTriviaDialog(index,q,waiting_list);
    };
    
    
    //show_options
    var trivia=false;
    $scope.initTrivia = function(trivia_flag) {
        $.ajax({
            url: DATA_PATH+'app/controllers/triviaQCtrl.js',
            dataType: "script"
        });
        trivia_flag = typeof trivia_flag !== 'undefined' ? trivia_flag : false;
        trivia=trivia_flag;
        $(".autoSave").blur(autoSave);
    };

    
    $scope.removeQTrivia = function(index,waiting_list) {
      waiting_list = typeof waiting_list !== 'undefined' ? waiting_list : false;
      if(!confirm("Sure?"))
          return;
      
      if(waiting_list) {
        $scope.editData.waitForApprooval.splice(index, 1);
      }
      else {
        $scope.editData.quiz.splice(index, 1);
      }
      
      autoSave();

    };
    
    $scope.approveQTrivia = function(index) {
        
        var q=$scope.editData.waitForApprooval[index];
        q.q_data.not_approved=0;
        $scope.editData.quiz.push(q);
        $scope.editData.waitForApprooval.splice(index, 1);
        autoSave();
    };
    
    
    ////////////// PayItForward ///////////////
    
    $scope.initPayItForward = function() {
        $scope.new_tip="";
        $(".autoSave").blur(autoSave);
    };
    
    
    $scope.advancedShow = function() {
        $(".advanced_data").toggle();
    };
    
    $scope.settingsShow = function() {
        $(".settings_wrap").toggle();
    };
    
    $scope.removeTipPIF = function(index) {
      if(!confirm("Sure?"))
          return;
      
      $scope.editData.tips.splice(index, 1);
      autoSave();
    };


    
    $scope.addTipPIF = function() {
        
        
        if(!$("#new_tip").hasClass("ng-valid")) {
            return;
        }
        
        $scope.new_tip=$("#new_tip").val();
        
         
        
        $scope.editData.tips.push({text:$scope.new_tip});
        $("#new_tip").attr("class","inputMark ng-pristine ng-invalid ng-invalid-required ng-valid-maxlength ng-valid-minlength");
        $("#new_tip").val("");
        autoSave();
    };
    
    
    $scope.initDatePicker = function() {
        $scope.minDate = $scope.editData.ex_info.now_time;
    };
    
    $scope.showWeeks = false;
    $scope.openCal = function() {

          $scope.cal_opened = true;
        
      };
      
    $scope.dateOptions = {
        'year-format': "'yy'",
        'starting-day': 1
    };
    

    
    
    
    $scope.hstep = 1;
    $scope.mstep = 15;
    $scope.ismeridian = true;
    


    $scope.showTime = function() {
      getTime();
    };
    
    getTime = function() {
      var date_as=$scope.editData.ex_info.end_time_time;

      var time=new Date(date_as)
      var h=time.getHours();
      var m=time.getMinutes();
      var s=time.getSeconds();
      
      var date=$scope.editData.ex_info.end_time_date;
      
      if(typeof(date)!="object") {
        var dateSpl=date.split("-")
        var total_date=new Date(dateSpl[0],dateSpl[1],dateSpl[2],h,m);
      }
      else {
         total_date=date;
         total_date.setHours(h);
         total_date.setMinutes(m);
      }

      var ret=Math.floor(total_date.getTime()/1000);
      return ret;
      
    };
    $scope.dateChanged = function() {
        $scope.editData.game_info.end_time=getTime();
        autoSave();
    };
    
    ////////////// GuessWot ///////////////////
    $scope.initGuessWot = function() {
        $.ajax({
            url: DATA_PATH+'app/controllers/GWQCtrl.js',
            dataType: "script"
        });
        
        
        $scope.q_types=[];
        $scope.q_types[0]={id:0,name:"--- Select ---"};
        for(i in $scope.editData.q_types){
            var type=$scope.editData.q_types[i];
            
            var new_type={
                id:parseInt(type.id),
                name:type.name   
            };
            $scope.q_types.push(new_type);
            
        }
        
        var types_array=[];
        for(i in $scope.q_types) {
            var type=$scope.q_types[i];
            types_array[type.id]=type.name;
        }
        
        $scope.types_array=types_array;
        $scope.questions=$scope.editData.questions;
    };
    
    $scope.moveUp = function(index) {
      if(index>0) {  
          var from=$scope.editData.questions[index];
          var to=$scope.editData.questions[index-1];
          
          $scope.editData.questions[index]=to;
          $scope.editData.questions[index-1]=from;
      }
    };
    
    $scope.moveDown = function(index) {
      if(index<$scope.editData.questions.length-1) {  
          var from=$scope.editData.questions[index];
          var to=$scope.editData.questions[index+1];
          
          $scope.editData.questions[index]=to;
          $scope.editData.questions[index+1]=from;
      }  
    };
    
    
    openAddQGWDialog = function(q_index,q) {
        
        var q_alt={
            q_id:0,
            type:0,
            text:"",
            options:[],
            values:[],
            condition:{
                value:50,
                instruction_true:"",
                instruction_false:""
            },
            conditionMultiSelect:{
                value:50,
                instruction_true:"",
                instruction_false:"",
                options_true:[],
                options_false:[]
            }       
        };
        
        q = typeof q !== 'undefined' ? q : q_alt;
        
        var f_data=[];
        f_data.q_index=q_index;
        f_data.q=q;
        f_data.q_types=$scope.q_types;
        
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/GWAddQ.html?t='+t,
          keyboard:false,
          backdrop:'static',
          controller: GWQCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
          
        //console.log(q);
        modalInstance.result.then(function (new_question) {
           
           if(q_index==-1) {
                $scope.questions.push(new_question);
            }
            else {
                $scope.questions[q_index]=new_question;
            }
           autoSave();
           
        }, function () {
            
        });
    };
    
    $scope.addGWQuestion = function() {

       openAddQGWDialog(-1);
    };
    
    
    $scope.editQGW = function(index) {
        var q=$scope.questions[index];
        openAddQGWDialog(index,q);
    };
    
    $scope.removeQGW = function(index) {
      if(!confirm("Sure?"))
          return;
      
      $scope.questions.splice(index, 1);
      autoSave();
    };

    
    
    
    //// voter //////////////////////////////////////////////////////
    
    $scope.initVoter = function() {
        $.ajax({
            url: DATA_PATH+'app/controllers/VACtrl.js',
            dataType: "script"
        });
        
        
//        $("#voter_question").wheeldo_autosave({
//            callback:autoSave,
//            delay:1200
//        });
//        
        
        $("#voter_question").blur(autoSave);
        $("#limit_time").blur(autoSave);
        $(".checkbox").click(autoSave);
    };

    
    $scope.editAddVoterAnswer = function(ans_index,ans) {
        
        
        var ans_alt={
            answer:"",
            answer_description:"",
            approved:"1",
            img_link:"",
            user_id:0,
            img_bitmap:""
        };
        
        ans = typeof ans !== 'undefined' ? ans : ans_alt;
        
        var f_data=[];
        f_data.ans_index=ans_index;
        f_data.ans=ans;
        
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/VoterAddAnswer.html?t='+t,
          keyboard:true,
          backdrop:'static',
          controller: VACtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
        
        
        modalInstance.result.then(function (ans) {

           if(ans_index==-1) {
                $scope.editData.answers.push(ans);
            }
            else {
                $scope.editData.answers[ans_index]=ans;
            }
           autoSave();
           
        }, function () {
            
        });
    };
    
    $scope.addVoterAnswer = function() {
        $scope.editAddVoterAnswer(-1);
    };
    
    
    $scope.editAnswerVoter = function(index) {
        var ans=$scope.editData.answers[index].data;
        $scope.editAddVoterAnswer(index,ans);
    };
    
    
    $scope.removeAnswerVoter = function(index) {
        if(!confirm("Sure?"))
          return;
      
        $scope.editData.answers.splice(index, 1);
    };
    
    
    /////////////////////////////////////////////////////////////////
    
    
    // DiscussionGroup //////////////////////////////////////////////
    
    $scope.initDiscussionGroup = function() {
        $scope.copyID=copyID;
        watchVideoLink();
        $(".autoSave").blur(autoSave);
        $(".radio").click(autoSave);
        
    };
    
    watchVideoLink = function() {
        $scope.$watch('editData.vlink', function() {
            $("#vlink").removeClass("youtube");
            $("#vlink").removeClass("vimeo");
            $(".videoIframe").hide();
            checkVideoLink();
        });
    };
    
    checkVideoLink = function() {
      if($scope.editData.vlink) {
          var link=$scope.editData.vlink;
          if(link.indexOf("youtube")>-1) {
              $("#vlink").removeClass("vimeo");
              $("#vlink").addClass("youtube");
              var youtubeEX=link.split("v=");
              
              if(youtubeEX.length>1) {
               var split1=link.split("v=")[1];
               var split2=split1.split("&");
                var youtubeID=split2[0];
              }
              else {
                youtubeEX=link.split("/");
                var youtubeID=youtubeEX[(youtubeEX.length-1)];
              }
              
              
              
              var iframeUrl="http://www.youtube.com/embed/"+youtubeID;
              $scope.editData.vlink=iframeUrl;
              
              $(".videoIframe").attr("src",iframeUrl);
              $(".videoIframe").slideDown(400);
              
          }
          
          if(link.indexOf("vimeo")>-1) {
              $("#vlink").removeClass("youtube");
              $("#vlink").addClass("vimeo");
              var vimeoEX=link.split("/")
              var vimeoID=vimeoEX[(vimeoEX.length-1)];
              var iframeUrl="http://player.vimeo.com/video/"+vimeoID;
              $scope.editData.vlink=iframeUrl;
              $(".videoIframe").attr("src",iframeUrl);
              $(".videoIframe").slideDown(400);
          }
      }
    };
    
    $scope.onDiscussionGroupFileSelect = function($files) {
        var file=$files[0];
        var fileEnabled=['jpeg','png','zip','pdf'];
        var officeEnabled=['word','excel','ppt','office'];
        var fileType=file.type;
        
        var allow=false;
        if(fileType) {
            var miniType=fileType.split("/")[1];
            var index=fileEnabled.indexOf(miniType);
            if(index>-1) {
                allow=true; 
            }
            else {
               // check for office documents:
               for(i in officeEnabled) {
                   var allowWord=officeEnabled[i];
                   if(miniType.indexOf(allowWord)>-1) {
                       allow=true;
                       break;
                   }
               }
            }
        
        }
        

        if(!allow) {
            alert("Invalid file type!")
        }
        else {
            $(".loadImage").show();
            var fileName=file.name;
            var formObj=$("#file_upload_form");
            var action=formObj.attr("action");
            var target=formObj.attr("target");
            $("#upload_target").attr("onload","onUploadDiscussionGroupFrmaeLoad('"+fileName+"')");
            formObj.submit();
        }
    };
    
    $scope.checkVideoDG = function() {
      console.log("bla");
    };
    
    
    
    // SlideStar //////////////////////////////////////////////
        //$scope.editData.slides=[];
        $scope.initSlideStar = function() {
            $scope.copyID=copyID;
            loadSlides(copyID);
            $.ajax({
                url: DATA_PATH+'app/controllers/triviaQCtrl.js',
                dataType: "script"
            });
            
            $(".autoSave").blur(autoSave);
        };
    
        
    
        $scope.onSlideStarFileSelect = function($files) {
        var file=$files[0];
        var fileEnabled=['pptx','ppt','powerpoint'];
        var officeEnabled=['pptx','ppt'];
        var fileType=file.type;
        
        var allow=false;
//        if(fileType) {
//            var miniType=fileType.split("/")[1];
//            var index=fileEnabled.indexOf(miniType);
//            if(index>-1) {
//                allow=true; 
//            }
//            else {
//                
//                if(fileType.indexOf("powerpoint")>-1) {
//                    allow=true; 
//                }
//                
//                if(fileType.indexOf("presentation")>-1) {
//                    allow=true; 
//                }  
//            }
//        }
        
        var file_name=file.name;
        var ex=file_name.split(".");
        var ext=ex[(ex.length-1)];
        
        if(fileEnabled.indexOf(ext)>-1) {
            allow=true; 
        }

        if(!allow) {
            alert("Invalid file type!")
        }
        else {
            $(".loadImage").show();
            $(".loading_bar").show();
            $(".loading_bar .fill").css("width","0%");
            perFill=0;
            increaseFillBar();
            inter=setInterval('increaseFillBar()',50)

            
            var fileName=file.name;
            var formObj=$("#file_upload_form");
            var action=formObj.attr("action");
            var target=formObj.attr("target");
            //$("#upload_target").attr("onload","");
            
//            $("#upload_target").onload = function(){
//                alert("loaded")
//            };
            
            document.getElementsByTagName('iframe')[0].onload = function(){
                $(".loading_bar").hide();
                $(".loadImage").hide();
                saveSlides(copyID);
                $scope.$apply(function () {
                    $scope.editData=$scope.editData;
                });
                loadSlides(copyID);
                autoSave();
            };
            
            formObj.submit();
        }
    };
    
    
    var inter;
    var perFill=0;
    increaseFillBar = function() {
        perFill=perFill+0.1;
        $(".loading_bar .fill").css("width",perFill+"%");
        if(perFill>=100) {
            clearInterval(inter);
        }
            
    };
    
    saveSlides = function(copyID) {
      $scope.editData.slides=[];
      var res=loadSlidesPics(copyID); 
      if(res.status=="ok") {
          var files=res.files;
          for(i in files) {
              var slideObg={};
              slideObg.src=files[i];
              slideObg.quiz=[];
              $scope.editData.slides.push(slideObg);
          }
      }
    };
    
    loadSlides = function(copyID) {
        
        if($scope.editData.slides.length==0){
            saveSlides(copyID);
            if($scope.editData.slides.length==0)
                return;
        }
        

        $(".wheeldo_slide").wheeldo_slide.removeSlider();
        //saveSlides(copyID);

        $(".wheeldo_slide").wheeldo_slide({
            slides:$scope.editData.slides,
            onSlideChange:"updateQ()",
            keys:false
        });

        $(".wheeldo_next_slide").unbind("click");
        $(".wheeldo_next_slide").click(function(){
            $(".wheeldo_slide").wheeldo_slide.next();
        });
        
        $(".wheeldo_next_end").unbind("click");
        $(".wheeldo_next_end").click(function(){
            $(".wheeldo_slide").wheeldo_slide.next_end();
        });
        
        $(".wheeldo_prev_slide").unbind("click");
        $(".wheeldo_prev_slide").click(function(){
            $(".wheeldo_slide").wheeldo_slide.prev();
        });
        
        $(".wheeldo_prev_end").unbind("click");
        $(".wheeldo_prev_end").click(function(){
            $(".wheeldo_slide").wheeldo_slide.prev_end();
        });

    };
    
    
    
    
    $scope.remmm = function() {
        $(".wheeldo_slide").wheeldo_slide.removeSlider();
    };
    
    $scope.loaaa = function() {
        loadSlides(copyID);
    };
    
    updateQ = function() {
       var slide=$(".wheeldo_slide").wheeldo_slide.getSlide();
       $scope.loadQuestions(slide);
    };
    
    $scope.loadQuestions = function(slide) {
        $(".questions_wrap").hide();
        $(".slide"+slide).show();
    };
    
    
    $scope.addSlideQuestion = function() {
        clearDialog();
        openAddQSlideDialog(-1);
    };

    

    openAddQSlideDialog = function(q_index,q,waiting_list) {
        
        var curr_slide=$(".wheeldo_slide").wheeldo_slide.getSlide();
        
        var q_alt={
            slide_id:curr_slide,
            question:'',
            extra:'',
            more_link:'',
            answer:'A',
            is_bonus:0,
            A:'',
            B:'',
            C:'',
            D:'',
            userID:0
        };
        
        q = typeof q !== 'undefined' ? q : q_alt;
        
        var f_data=[];
        f_data.q_index=q_index;
        f_data.q=q;
        f_data.slide_q=true;
        
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/TriviaAddQ.html?t='+t,
          keyboard:false,
          backdrop:'static',
          controller: triviaQCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
          
        modalInstance.result.then(function (new_question) {

           if(q_index==-1) {
               if(waiting_list) {
                   //$scope.editData.slides[curr_slide].quiz.push(new_question);
               }
               else{
                   $scope.editData.slides[curr_slide].quiz.push(new_question);
               }
            }
            else {
                if(waiting_list) {
                   $scope.editData.waitForApprooval[q_index].q_data=new_question.q_data;
               }
               else{
                   $scope.editData.slides[curr_slide].quiz[q_index]=new_question;
               }
                
            }
            
            autoSave();
        }, function () {
            
        });
    };
    
    
     $scope.editQSlide = function(index,waiting_list) {
        var curr_slide=$(".wheeldo_slide").wheeldo_slide.getSlide();
        waiting_list = typeof waiting_list !== 'undefined' ? waiting_list : false;
        
        if(waiting_list) {
            //var q=$scope.editData.waitForApprooval[index].q_data;
        }
        else {
            var q=$scope.editData.slides[curr_slide].quiz[index].q_data;
        }
        
        openAddQSlideDialog(index,q,waiting_list);
    };
    
    
    $scope.removeQSlide = function(index,waiting_list) {
      var curr_slide=$(".wheeldo_slide").wheeldo_slide.getSlide();
      waiting_list = typeof waiting_list !== 'undefined' ? waiting_list : false;
      if(!confirm("Sure?"))
          return;
      
      if(waiting_list) {
        //$scope.editData.waitForApprooval.splice(index, 1);
      }
      else {
        $scope.editData.slides[curr_slide].quiz.splice(index, 1);
      }
      autoSave();
    };
    
    
    $scope.approveQSlide = function(index) {
      var curr_slide=$(".wheeldo_slide").wheeldo_slide.getSlide();

      $scope.editData.slides[curr_slide].quiz[index].q_data.not_approved=0;
      autoSave();
    };
    
    //////////// Daily insight //////////////////////////////////
    
    $scope.initDailyInsight = function() {
        $scope.copyID=copyID;
        $(".autoSave").blur(autoSave);
        $(".autoSaveChange").change(autoSave);
        
    };
    
    
    $scope.approveQDailyInsight = function(index) { 
      $scope.editData.insights[index].data.status=1;
      autoSave();
    };
    
    $scope.removeQDailyInsight = function(index) {
      if(!confirm("Sure?"))
          return;
      $scope.editData.insights.splice(index,1);
      autoSave();
    };
    
    $scope.editQDailyInsight = function(index) {
      var insight=$scope.editData.insights[index];
      openAddInsightDialog(index,insight);
    };
    
    $scope.addnewInsight = function() {
       clearDialog();
       openAddInsightDialog(-1);
    };
    
    openAddInsightDialog = function(insight_index,insight,waiting_list) {
        
        var insight_alt={};
                
        insight_alt.data={
            id:0,
            headline:'',
            article:'',
            uid:0,
            gid:copyID,
            status:1,
            counter:0
        };
        
        insight = typeof insight !== 'undefined' ? insight : insight_alt;
        
        var f_data=[];
        f_data.insight_index=insight_index;
        f_data.insight=insight;
        
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/popups/insight.html?t='+t,
          keyboard:false,
          backdrop:'static',
          controller: insightCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
          
        modalInstance.result.then(function (new_insight) {
           
           if(insight_index==-1) {
               $scope.editData.insights.push(new_insight);
            }
            else {
               $scope.editData.insights[insight_index]=new_insight;  
            }
            
            autoSave();
           
        }, function () {
            
        });
    };
    
    /////////////////////////////////////////////////////////////////
    
    
    $scope.uploadCsvFileTrivia = function() {
      var data_expected=[
          {
              field:"question",
              name:"Question",
              type:"text",
              max_length:90
          },
          {
              field:"A",
              name:"Answer A",
              type:"text",
              max_length:80
          },
          {
              field:"B",
              name:"Answer B",
              type:"text",
              max_length:80
          },
          {
              field:"C",
              name:"Answer C",
              type:"text",
              max_length:80
          },
          {
              field:"D",
              name:"Answer D",
              type:"text",
              max_length:80
          },
          {
              field:"answer",
              name:"Correct answer (1,2,3,4)",
              type:"text",
              max_length:1
          }
      ];
      
      
      var f_data={};
      f_data.data_expected=data_expected;
      f_data.exp_file="http://my.wheeldo.com/getFile/triviaQExample.csv";
      var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/csvUploader.html?t='+t,
          keyboard:true,
          backdrop:'static',
          controller: csvUploaderCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
        
        modalInstance.result.then(function (csvResult) {
            for(i in csvResult[0]) {
                var new_question={};
                new_question.q_data={};
                for(j in data_expected) {
                    var field=data_expected[j].field;
                    
                    // fix for answer:
                    if(field=="answer") {
                        new_question.q_data[field] = getTriviaAnswerFix(csvResult[j][i]);
                    }
                    else {
                        new_question.q_data[field] = csvResult[j][i];
                    }
                }
                new_question.q_data['userID']=0;
                new_question.q_data['q_id']=0;
                new_question.q_data['is_bonus']="N";
                $scope.editData.quiz.push(new_question);
            }
           
        }, function () {
            
        });
    };
    
    getTriviaAnswerFix = function(str) {
        switch(str){
           case "A":
           case "B":
           case "C":
           case "D":
               return str;
           break;
           case "1":return "A";break;
           case "2":return "B";break;
           case "3":return "C";break;
           case "4":return "D";break;
           default:
               return "A";
           break;     
        }
    };
    
    
    
    
    
    
    
    
    makeDataReadyToSend = function(editData) {
      var editDataJson=JSON.stringify(editData);
      editDataJson=editDataJson.replace(/'/g,"\\\"");
      editDataJson=editDataJson.replace(/&/g,"___amp___");
      return editDataJson;
    };
    
    
    autoSave = function() {
        $scope.saveData(true);
    };
    
    $scope.saveDataSync = function(autoSave) {
        $(".save_bar").removeClass("error");
        autoSave = typeof autoSave !== 'undefined' ? autoSave : false;
        if(autoSave) {
            $(".save_text").html("Auto saving...");
        }
        else {
            $(".save_text").html("Saving...");
        }
        
        $(".save_bar").show();
        //setSaving();
        //console.log($scope.editData);
        var editDataReady=makeDataReadyToSend($scope.editData);
        //console.log(editDataReady);
        var res=setEditData($routeParams.app,copyID,editDataReady);
        //savingDone();
        if(res.status!="ok") {
            $(".save_bar").addClass("error");
            $(".save_text").html("Error occured, Error:::"+res.error+(lastSave!=0?" (last successful save in "+lastSave+")":""));
        }
        else {
            lastSave=getCurrTime();
            if(autoSave) {
                $(".save_text").html("Auto saved (last save in "+getCurrTime()+")");
            }
            else {
                $(".save_text").html("Saved (last save in "+getCurrTime()+")");
            }
        }
    };
    
    $scope.saveData = function(autoSave) {
        $(".save_bar").removeClass("error");
        autoSave = typeof autoSave !== 'undefined' ? autoSave : false;
        if(autoSave) {
            $(".save_text").html("Auto saving...");
        }
        else {
            $(".save_text").html("Saving...");
        }
        
        $(".save_bar").show();

        var editDataReady=makeDataReadyToSend($scope.editData);
        

          $.ajax({
                type: "post",
                dataType:"json",
                url: DATA_PATH + "gt" ,
                data:{
                    op:"setEditData",
                    appID:$routeParams.app,
                    copyID:copyID,
                    data:editDataReady
                },
                success: function(data) {
                    var res=data;
                    if(res.status!="ok") {
                        $(".save_bar").addClass("error");
                        $(".save_text").html("Error occured, Error:::"+res.error+(lastSave!=0?" (last successful save in "+lastSave+")":""));
                    }
                    else {
                        lastSave=getCurrTime();
                        if(autoSave) {
                            $(".save_text").html("Auto saved (last save in "+getCurrTime()+")");
                        }
                        else {
                            $(".save_text").html("Saved (last save in "+getCurrTime()+")");
                        }
                    }  
                }
        }); 
    };
    
    var lastSave=0;
    getCurrTime = function() {
        var d = new Date();
        var h=d.getHours()>9?d.getHours():"0"+d.getHours();
        var m=d.getMinutes()>9?d.getMinutes():"0"+d.getMinutes();
        var s=d.getSeconds()>9?d.getSeconds():"0"+d.getSeconds();
        return h+":"+m+":"+s;
    };
    
    $scope.hideSaveInfo = function() {
        $(".save_bar").hide();
    };
    
    $scope.publishRes="";
    $scope.showSaveAndPublish=true;
    
    $scope.saveAndPublish = function() {
        if(trial_expired) {
            $scope.saveData();
            alert_system('<div style="text-align:center;">Your trial period has expired.<br /> To publish your saved data <a href="/#/purchase" style="font-weight:bold;">purchase a wheeldo package</a>.</div>');
            return;
        }
        //$("#saveAndPublishButton").hide();
        
        if($("#saveAndPublishButton").hasClass("disabled"))
            return;
        
        $("#saveAndPublishButton").addClass("disabled");
        
        
        $(".wait").show();
        $(".saving").show();
        
        $.ajax({
                type: "post",
                dataType:"json",
                url: DATA_PATH + "gt" ,
                data:{
                    op:"getTeamsList"
                },
                success: function(data) {
                    $(".wait").hide();
                    $(".saving").hide();
                    $scope.saveData();
                    var f_data=[];
                    f_data.teamsList=data;
                    f_data.appID=$routeParams.app;
                    f_data.copyID=copyID;
                    f_data.copyData=$scope.copyData;
                    
                    var modalInstance = $modal.open({
                      templateUrl: DATA_PATH+'app/templates/publishGames.html?t='+t,
                      keyboard:false,
                      backdrop:'static',
                      controller: publishGamesCtrl,
                      modal: true,
                      resolve: {
                          f_data:function () {
                            return f_data;
                          }
                      }
                    });
                    
                    modalInstance.result.then(function (publishRes) {
                       $("#saveAndPublishButton").removeClass("disabled");

                    }, function () {

                    });
                }
        }); 
        
        
        
        return;
        
        var f_data=[];
        f_data.teamsList=getTeamsList();
        f_data.appID=$routeParams.app;
        f_data.copyID=copyID;
        f_data.copyData=$scope.copyData;
        
        $scope.saveData();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/publishGames.html?t='+t,
          keyboard:false,
          backdrop:'static',
          controller: publishGamesCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
        
        modalInstance.result.then(function (publishRes) {
           $("#saveAndPublishButton").removeClass("disabled");
           
        }, function () {
            
        });
        
        
        
        //$location.path("/publish/"+$routeParams.app+"/"+copyID)
    };
    
    
    $scope.loadPreviousGames = function() {
        var t=new Date().getTime();
        var f_data=[];
        
        
        $(".wait").show();
        $(".saving").show();
        
        $.ajax({
                type: "post",
                dataType:"json",
                url: DATA_PATH + "gt" ,
                data:{
                    op:"getPreviousGames",
                    appID:$routeParams.app,
                    copyID:copyID
                },
                success: function(data) {
                    
                    f_data.copies=data;
                    var modalInstance = $modal.open({
                    templateUrl: DATA_PATH+'app/templates/loadPreviousGames.html?t='+t,
                    keyboard:true,
                    controller: loadPreviousGamesCtrl,
                    resolve: {
                        f_data:function () {
                          return f_data;
                        }
                    }
                  }); 

                  var loadGameID=0;
                  modalInstance.result.then(function (loadGameID) {
                     $scope.editData=loadPrevEditData($routeParams.app,$routeParams.copyID,loadGameID);
                     window.location.reload();
                  }, function () {

                  });
                }
        }); 
        return;
        
        
        f_data.copies=getPreviousGames($routeParams.app,copyID);
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/loadPreviousGames.html?t='+t,
          keyboard:true,
          controller: loadPreviousGamesCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        }); 
        
        var loadGameID=0;
        modalInstance.result.then(function (loadGameID) {
           $scope.editData=loadPrevEditData($routeParams.app,$routeParams.copyID,loadGameID);
           console.log($scope.editData);
        }, function () {
            
        });
    };
    
    $scope.checkEdit = function() {
      console.log($scope.editData);  
    };
    
    $scope.$watch('editData', function() {
      
    });
    
    
    $scope.addPlayerToGame = function() {
        var t=new Date().getTime();
        var f_data=[];
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/addPlayer.html?t='+t,
          keyboard:true,
          controller: addPlayerCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        }); 
        
        var addPlayer={};
        modalInstance.result.then(function (addPlayer) {
           
           addPlayerToCopyID($routeParams.app,copyID,addPlayer.name,addPlayer.email);
        }, function () {
            
        });
        
        
        
    };
    
    
    
    $scope.teamOptionsOpen = function() {
        var f_data={};
        f_data.copyID=copyID;
        f_data.copyRow=$scope.copyData;
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/gameTeam.html?t='+t,
          keyboard:true,
          controller: gameTeamCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        }); 
        
        var loadGameID=0;
        modalInstance.result.then(function (loadGameID) {

        }, function () {
            
        });
    };
});


app.controller('publishController', function ($scope, $routeParams, $http, $modal, WheeldoService) {
  
    var copyID=$routeParams.copyID;
    var appID=$routeParams.app;
    $scope.appData=getAppInfo(appID);
    $scope.copyData=getCopyInfo(copyID);
    $scope.teamsList=getTeamsList();
    
    
    
    
});


app.controller('reportController', function ($scope, $routeParams, $http, $modal, WheeldoService) {
    
    init();
    function init() {
        $scope.appData=getAppInfo($routeParams.app);
        $scope.copyData=getCopyInfo($routeParams.copyID);
        $scope.reportData=getAppReport($routeParams.app,$routeParams.copyID);
        $scope.finish_load='loaded';
    };

    $scope.reloadReportData = function(type) {
        var reportData=getAppReport($routeParams.app,$routeParams.copyID);
        setReportData(reportData,type);
    };
    
    setReportData = function(reportData,type) {
        $scope.reportData=reportData;
        initReports(type);
    };
    
    
    initReports = function(type) {
        switch(type) {
            case "payItForwardInit":
              $scope.payItForwardInit();  
            break;
            case "guessWotInit":
              $scope.guessWotInit();
            break;
        }
    }
    
    var t=new Date().getTime();
    $scope.template = "app/reports/app_report_"+$routeParams.app+".html?t="+t;
    
    //////////// Mountaineer //////////////////////////////////////////////////////////////////
    $scope.setMountaineerReport = function() {
        
        // get all answers:
        var usersAnswers={};
        for(i in $scope.reportData.user_quiz) {
            var answer=$scope.reportData.user_quiz[i];
            if(answer.userID=="0")
                continue;
            
            if(usersAnswers[answer.userID]) {
                usersAnswers[answer.userID].push(answer);
            }
            else {
                usersAnswers[answer.userID]=[];
                usersAnswers[answer.userID].push(answer);
            }
        }
        
        // players in total:
        
        
        // get all scores:
        var usersScores={};
        for(i in $scope.reportData.score) {
            var score=$scope.reportData.score[i];
            usersScores[score.userID]=score;
        }
        
        //console.log(usersScores);
        
        // get all challenges:
        var usersChallenges={};
        var usersChallengesApproved={};
        var top_challengers_number=0;

        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            if(question.userID=="0")
                continue;
            
            if(usersChallenges[question.userID]) {
                usersChallenges[question.userID]++;
                
                if(question.not_approved=="0") {
                    usersChallengesApproved[question.userID]++;
                    if(top_challengers_number<usersChallengesApproved[question.userID]) {
                        top_challengers_number=usersChallengesApproved[question.userID];
                    }
                }
            }
            else {
                usersChallenges[question.userID]=1;
                if(question.not_approved=="0") {
                    usersChallengesApproved[question.userID]=1;
                    if(top_challengers_number<usersChallengesApproved[question.userID]) {
                        top_challengers_number=usersChallengesApproved[question.userID];
                    }
                }
                
            }  
        }
        
        //usersChallengesApproved.sort(); 

        // load participants report:
        var activeUsers=0;
        var inactiveUsers=0;
        var userByID={};
        
        for(i in $scope.reportData.user) {
            var user=$scope.reportData.user[i];
            var userID=user.user_id;
            if(usersAnswers[userID]) {
                activeUsers++;
            }
            else {
                inactiveUsers++;
            }
            
            userByID[userID]=user.user_name;
        }
        
        $scope.active_players=activeUsers;
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Active Users"},
                {v: activeUsers}
            ]},
            {c: [
                {v: "Inactive users"},
                {v: inactiveUsers}
            ]}
        ]};
        throwChart("chart_participants",data,"PieChart","Active users","320px","200px");
        
        
        // load success report:
        var rightAnswers=0;
        var wrongAnswers=0;
        var q_data={};
        
        for(i in $scope.reportData.user_quiz) {
            var answer=$scope.reportData.user_quiz[i];
            
            if(q_data[answer.q_id]) {
                q_data[answer.q_id].total++;
                q_data[answer.q_id].total_points+=parseInt(answer.points);
                if(answer.currect=="1") {
                    q_data[answer.q_id].right++
                }
            }
            else {
                q_data[answer.q_id]={};
                q_data[answer.q_id].total=1;
                q_data[answer.q_id].right=0;
                q_data[answer.q_id].total_points=parseInt(answer.points);
                
                if(answer.currect=="1") {
                    q_data[answer.q_id].right++
                }
            }
            
            
            if(answer.currect=="0") {
                wrongAnswers++;
            }
            else {
                rightAnswers++;
            }
        }

        
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Right answers"},
                {v: rightAnswers}
            ]},
            {c: [
                {v: "Wrong answers"},
                {v: wrongAnswers}
            ]}
        ]};
        throwChart("chart_success",data,"PieChart","Success rate","320px","200px");
        
        
        // load user answers report:
        var adminQuestions=0;
        var userQuestions=0;
        var challenges_by_users=0;
        
        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            if(question.userID=="0") {
                adminQuestions++;
            }
            else { 
                if(question.not_approved=="0"){
                    userQuestions++;
                }
                challenges_by_users++;
            }
        }
        
        $scope.challenges_by_users=challenges_by_users;
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Admin questions"},
                {v: adminQuestions}
            ]},
            {c: [
                {v: "Users questions (approved)"},
                {v: userQuestions}
            ]}
        ]};
        throwChart("chart_users_answers",data,"PieChart","Users questions VS Admin questions","320px","200px");
        
        
        // load users report:
        var usersData=[];
        var scoresData=[];
        var leader={};
        leader.score=0;
        
        var leading_in_answers={};
        leading_in_answers.rate=0;
        
        var final_in_answers={};
        final_in_answers.rate=100;
        
        var top_chalegers=[];

        // fix for big data:
        var bd=false;
        if($scope.reportData.user.length>1000) {
            bd=true;
        }
        
        var tuc=0;
        
        for(i in $scope.reportData.user) {
            var user=$scope.reportData.user[i];
            var userID=user.user_id;
            var right=0;
            var wrong=0;
            var total=0;
            for(j in usersAnswers[userID]) {
                var answer=usersAnswers[userID][j];
                if(answer.currect=="0") 
                    wrong++;
                else 
                    right++;
                total++;
            }
            
            
            var score=0;
            if(usersScores[userID]) {
                score=usersScores[userID].score;
            }
            

            if(usersAnswers[userID]) {
                usersData[tuc]={};
                usersData[tuc].c=[];
                usersData[tuc].c.push({v:"empID",v:user.empID});
                usersData[tuc].c.push({v:"name",v:user.user_name});
                usersData[tuc].c.push({v:"right",v:right});
                usersData[tuc].c.push({v:"wrong",v:wrong});
                usersData[tuc].c.push({v:"total",v:parseInt(total)});
                usersData[tuc].c.push({v:"challenges",v:usersChallenges[userID]?usersChallenges[userID]:0});
                usersData[tuc].c.push({v:"score",v:score});



                scoresData[tuc]={};
                scoresData[tuc].c=[];
                scoresData[tuc].c.push({v:"name",v:user.user_name});
                scoresData[tuc].c.push({v:"score",v:parseInt(score)});
            
                tuc++;
            }
            if(!usersAnswers[userID]) 
                continue;
            
            var cScore=parseInt(usersScores[userID].score);
            if(cScore>leader.score) {
                //console.log(cScore);
                leader.name=user.user_name;
                leader.score=cScore;
            }
            
            var total=parseInt(j)+1;
            var rateRight=right/total*100;
            var rateWrong=wrong/total*100;
            
            if(rateRight>leading_in_answers.rate) {
                leading_in_answers.name=user.user_name;
                leading_in_answers.rate=Math.round(rateRight * 10) / 10;
            }
            
            if(rateRight<final_in_answers.rate) {
                final_in_answers.name=user.user_name;
                final_in_answers.rate=Math.round(rateRight * 10) / 10;
            }
            
            
            
            // top_challengers_number:
            
            if(usersChallengesApproved[userID] && usersChallengesApproved[userID]==top_challengers_number) {
                top_chalegers.push(user.user_name);
            }
        }
        
        $scope.top_chalegers=top_chalegers;
        $scope.top_challengers_number=top_challengers_number;
        
        $scope.leader=leader;
        $scope.leading_in_answers=leading_in_answers;
        $scope.final_in_answers=final_in_answers;
        
        
        var data={"cols": [
            {id: "empID", label: "Employee ID", type: "string"},
            {id: "name", label: "Name", type: "string"},
            {id: "right", label: "Correct Answer", type: "number"},
            {id: "wrong", label: "Wrong answers", type: "number"},
            {id: "total", label: "Total answers", type: "number"},
            {id: "challenges", label: "Challenges", type: "number"},
            {id: "score", label: "Score", type: "number"}
        ]};
    
         data.rows=usersData;

         throwChart("chart_users",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
        
        // scorers graph:
        
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "score", label: "Height (feet)", type: "number"}
        ]};
    
        data.rows=scoresData;
        
        if(!bd) throwChart("chart_users_scores",data,"ColumnChart","Scores","1050px","400px",["#33ADDF"],true,"Users","Height (feet)");
        
        
        // answers:
        var questionsData=[];
        var most_difficult_question={};
        most_difficult_question.rate=100;
        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            questionsData[i]={};
            questionsData[i].c=[];
            if(question.userID=="0") {
                questionsData[i].c.push({v:"question",v:question.question});
            }
            else {
                questionsData[i].c.push({v:"question",v:question.question+" (By - "+userByID[question.userID]+")"});
            }
            if(q_data[question.q_id]) {
                var rate=Math.round(q_data[question.q_id].right/q_data[question.q_id].total * 10) / 10;
                questionsData[i].c.push({v:"total_answers",v:q_data[question.q_id].total});
                questionsData[i].c.push({v:"rate",v:rate*100+"%"});
                
                var avg_time=Math.round(q_data[question.q_id].total_points/10/q_data[question.q_id].right * 10) / 10;;
                
                avg_time=60-avg_time;
                questionsData[i].c.push({v:"avg_time",v:avg_time+" seconds"});
                if(rate*100<most_difficult_question.rate) {
                    most_difficult_question.rate=rate*100;
                    most_difficult_question.question=question.question;
                }
                
            }
            else {
                questionsData[i].c.push({v:"total_answers",v:0});
                questionsData[i].c.push({v:"rate",v:0});
            }
            
        }
        
        $scope.most_difficult_question=most_difficult_question;
        
        var data={"cols": [
            {id: "question", label: "Question", type: "string"},
            {id: "total_answers", label: "Total answers", type: "number"},
            {id: "rate", label: "Success rate", type: "string"},
            {id: "avg_time", label: "Average time (for answer correctly)", type: "string"}
            
        ]};
    
         data.rows=questionsData;

        throwChart("chart_questions",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
    };
    
    
    
    ////////////////////////////////////////////////////////// end of mountaineer ////////////////////////////////////////////////////////////////////////////
    $scope.payItForwardInit = function() {
        
        
        var keyByTip={};
        for(i in $scope.reportData.game_values) {
            var value=$scope.reportData.game_values[i];
            keyByTip[value.value]=i;
        }
        
        
        
        var scoreByID={};
        
        for(i in $scope.reportData.scores) {
            var score=$scope.reportData.scores[i];
            scoreByID[score.userID]=parseInt(score.score);
        }
  
        var userByID=[];
        userByID[0]="N/A";
        var activeUsers=0;
        var inactiveUsers=0;
        var scoresData=[];
        var users_activity={};
        
        
        var leader={};
        leader.score=0;
        
        for(i in $scope.reportData.users) {
            var user=$scope.reportData.users[i];
            var userID=user.user_id;
            users_activity[userID]={recive:0,send:0,personal_notes:0,posts:parseInt(user.posts)+parseInt(user.comments)};
            if(scoreByID[userID] && scoreByID[userID]>0) {
                activeUsers++;
            }
            else {
                inactiveUsers++;
            }
            
            userByID[userID]=user.user_name;
            
            
            scoresData[i]={};
            scoresData[i].c=[];
            scoresData[i].c.push({v:"name",v:user.user_name});
            scoresData[i].c.push({v:"score",v:scoreByID[userID]});
            
            
            
            if(scoreByID[userID]>leader.score) {
                leader.name=user.user_name;
                leader.score=scoreByID[userID];
            }
        }
        

        
        $scope.leader=leader;
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "score", label: "Points", type: "number"}
        ]};
    
        data.rows=scoresData;
        
        throwChart("chart_users_scores",data,"ColumnChart","Scores","1050px","400px",["#33ADDF"],true,"Users","Points");
        
        $scope.active_players=activeUsers;
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Active Users"},
                {v: activeUsers}
            ]},
            {c: [
                {v: "Inactive users"},
                {v: inactiveUsers}
            ]}
        ]};
        throwChart("chart_participants",data,"PieChart","Active users","320px","200px");
        
        
        
        var total_boxes=0;
        var total_exchanges=0;
        var with_personal_note=0;
        var without_personal_note=0;
        var userNotes={};
        
        var tips_stat={};
        
        var boxesData=[];
        var c=0;
        
        for(i in $scope.reportData.boxes) {
            total_boxes++;
            var box=$scope.reportData.boxes[i];
            
            for(j in box.exchanges) {
                total_exchanges++;
                var exchange=box.exchanges[j];
                
                
                if(exchange.passToUserID!="0") { //box sent:
                    if(exchange.passToMassage=="") {
                        without_personal_note++;
                    }
                    else {
                        with_personal_note++
                        if(userNotes[exchange.userID]) {
                            userNotes[exchange.userID].push(exchange.passToMassage);
                        }
                        else {
                            userNotes[exchange.userID]=[];
                            userNotes[exchange.userID].push(exchange.passToMassage);
                        }
                    }
                    
                    
                    
                }
                
                
                if(tips_stat[keyByTip[exchange.passToTip]]) {
                    tips_stat[keyByTip[exchange.passToTip]].counter++;
                }
                else {
                    if(exchange.passToTip!="") {
                        tips_stat[keyByTip[exchange.passToTip]]={};
                        tips_stat[keyByTip[exchange.passToTip]].tip=exchange.passToTip;
                        tips_stat[keyByTip[exchange.passToTip]].counter=1;
                    }
                }
                
                // activity marking:
                //if(users_activity[]
                
                if(exchange.passToUserID!="0")
                    if(users_activity[exchange.passToUserID])
                        users_activity[exchange.passToUserID].recive++;
                    else
                        users_activity[exchange.passToUserID]=1;
                
                if(exchange.passToUserID!="0") {
                    users_activity[exchange.userID].send++;
                    
                    if(exchange.passToMassage!="") {
                        users_activity[exchange.userID].personal_notes++;
                    }
                }
                
                
                var exchangeTime=timeConverter(exchange.time);
    
                boxesData[c]={};
                boxesData[c].c=[];
                boxesData[c].c.push({v:"boxID",v:box.boxID});
                boxesData[c].c.push({v:"time",v:exchangeTime});
                boxesData[c].c.push({v:"exchange",v:(parseInt(exchange.exchange)+1)});
                boxesData[c].c.push({v:"opener",v:userByID[exchange.userID]});
                boxesData[c].c.push({v:"win",v:exchange.win});
                boxesData[c].c.push({v:"sent_to",v:userByID[exchange.passToUserID]});
                boxesData[c].c.push({v:"sent_value",v:exchange.passToTip});
                boxesData[c].c.push({v:"sent_message",v:exchange.passToMassage});
                c++;
                
            }
        }


        
        var recive_the_most={};
        recive_the_most.c=0;
        
        var send_the_most={};
        send_the_most.c=0;
        
        var note_the_most={};
        note_the_most.c=0;
        
        var user_activity_score={};
        user_activity_score.score=0;
        
        
        
        var data={};
        data.rows=[];
        var scoresData=[];
        var recognitionUserData=[];
        
        var c=0;
        for(i in users_activity) {
           //recive
           //send
           //personal_notes
           //posts
           var userID=i;
           var activity=users_activity[i];
           var score=(activity.recive+activity.send+activity.posts+(activity.personal_notes*6))*activity.send;
           
           if(score>user_activity_score.score) {
               user_activity_score.name=userByID[userID];
               user_activity_score.score=score;
               user_activity_score.activity=activity;
           }
           
           
           // the reciver::::
           if(activity.recive>recive_the_most.c) {
                recive_the_most.name=userByID[userID];
                recive_the_most.c=activity.recive;
           }
           
           // the sender:::
           if(activity.send>send_the_most.c) {
                send_the_most.name=userByID[userID];
                send_the_most.c=activity.send;
           }
           
           // the noter:::
           if(activity.personal_notes>note_the_most.c) {
                note_the_most.name=userByID[userID];
                note_the_most.c=activity.personal_notes;
           }
           
           
           
           
           
           
           
           
           // fill users table:
           
           var notes_user="("+activity.personal_notes+") ";
           for(k in userNotes[userID]) {
                if(k!=0) notes_user+=" ; ";
               notes_user+=(parseInt(k)+1)+". ";
               notes_user+='["'+userNotes[userID][k]+'"]';
           }
           
           
           
            scoresData[c]={};
            scoresData[c].c=[];
            scoresData[c].c.push({v:"name",v:userByID[userID]});
            scoresData[c].c.push({v:"receive",v:activity.recive});
            scoresData[c].c.push({v:"sent",v:activity.send});
            scoresData[c].c.push({v:"notes_user",v:notes_user});
            scoresData[c].c.push({v:"score",v:scoreByID[userID]});


           ////////////////////
           
           
            // recognition chart:
            recognitionUserData[c]={};
            recognitionUserData[c].c=[];
            recognitionUserData[c].c.push({v:"name",v:userByID[userID]});
            recognitionUserData[c].c.push({v:"receive",v:activity.recive});
            recognitionUserData[c].c.push({v:"sent",v:activity.send});
            recognitionUserData[c].c.push({v:"score",v:scoreByID[userID]});
            recognitionUserData[c].c.push({v:"total",v:activity.send+activity.recive});
           c++;
        }

    
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "receive", label: "Receive", type: "string"},
            {id: "sent", label: "Sent", type: "string"},
            {id: "notes_user", label: "Personal messages written by the user", type: "string"},
            {id: "score", label: "Points", type: "number"}
        ]};
    
        data.rows=scoresData;
        
        throwChart("chart_users",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
        
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "receive", label: "Receive", type: "number"},
            {id: "sent", label: "Sent", type: "number"},
            {id: "score", label: "Score", type: "number"},
            {id: "total", label: "Total", type: "number"},
        ]};
    
        data.rows=recognitionUserData;
        
        throwChart("recognition_users",data,"BubbleChart","Peer Recognition Map (Sent VS Received)","1050px","400px",['#33ADDF','#E14F4F','#85C7E9','#C01D4A','#C096B8','#002156'],false,"Receive","Sent");

        
        
        $scope.most_active_player=user_activity_score;
        $scope.recive_the_most=recive_the_most;
        $scope.send_the_most=send_the_most;
        $scope.note_the_most=note_the_most;

        
        
        var most_popular_tip={};
        most_popular_tip.c=0;
        
        var data={};
        data.cols=[];
        data.rows=[];
        for(i in tips_stat) {
            var tip=tips_stat[i];
            
            
            //
            if(tip.counter>most_popular_tip.c) {
                most_popular_tip.tip=tip.tip;
                most_popular_tip.c=tip.counter;
            }
            //
            
            
            data.cols.push({id: "tip_"+i, label: tip.tip, type: "string"});
            data.rows.push({c: [{v: tip.tip},{v: tip.counter}]});
        }
        throwChart("chart_tips",data,"PieChart","Tips","320px","200px");
        
        $scope.most_popular_tip=most_popular_tip;

        
        $scope.total_exchanges=total_exchanges;
        $scope.total_boxes=total_boxes;
        $scope.with_personal_note=with_personal_note;
        $scope.without_personal_note=without_personal_note;
        
        
        var data={"cols": [
            {id: "with_personal_note", label: "Total users", type: "string"},
            {id: "without_personal_note", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Pesonal note"},
                {v: with_personal_note}
            ]},
            {c: [
                {v: "No personal note"},
                {v: without_personal_note}
            ]}
        ]};
        throwChart("chart_users_notes",data,"PieChart","Pesonal notes","320px","200px");
        
        
        
        // boxes table:
        
        
        
        var data={"cols": [
            {id: "boxID", label: "ID", type: "number"},
            {id: "time", label: "Time", type: "string"},
            {id: "exchange", label: "No in Exchange", type: "number"},
            {id: "opener", label: "Opener", type: "string"},
            {id: "win", label: "Win box", type: "string"},
            {id: "sent_to", label: "Sent to", type: "string"},
            {id: "sent_value", label: "Sent with tip", type: "string"},
            {id: "sent_message", label: "Sent with message", type: "string"}
        ]};
    
        data.rows=boxesData;
        
        throwChart("chart_boxes",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
        
    };
    
    //////////////////////////////////////////////////////// voter /////////////////////////////////////////
    
    $scope.setVoterReport = function() {
        
        var userByID=[];
        userByID[0]="N/A";
        var activeUsers=0;
        var inactiveUsers=0;
        var scoresData=[];
        var users_activity={};
        
        
        var leader={};
        leader.score=0;
        
        for(i in $scope.reportData.users) {
            var user=$scope.reportData.users[i];
            var userID=user.user_id;
            users_activity[userID]={recive:0,send:0,personal_notes:0,posts:parseInt(user.posts)+parseInt(user.comments)};
            if(user.play=="1") {
                activeUsers++;
            }
            else {
                inactiveUsers++;
            }
            
            userByID[userID]=user.user_name;

        }
        $scope.active_players=activeUsers;
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Active Users"},
                {v: activeUsers}
            ]},
            {c: [
                {v: "Inactive users"},
                {v: inactiveUsers}
            ]}
        ]};
        throwChart("chart_participants",data,"PieChart","Active users","320px","200px");
        
        
        
        var answerByPoints=[];
        var answersData=[];
        
        var leading_idea={};
        leading_idea.points=0;
        
        
        
        
        var activePlayersByID={};
        
        var mostRatedPlayer={};
        mostRatedPlayer.c=0;
        
        var mostRatedIdea={};
        mostRatedIdea.c=0;
        
        for(i in $scope.reportData.answers) {
            var answer=$scope.reportData.answers[i];
            
            
            var voteRs=0;
            var voteC=0;
            for(j in answer.votes) {
                var vote=answer.votes[j];
                voteRs+=parseInt(vote.amount);
                voteC++;
            }
            
            var resAnswer={};
            
            resAnswer.answer_id=answer.id;
            resAnswer.title=answer.answer_description;
            resAnswer.description=answer.answer;
            resAnswer.img=answer.img_link;
            resAnswer.points=voteRs;
            
            
            
            
            answerByPoints.push(resAnswer);
            
            
            var userID=answer.user_id;
            var user="Admin";
            
            if(userID!="0") {
                user=userByID[userID];
                
                if(activePlayersByID[userID]){
                    activePlayersByID[userID]++;
                }
                else {
                    activePlayersByID[userID]=1;
                }
                
                
                if(mostRatedPlayer.c<voteC) {
                    mostRatedPlayer.c=voteC;
                    mostRatedPlayer.name=user;
                    
                }
                
            }
            
            
            if(mostRatedIdea.c<voteC) {
                mostRatedIdea.c=voteC;
                mostRatedIdea.idea=answer.answer_description;
            }
            
            answersData[i]={};
            answersData[i].c=[];
            answersData[i].c.push({v:"title",v:answer.answer_description});
            answersData[i].c.push({v:"description",v:answer.answer});
            answersData[i].c.push({v:"user",v:user});
//            answersData[i].c.push({v:"time",v:1});
            answersData[i].c.push({v:"voted",v:voteC});
            answersData[i].c.push({v:"points",v:voteRs});


            
            if(leading_idea.points<voteRs) {
                leading_idea.points=voteRs;
                leading_idea.idea=answer.answer_description;
            }
            
        }
        
        //console.log(activePlayersByID);
        var mostActivePlayer={};
        mostActivePlayer.counter=0;
        
        for(i in activePlayersByID) {
            var userID=i;
            var c=activePlayersByID[i];
            
            if(mostActivePlayer.counter<c) {
                mostActivePlayer.counter=c;
                mostActivePlayer.name=userByID[userID];
            }
        }
        
        $scope.mostActivePlayer=mostActivePlayer;
        
        $scope.leading_idea=leading_idea;
        $scope.mostRatedPlayer=mostRatedPlayer;
        $scope.mostRatedIdea=mostRatedIdea;
        
        
        answerByPoints.sort(function(a,b) {
            var x = a.points;
            var y = b.points;
            return x < y;
        });
        
        var answersToGraph=[];
        for(i in answerByPoints) {
            answer=answerByPoints[i];
            
            
            var tooltip=
                '<div class="answer_tooltip">' +
                    '<table>'+
                    '<tr>'+
                    '<th>Title:</th>'+
                    '<td>'+answer.title+'</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<th>Points:</th>'+
                    '<td>'+answer.points+'</td>'+
                    '</tr>'+
                    '<tr>'+
                    '<th>Description:</th>'+
                    '<td>'+answer.description+'</td>'+
                    '</tr>';
                if(answer.img!="") {
                    tooltip+='<tr>'+
                            '<th>Img:</th>'+
                            '<td><img src="http://apps.wheeldo.com/Voter/'+answer.img+'" /></td>'+
                            '</tr>';
                }
                    tooltip+='</table>'+
                    '</div>';

            
            answersToGraph[i]={}
            answersToGraph[i].c=[];
            answersToGraph[i].c.push({v:"title",v:answer.title});
            answersToGraph[i].c.push({v:"points",v:answer.points});
            answersToGraph[i].c.push({v:"title",v:tooltip,p:{}});
        }
        

        
         var data={"cols": [
            {id: "title", label: "Title", type: "string"},
            {id: "points", label: "Points", type: "number"},
            {id: "", role: "tooltip", type: "string",p:{role: "tooltip",html: true}}
        ]};
    
        data.rows=answersToGraph;
        
        throwChart("chart_users_scores",data,"ColumnChart","Answers votes:","1050px","400px",["#33ADDF"],true,"Answers","Points",true);
        
        
      // users:
      
        var data={"cols": [
            {id: "title", label: "Idea Title", type: "string"},
            {id: "description", label: "Idea Description", type: "string"},
            {id: "user", label: "User", type: "string"},
//            {id: "time", label: "Time", type: "string"},
            {id: "voted", label: "No of votes", type: "number"},
            {id: "points", label: "Points", type: "number"}
        ]};
    
        data.rows=answersData;
        
        throwChart("chart_answers",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
    };
    
    
    /////////////////////////////////////////////////////////// GuessWot //////////////////////////////////////
    
    $scope.guessWotInit = function() {
        
        
        //console.log($scope.reportData.filters);
        $scope.getFullGuessWotReport = function() {
            var res=getGuessWotFullReport($routeParams.app,$routeParams.copyID);
            window.location.href = res.link;
        };
        
        
        
        $scope.filterData = function() {
            var filterArray={};
            $(".filters").each(function(){
               if($(this).is(":checked")) {
                    if(filterArray[$(this).attr("cat")])
                        filterArray[$(this).attr("cat")].push($(this).attr("val"));
                    else {
                        filterArray[$(this).attr("cat")]=[];
                        filterArray[$(this).attr("cat")].push($(this).attr("val"));
                    }     
               }
            });
            
            
            
            var editDataJson=JSON.stringify(filterArray);
            editDataJson=editDataJson.replace(/'/g,"\\\"");
            editDataJson=editDataJson.replace(/&/g,"___amp___");
            
            var data=getGuessWotFilteredData($routeParams.app,$routeParams.copyID,editDataJson);
            var filters=$scope.reportData.filters;
            $scope.reportData=data;
            $scope.reportData.filters=filters;
            $scope.guessWotInit();
        };
        

        var scoreByID={};
        for(i in $scope.reportData.scores) {
            var score=$scope.reportData.scores[i];
            scoreByID[score.userID]=parseInt(score.score);
        }
        
        var userByID=[];
        userByID[0]="N/A";
        var activeUsers=0;
        var inactiveUsers=0;
        var scoresData=[];
        var users_activity={};
        
        
        var leader={};
        leader.score=0;
        
        
        
        for(i in $scope.reportData.users) {
            var user=$scope.reportData.users[i];
            
            var userID=user.user_id;
            users_activity[userID]={recive:0,send:0,personal_notes:0,posts:parseInt(user.posts)+parseInt(user.comments)};
            if(scoreByID[userID] && scoreByID[userID]>0) {
                activeUsers++;
            }
            else {
                inactiveUsers++;
            }
            
            userByID[userID]=user.user_name;
            
            
            scoresData[i]={};
            scoresData[i].c=[];
            scoresData[i].c.push({v:"id",v:user.user_id});
            scoresData[i].c.push({v:"name",v:user.user_name});
            scoresData[i].c.push({v:"active",v:scoreByID[userID]?1:0});
            scoresData[i].c.push({v:"score",v:scoreByID[userID]?scoreByID[userID]:0});
            scoresData[i].c.push({v:"re_score",v:$scope.reportData.new_score_by_id[userID]});
            
            
            
            if(scoreByID[userID]>leader.score) {
                leader.name=user.user_name;
                leader.score=scoreByID[userID];
            }
        }
        
        
        //raw_users
        

        
        $scope.leader=leader;
        var data={"cols": [
            {id: "id", label: "ID", type: "number"},
            {id: "name", label: "Name", type: "string"},
            {id: "active", label: "Active", type: "number"},
            {id: "score", label: "Points", type: "number"},
            {id: "re_score", label: "Recalculated Points", type: "number"}
        ]};
    
        data.rows=scoresData;
        
        throwChart("raw_users",data,"Table","Scores","1050px","400px",["#33ADDF"],true,"Users","Points");
        
        $scope.active_players=activeUsers;
        
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Active Users"},
                {v: activeUsers}
            ]},
            {c: [
                {v: "Inactive users"},
                {v: inactiveUsers}
            ]}
        ]};
        throwChart('chart_participants',data,"PieChart","Active users","320px","200px");
        
        
        // questions data table:
        questionsData=[];
        questionsData[0]=null;
        
        var type1c=0;
        var type2c=0;
        var type3c=0;
        var type4c=0;
        var type5c=0;
        
        
        var questionsByType=[];
        questionsByType[0]=null;
        
        var questions_texts=[];
        
        for(i in $scope.reportData.q_types_q) {
            var q=$scope.reportData.q_types_q[i].q;
            var type=q.type;
            var q_text=jQuery.parseJSON(q.text);
            var stats=$scope.reportData.q_types_q[i].stats;
           questions_texts.push(q_text);
           
        }
        
        $scope.questions_texts=questions_texts;
        $scope.setQStats = function(index,q_type,condition) {

            q_type = typeof q_type !== 'undefined' ? q_type : false;
            condition = typeof condition !== 'undefined' ? condition : "NA";
            
            
            
            var q=null;
            q=$scope.reportData.q_types_q[index];
            var q_text=jQuery.parseJSON(q.q.text);

            
            
            var sw_type=q.q.type;
            
            if(q_type && q_type=="5") {
                sw_type="3";
            }
            
            
            if(q_type && q_type=="4") {
                sw_type="1";
            }


            
            var rows=[];
            var colors=['#33ADDF','#E14F4F','#85C7E9','#C01D4A','#C096B8','#002156'];
            switch(sw_type) {
                case "1":
                case "4":
                case "5":
                    var cols=[
                        {id: "range", label: "Range", type: "string"},
                        {id: "votes", label: "Votes", type: "number"}
                    ];
                    
                    var bin=[0,0,0,0,0];
                    
                    for(k in q.user_data) {
                        var ud=q.user_data[k];
                        if(ud.p=="1")
                            continue;
                        
                        var data_ps=jQuery.parseJSON(stripslashes(ud.data));
                        var value=parseInt(data_ps.value);

                        console.log(data_ps);
                        if(q_type && q_type=="4") {
                            if(condition=="true") {
                                value=parseInt(data_ps.if_true_ans);
                                if(data_ps.condition_result=="0")
                                    continue;
                            }
                            if(condition=="false") {
                                if(data_ps.condition_result=="1")
                                    continue;
                                value=parseInt(data_ps.if_false_ans);
                            }
                        }
                        
                        if(value>0&&value<21) bin[0]++;
                        if(value>20&&value<41) bin[1]++;
                        if(value>40&&value<61) bin[2]++;
                        if(value>60&&value<81) bin[3]++;
                        if(value>80&&value<101) bin[4]++;
                    }
                    
                    rows[0]={};
                    rows[0].c=[];
                    rows[0].c.push({v:"range",v:"0-20"});
                    rows[0].c.push({v:"votes",v:bin[0]});
                    
                    rows[1]={};
                    rows[1].c=[];
                    rows[1].c.push({v:"range",v:"21-40"});
                    rows[1].c.push({v:"votes",v:bin[1]});
                    
                    rows[2]={};
                    rows[2].c=[];
                    rows[2].c.push({v:"range",v:"41-60"});
                    rows[2].c.push({v:"votes",v:bin[2]});
                    
                    rows[3]={};
                    rows[3].c=[];
                    rows[3].c.push({v:"range",v:"61-80"});
                    rows[3].c.push({v:"votes",v:bin[3]});
                    
                    rows[4]={};
                    rows[4].c=[];
                    rows[4].c.push({v:"range",v:"81-100"});
                    rows[4].c.push({v:"votes",v:bin[4]});
                break;
                
                case "2":
                    colors=["#DC3912","#FF9900","#109618"];
                    var cols=[
                        {id: "value", label: "Values", type: "string"},
                        {id: "low", label: "Low", type: "number"},
                        {id: "med", label: "Medium", type: "number"},
                        {id: "high", label: "High", type: "number"}
                    ];
                    var bin=[];
                    for(l in q_text.values) {
                        var value=q_text.values[l];
                        rows[l]={};
                        rows[l].c=[];
                        rows[l].c.push({v:"value",v:value.text});
                        bin[l]={};
                        bin[l].high=0;
                        bin[l].med=0;
                        bin[l].low=0;
                    }
                    

                    for(k in q.user_data) {
                        var ud=q.user_data[k];
                        if(ud.p=="1")
                            continue;
                        
                        var data_ps=jQuery.parseJSON(stripslashes(ud.data));
                        var values_user=data_ps.answers;

                        for(m in values_user) {
                            var rr=values_user[m];
                            if(rr!=0) {
                                bin[m][rr]++; 
                            }
                        }
                    }
                    

                    
                    for(g in bin) {
                        rows[g].c.push({v:"high",v:bin[g].high});
                        rows[g].c.push({v:"med",v:bin[g].med});
                        rows[g].c.push({v:"low",v:bin[g].low});
                    }
                    
                break;
                
                case "3":
                    var cols=[
                        {id: "option", label: "Option", type: "string"},
                        {id: "votes", label: "Votes", type: "number"}
                    ];
                    
                    var bin=[];
                    var options=q_text.options;
                    
                    if(q_type && q_type=="5") {
                        if(condition=="true")
                            options=q_text.conditionMultiSelect.options_true;
                        if(condition=="false")
                            options=q_text.conditionMultiSelect.options_false;
                    }

                    
                    for(l in options) {
                        var option=options[l];
                        rows[l]={};
                        rows[l].c=[];
                        rows[l].c.push({v:"range",v:option.text});
                        bin[l]=0;
                    }
                    
                    
                    var user_data=q.user_data;

                    
                    for(k in user_data) {
                        var ud=user_data[k];
                        if(ud.p=="1")
                            continue;
                        
                        var data_ps=jQuery.parseJSON(stripslashes(ud.data));
 
                        var options_user=data_ps.options;
                        
                        if(q_type && q_type=="5") {
                            if(condition=="true")
                                options_user=data_ps.if_true_options;
                            if(condition=="false")
                                options_user=data_ps.if_false_options;
                        }
                        
                        for(m in options_user) {
                            var opt=parseInt(options_user[m]);
                            bin[opt]++;
                        }
                    }

                    
                    for(g in bin) {
                        rows[g].c.push({v:"votes",v:bin[g]});
                    }
                    
                break;
            }
            var data={};
            data.cols=cols;
            data.rows=rows;
            
            throwChart('question_graph',data,"ColumnChart","Question data","100%","400px",colors);
        }; 
    };
    
    
            
        //////////// SlideStar //////////////////////////////////////////////////////////////////
        
        $scope.setSlideStarReport = function() {
            
        // get slides:
        
        var slideByID={};
        for(i in $scope.reportData.slides) {
            var slide=$scope.reportData.slides[i];
            slideByID[slide.id]=i;
        }
        //////////////
        
        
        // get all answers:
        var usersAnswers={};
        for(i in $scope.reportData.user_quiz) {
            var answer=$scope.reportData.user_quiz[i];
            if(answer.userID=="0")
                continue;
            
            if(usersAnswers[answer.userID]) {
                usersAnswers[answer.userID].push(answer);
            }
            else {
                usersAnswers[answer.userID]=[];
                usersAnswers[answer.userID].push(answer);
            }
        }

        
        // players in total:
        
        
        // get all scores:
        var usersScores={};
        for(i in $scope.reportData.score) {
            var score=$scope.reportData.score[i];
            usersScores[score.userID]=score;
        }
        
        //console.log(usersScores);
        
        // get all challenges:
        var usersChallenges={};
        var usersChallengesApproved={};
        var top_challengers_number=0;

        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            if(question.userID=="0")
                continue;
            
            if(usersChallenges[question.userID]) {
                usersChallenges[question.userID]++;
                
                if(question.not_approved=="0") {
                    usersChallengesApproved[question.userID]++;
                    if(top_challengers_number<usersChallengesApproved[question.userID]) {
                        top_challengers_number=usersChallengesApproved[question.userID];
                    }
                }
            }
            else {
                usersChallenges[question.userID]=1;
                if(question.not_approved=="0") {
                    usersChallengesApproved[question.userID]=1;
                    if(top_challengers_number<usersChallengesApproved[question.userID]) {
                        top_challengers_number=usersChallengesApproved[question.userID];
                    }
                }
                
            }  
        }
        
        //usersChallengesApproved.sort(); 

        // load participants report:
        var activeUsers=0;
        var inactiveUsers=0;
        var userByID={};
        
        for(i in $scope.reportData.user) {
            var user=$scope.reportData.user[i];
            var userID=user.user_id;
            if(usersAnswers[userID]) {
                activeUsers++;
            }
            else {
                inactiveUsers++;
            }
            
            userByID[userID]=user.user_name;
        }
        
        $scope.active_players=activeUsers;
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Active Users"},
                {v: activeUsers}
            ]},
            {c: [
                {v: "Inactive users"},
                {v: inactiveUsers}
            ]}
        ]};
        throwChart("chart_participants",data,"PieChart","Active users","320px","200px");
        
        
        // load success report:
        var rightAnswers=0;
        var wrongAnswers=0;
        var q_data={};
        
        for(i in $scope.reportData.user_quiz) {
            var answer=$scope.reportData.user_quiz[i];
            
            if(q_data[answer.q_id]) {
                q_data[answer.q_id].total++;
                q_data[answer.q_id].total_points+=parseInt(answer.points);
                if(answer.currect=="1") {
                    q_data[answer.q_id].right++
                }
            }
            else {
                q_data[answer.q_id]={};
                q_data[answer.q_id].total=1;
                q_data[answer.q_id].right=0;
                q_data[answer.q_id].total_points=parseInt(answer.points);
                
                if(answer.currect=="1") {
                    q_data[answer.q_id].right++
                }
            }
            
            
            if(answer.currect=="0") {
                wrongAnswers++;
            }
            else {
                rightAnswers++;
            }
        }

        
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Right answers"},
                {v: rightAnswers}
            ]},
            {c: [
                {v: "Wrong answers"},
                {v: wrongAnswers}
            ]}
        ]};
        throwChart("chart_success",data,"PieChart","Success rate","320px","200px");
        
        
        // load user answers report:
        var adminQuestions=0;
        var userQuestions=0;
        var challenges_by_users=0;
        
        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            if(question.userID=="0") {
                adminQuestions++;
            }
            else { 
                if(question.not_approved=="0"){
                    userQuestions++;
                }
                challenges_by_users++;
            }
        }
        
        $scope.challenges_by_users=challenges_by_users;
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Admin questions"},
                {v: adminQuestions}
            ]},
            {c: [
                {v: "Users questions (approved)"},
                {v: userQuestions}
            ]}
        ]};
        throwChart("chart_users_answers",data,"PieChart","Users questions VS Admin questions","320px","200px");
        
        
        // load users report:
        var usersData=[];
        var scoresData=[];
        var leader={};
        leader.score=0;
        
        var leading_in_answers={};
        leading_in_answers.rate=0;
        
        var final_in_answers={};
        final_in_answers.rate=100;
        
        var top_chalegers=[];

        // fix for big data:
        var bd=false;
        if($scope.reportData.user.length>1000) {
            bd=true;
        }
        
        var tuc=0;
        
        for(i in $scope.reportData.user) {
            var user=$scope.reportData.user[i];
            var userID=user.user_id;
            var right=0;
            var wrong=0;
            var total=0;
            for(j in usersAnswers[userID]) {
                var answer=usersAnswers[userID][j];
                if(answer.currect=="0") 
                    wrong++;
                else 
                    right++;
                total++;
            }
            
            
            var score=0;
            if(usersScores[userID]) {
                score=usersScores[userID].score;
            }
            

            if(usersAnswers[userID]) {
                usersData[tuc]={};
                usersData[tuc].c=[];

                usersData[tuc].c.push({v:"name",v:user.user_name});
                usersData[tuc].c.push({v:"right",v:right});
                usersData[tuc].c.push({v:"wrong",v:wrong});
                usersData[tuc].c.push({v:"total",v:parseInt(total)});
                usersData[tuc].c.push({v:"challenges",v:usersChallenges[userID]?usersChallenges[userID]:0});
                usersData[tuc].c.push({v:"score",v:score});


                scoresData[tuc]={};
                scoresData[tuc].c=[];
                scoresData[tuc].c.push({v:"name",v:user.user_name});
                scoresData[tuc].c.push({v:"score",v:parseInt(score)});
            
                tuc++;
            }
            if(!usersAnswers[userID]) 
                continue;
            
            var cScore=parseInt(usersScores[userID].score);
            if(cScore>leader.score) {
                //console.log(cScore);
                leader.name=user.user_name;
                leader.score=cScore;
            }
            
            var total=parseInt(j)+1;
            var rateRight=right/total*100;
            var rateWrong=wrong/total*100;
            
            if(rateRight>leading_in_answers.rate) {
                leading_in_answers.name=user.user_name;
                leading_in_answers.rate=Math.round(rateRight * 10) / 10;
            }
            
            if(rateRight<final_in_answers.rate) {
                final_in_answers.name=user.user_name;
                final_in_answers.rate=Math.round(rateRight * 10) / 10;
            }
            
            
            
            // top_challengers_number:
            
            if(usersChallengesApproved[userID] && usersChallengesApproved[userID]==top_challengers_number) {
                top_chalegers.push(user.user_name);
            }
        }
        
        $scope.top_chalegers=top_chalegers;
        $scope.top_challengers_number=top_challengers_number;
        
        $scope.leader=leader;
        $scope.leading_in_answers=leading_in_answers;
        $scope.final_in_answers=final_in_answers;
        
        
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "right", label: "Correct Answer", type: "number"},
            {id: "wrong", label: "Wrong answers", type: "number"},
            {id: "total", label: "Total answers", type: "number"},
            {id: "challenges", label: "Challenges", type: "number"},
            {id: "score", label: "Score", type: "number"}
        ]};
    
         data.rows=usersData;

         throwChart("chart_users",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
        
        // scorers graph:
        
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "score", label: "Points", type: "number"}
        ]};
    
        data.rows=scoresData;
        
        if(!bd) throwChart("chart_users_scores",data,"ColumnChart","Scores","1050px","400px",["#33ADDF"],true,"Users","Points");
        
        
        // answers:
        var questionsData=[];
        var most_difficult_question={};
        most_difficult_question.rate=100;
        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            
            
            
            questionsData[i]={};
            questionsData[i].c=[];
            
            questionsData[i].c.push({v:"slide",v:(parseInt(slideByID[question.slide_id])+1)});
            
            if(question.userID=="0") {
                questionsData[i].c.push({v:"question",v:question.question});
            }
            else {
                questionsData[i].c.push({v:"question",v:question.question+" (By - "+userByID[question.userID]+")"});
            }
            if(q_data[question.q_id]) {
                var rate=Math.round(q_data[question.q_id].right/q_data[question.q_id].total * 10) / 10;
                questionsData[i].c.push({v:"total_answers",v:q_data[question.q_id].total});
                questionsData[i].c.push({v:"rate",v:rate*100+"%"});
                
                var avg_time=Math.round(q_data[question.q_id].total_points/10/q_data[question.q_id].right * 10) / 10;;
                
                
                
                
                
                avg_time=60-avg_time;
                if(rate*100<most_difficult_question.rate) {
                    most_difficult_question.rate=rate*100;
                    most_difficult_question.question=question.question;
                }
                
            }
            else {
                questionsData[i].c.push({v:"total_answers",v:0});
                questionsData[i].c.push({v:"rate",v:0});
            }
            
        }

        
        $scope.most_difficult_question=most_difficult_question;
        
        var data={"cols": [
            {id: "slide", label: "Slide No.", type: "string"},
            {id: "question", label: "Question", type: "string"},
            {id: "total_answers", label: "Total answers", type: "number"},
            {id: "rate", label: "Success rate", type: "string"},

            
        ]};
    
         data.rows=questionsData;

        throwChart("chart_questions",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
    };
    
    
    
    ////////////////////////////////////////////////////////// end of slidestar ////////////////////////////////////////////////////////////////////////////
    
    ////////////////////////////////////////////////////////// Trivia ////////////////////////////////////////////////////////////////////////////
    
    
    
        $scope.setTriviaReport = function() {
            
        // get slides:
        
        var slideByID={};
        for(i in $scope.reportData.slides) {
            var slide=$scope.reportData.slides[i];
            slideByID[slide.id]=i;
        }
        //////////////
        
        
        // get all answers:
        var usersAnswers={};
        for(i in $scope.reportData.user_quiz) {
            var answer=$scope.reportData.user_quiz[i];
            if(answer.userID=="0")
                continue;
            
            if(usersAnswers[answer.userID]) {
                usersAnswers[answer.userID].push(answer);
            }
            else {
                usersAnswers[answer.userID]=[];
                usersAnswers[answer.userID].push(answer);
            }
        }

        
        // players in total:
        
        
        // get all scores:
        var usersScores={};
        for(i in $scope.reportData.score) {
            var score=$scope.reportData.score[i];
            usersScores[score.userID]=score;
        }
        
        //console.log(usersScores);
        
        // get all challenges:
        var usersChallenges={};
        var usersChallengesApproved={};
        var top_challengers_number=0;

        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            if(question.userID=="0")
                continue;
            
            if(usersChallenges[question.userID]) {
                usersChallenges[question.userID]++;
                
                if(question.not_approved=="0") {
                    usersChallengesApproved[question.userID]++;
                    if(top_challengers_number<usersChallengesApproved[question.userID]) {
                        top_challengers_number=usersChallengesApproved[question.userID];
                    }
                }
            }
            else {
                usersChallenges[question.userID]=1;
                if(question.not_approved=="0") {
                    usersChallengesApproved[question.userID]=1;
                    if(top_challengers_number<usersChallengesApproved[question.userID]) {
                        top_challengers_number=usersChallengesApproved[question.userID];
                    }
                }
                
            }  
        }
        
        //usersChallengesApproved.sort(); 

        // load participants report:
        var activeUsers=0;
        var inactiveUsers=0;
        var userByID={};
        
        for(i in $scope.reportData.user) {
            var user=$scope.reportData.user[i];
            var userID=user.user_id;
            if(usersAnswers[userID]) {
                activeUsers++;
            }
            else {
                inactiveUsers++;
            }
            
            userByID[userID]=user.user_name;
        }
        
        $scope.active_players=activeUsers;
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Active Users"},
                {v: activeUsers}
            ]},
            {c: [
                {v: "Inactive users"},
                {v: inactiveUsers}
            ]}
        ]};
        throwChart("chart_participants",data,"PieChart","Active users","320px","200px");
        
        
        // load success report:
        var rightAnswers=0;
        var wrongAnswers=0;
        var q_data={};
        
        for(i in $scope.reportData.user_quiz) {
            var answer=$scope.reportData.user_quiz[i];
            
            if(q_data[answer.q_id]) {
                q_data[answer.q_id].total++;
                q_data[answer.q_id].total_points+=parseInt(answer.points);
                if(answer.currect=="1") {
                    q_data[answer.q_id].right++
                }
            }
            else {
                q_data[answer.q_id]={};
                q_data[answer.q_id].total=1;
                q_data[answer.q_id].right=0;
                q_data[answer.q_id].total_points=parseInt(answer.points);
                
                if(answer.currect=="1") {
                    q_data[answer.q_id].right++
                }
            }
            
            
            if(answer.currect=="0") {
                wrongAnswers++;
            }
            else {
                rightAnswers++;
            }
        }

        console.log(q_data);
        
        var data={"cols": [
            {id: "total", label: "Total users", type: "string"},
            {id: "participants", label: "Participants users", type: "string"}
        ], "rows": [
            {c: [
                {v: "Right answers"},
                {v: rightAnswers}
            ]},
            {c: [
                {v: "Wrong answers"},
                {v: wrongAnswers}
            ]}
        ]};
        throwChart("chart_success",data,"PieChart","Success rate","320px","200px");
        
        
        // load user answers report:
        var adminQuestions=0;
        var userQuestions=0;
        var challenges_by_users=0;
        
        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            if(question.userID=="0") {
                adminQuestions++;
            }
            else { 
                if(question.not_approved=="0"){
                    userQuestions++;
                }
                challenges_by_users++;
            }
        }
        
        $scope.challenges_by_users=challenges_by_users;
        

        
        
        // load users report:
        var usersData=[];
        var scoresData=[];
        var leader={};
        leader.score=0;
        
        var leading_in_answers={};
        leading_in_answers.rate=0;
        
        var final_in_answers={};
        final_in_answers.rate=100;
        
        var top_chalegers=[];

        // fix for big data:
        var bd=false;
        if($scope.reportData.user.length>1000) {
            bd=true;
        }
        
        var tuc=0;
        
        for(i in $scope.reportData.user) {
            var user=$scope.reportData.user[i];
            var userID=user.user_id;
            var right=0;
            var wrong=0;
            var total=0;
            for(j in usersAnswers[userID]) {
                var answer=usersAnswers[userID][j];
                if(answer.currect=="0") 
                    wrong++;
                else 
                    right++;
                total++;
            }
            
            
            var score=0;
            if(usersScores[userID]) {
                score=usersScores[userID].score;
            }
            

            if(usersAnswers[userID]) {
                usersData[tuc]={};
                usersData[tuc].c=[];

                usersData[tuc].c.push({v:"name",v:user.user_name});
                usersData[tuc].c.push({v:"right",v:right});
                usersData[tuc].c.push({v:"wrong",v:wrong});
                usersData[tuc].c.push({v:"total",v:parseInt(total)});

                usersData[tuc].c.push({v:"score",v:score});


                scoresData[tuc]={};
                scoresData[tuc].c=[];
                scoresData[tuc].c.push({v:"name",v:user.user_name});
                scoresData[tuc].c.push({v:"score",v:parseInt(score)});
            
                tuc++;
            }
            if(!usersAnswers[userID]) 
                continue;
            
            var cScore=parseInt(usersScores[userID].score);
            if(cScore>leader.score) {
                //console.log(cScore);
                leader.name=user.user_name;
                leader.score=cScore;
            }
            
            var total=parseInt(j)+1;
            var rateRight=right/total*100;
            var rateWrong=wrong/total*100;
            
            if(rateRight>leading_in_answers.rate) {
                leading_in_answers.name=user.user_name;
                leading_in_answers.rate=Math.round(rateRight * 10) / 10;
            }
            
            if(rateRight<final_in_answers.rate) {
                final_in_answers.name=user.user_name;
                final_in_answers.rate=Math.round(rateRight * 10) / 10;
            }
            
            
            
            // top_challengers_number:
            
            if(usersChallengesApproved[userID] && usersChallengesApproved[userID]==top_challengers_number) {
                top_chalegers.push(user.user_name);
            }
        }
        
        $scope.top_chalegers=top_chalegers;
        $scope.top_challengers_number=top_challengers_number;
        
        $scope.leader=leader;
        $scope.leading_in_answers=leading_in_answers;
        $scope.final_in_answers=final_in_answers;
        
        
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "right", label: "Correct Answer", type: "number"},
            {id: "wrong", label: "Wrong answers", type: "number"},
            {id: "total", label: "Total answers", type: "number"},

            {id: "score", label: "Score", type: "number"}
        ]};
    
         data.rows=usersData;

         throwChart("chart_users",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
        
        // scorers graph:
        
        var data={"cols": [
            {id: "name", label: "Name", type: "string"},
            {id: "score", label: "Points", type: "number"}
        ]};
    
        data.rows=scoresData;
        
        if(!bd) throwChart("chart_users_scores",data,"ColumnChart","Scores","1050px","400px",["#33ADDF"],true,"Users","Points");
        
        
        // answers:
        var questionsData=[];
        var most_difficult_question={};
        most_difficult_question.rate=100;
        for(i in $scope.reportData.quiz) {
            var question=$scope.reportData.quiz[i];
            
            
            
            questionsData[i]={};
            questionsData[i].c=[];
            

            

            questionsData[i].c.push({v:"question",v:question.question});
            
            if(q_data[question.q_id]) {
                var rate=Math.round(q_data[question.q_id].right/q_data[question.q_id].total * 10) / 10;
                questionsData[i].c.push({v:"total_answers",v:q_data[question.q_id].total});
                questionsData[i].c.push({v:"rate",v:rate*100+"%"});
                
                var avg_time=Math.round(q_data[question.q_id].total_points/10/q_data[question.q_id].right * 10) / 10;;
                
                
                
                
                
                avg_time=60-avg_time;
                if(rate*100<most_difficult_question.rate) {
                    most_difficult_question.rate=rate*100;
                    most_difficult_question.question=question.question;
                }
                
            }
            else {
                questionsData[i].c.push({v:"total_answers",v:0});
                questionsData[i].c.push({v:"rate",v:0});
            }
            
        }

        
        $scope.most_difficult_question=most_difficult_question;
        
        var data={"cols": [
            {id: "question", label: "Question", type: "string"},
            {id: "total_answers", label: "Total answers", type: "number"},
            {id: "rate", label: "Success rate", type: "string"},

            
        ]};
    
         data.rows=questionsData;

        throwChart("chart_questions",data,"Table","Users Data","100%","",["#2048CB","#F67B00"]);
        
    };    
    ////////////////////////////////////////////////////////// end of trivia ////////////////////////////////////////////////////////////////////////////
    $scope.downloadCsvFile = function(id) {
        if($scope[id].data) {
            var data=$scope[id].data;
            
            var editDataJson=JSON.stringify(data);
            editDataJson=editDataJson.replace(/'/g,"\\\"");
            editDataJson=editDataJson.replace(/&/g,"___amp___");

            var res=getCsvFile("report"+$routeParams.copyID,editDataJson);
            window.location.href = res.link;
        }
        else {
            alert("This file is currently unavailable");
        }
        
    };
    
    
    
    
    $scope.setStatView = function(id,type) {
        $scope[id].type=type;
    };
    
    throwChart = function(id,data,type,title,width,height,colors,is3D,x_label,y_label,tooltipIsHtml) {
      colors = typeof colors !== 'undefined' ? colors : ['#33ADDF','#E14F4F','#85C7E9','#C01D4A','#C096B8','#002156'];

      is3D = typeof is3D !== 'undefined' ? is3D : true; 
      tooltipIsHtml = typeof tooltipIsHtml !== 'undefined' ? tooltipIsHtml : false;
      
      x_label = typeof x_label !== 'undefined' ? x_label : ""; 
      y_label = typeof y_label !== 'undefined' ? y_label : ""; 
      
      var chart = {};
        chart.type = type;
        chart.displayed = false;
        chart.cssStyle = "height:"+height+"; width:"+width+";background-color:#F0F0F0;background-image:none;";  
        chart.data=data;
        chart.options = {
            "title": title,
            "isStacked": "true",
            "fill": 20,
            "colors":colors,
            is3D: is3D,
            "displayExactValues": true,
            backgroundColor: 'transparent',
            colorAxis: {colors: ['#E14F4F', '#33ADDF']},
            "vAxis": {
                "title": y_label
            },
            "hAxis": {
                "title": x_label
            },
            "tooltip": {
                "isHtml": tooltipIsHtml
              },
            bubble: {textStyle: {fontSize: 14}}

        };
        chart.formatters = {};
        $scope[id] = chart;
    };

    function fixGoogleChartsBarsBootstrap() {
         // Google charts uses <img height="12px">, which is incompatible with Twitter
         // * bootstrap in responsive mode, which inserts a css rule for: img { height: auto; }.
         // *
         // * The fix is to use inline style width attributes, ie <img style="height: 12px;">.
         // * BUT we can't change the way Google Charts renders its bars. Nor can we change
         // * the Twitter bootstrap CSS and remain future proof.
         // *
         // * Instead, this function can be called after a Google charts render to "fix" the
         // * issue by setting the style attributes dynamically.

        $(".google-visualization-table-table img[width]").each(function(index, img) {
            $(img).css("width", $(img).attr("width")).css("height", $(img).attr("height"));
        });
    };
    
});

app.controller('headerController', function ($scope, $timeout, $routeParams, $location, $http, $modal, WheeldoService) {

    $scope.header_init = function() {
     
    };
    
    
    
    $scope.isActive = function(route) {
        //alert(route);
        return route === $location.path();
    };

    
    $scope.setDD = function() {

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
            //triggerObj.append('<a class="trigger" href="javascript:void(0)"></a>');
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
    };
    
    $scope.openChangePassword = function() {
        $(".ddMenuTrigger").find(".ddMenuCont").hide();
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/change_password.html?t='+t,
          keyboard:true,
          windowClass: 'changePasswordModal',
          controller: changePasswordController,
          resolve: {
              f_data:function () {
                return 0;
              }
          }
        });
    };
    
    $scope.openMySettings = function() {
      $(".ddMenuTrigger").find(".ddMenuCont").hide();
      var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/my_settings.html?t='+t,
          keyboard:true,
          windowClass: 'mySettingsModal',
          controller: mySettingsController,
          resolve: {
              f_data:function () {
                return 0;
              }
          }
        });
    };

});

var changePasswordController = function ($scope, $http, $modalInstance, f_data, WheeldoService ) {
  $scope.current_password="";
  $scope.new_password="";
  $scope.retype_new_password="";
  
  $scope.reset = function() {
      if(myForm.new_password.value!=myForm.retype_new_password.value) {
          alert("Your passwords not match!");
          return;
      }
      // check current password:
      var res=checkPassword(myForm.current_password.value);
      if(res.status=="ok") {
          res=resetPassword(myForm.new_password.value);
          
          if(res.status=="ok") {
            alert("Your password has been reset successfully!");
            $modalInstance.dismiss('cancel');
          }
          else {
              alert("Error!");
          }
      }
      else {
         alert("Your current passwords is wrong!"); 
      }
  };

};


var mySettingsController = function ($scope, $http, $modalInstance, f_data, WheeldoService ) {
    $scope.userData={};
    $scope.userData.organization = getOrgInfo();
    $scope.userData.user = getUserInfo();
  
  
    $scope.onFileSelect = function($files) {
            var file=$files[0];

            $(".info_type").html(file.type);
            var size=Math.round(file.size/1024);
            $(".info_size").html(size+"Kb");
            var formObj=$("#file_upload_form");
            var action=formObj.attr("action");
            var target=formObj.attr("target");

            $(".main_image").hide();
            $("#voter_preview_image").attr("src","");
            $(".loadImage").show();
            $("#upload_target").attr("onload","onOrgLogoLoad()");
            formObj.submit();
        return;
      }
      
      
      $scope.save = function() {
            $scope.userData.organization.organizationImg=$("#organizationImg").val();

//            var userData=[];
//            userData['userName']=$scope.userData.user.userName;
//            userData['userEmail']=$scope.userData.user.userEmail;
//            userData['organizationName']=$scope.userData.organization.organizationName;
//            userData['organizationImg']=$scope.userData.organization.organizationImg;

            var editDataJson=JSON.stringify($scope.userData);
            editDataJson=editDataJson.replace(/'/g,"\\\"");
            editDataJson=editDataJson.replace(/&/g,"___amp___");
            var res=saveMysettings(editDataJson);
            if(res.status="ok") {
                alert("The changes has been saved!");
                window.location.reload();
            }
            else {
                alert("Error occurred!");
            }
            //$modalInstance.close(ans);
      };
};

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(obj, start) {
         for (var i = (start || 0), j = this.length; i < j; i++) {
             if (this[i] === obj) { return i; }
         }
         return -1;
    }
}

function stripslashes (str) {
  // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Ates Goral (http://magnetiq.com)
  // +      fixed by: Mick@el
  // +   improved by: marrtins
  // +   bugfixed by: Onno Marsman
  // +   improved by: rezna
  // +   input by: Rick Waldron
  // +   reimplemented by: Brett Zamir (http://brett-zamir.me)
  // +   input by: Brant Messenger (http://www.brantmessenger.com/)
  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
  // *     example 1: stripslashes('Kevin\'s code');
  // *     returns 1: "Kevin's code"
  // *     example 2: stripslashes('Kevin\\\'s code');
  // *     returns 2: "Kevin\'s code"
  return (str + '').replace(/\\(.?)/g, function (s, n1) {
    switch (n1) {
    case '\\':
      return '\\';
    case '0':
      return '\u0000';
    case '':
      return '';
    default:
      return n1;
    }
  });
}


app.controller('accountsController', function ($scope, $timeout, $routeParams, $location, $http, $modal, WheeldoService, $filter) {
    
    
    $scope.pager_min=0;
    $scope.pager_max=66;
    $scope.users_loaded=false;
    
    var outOF=0;

    
    var res=getPricingPackages();
    
    $scope.packages=res.packages;
    
    $scope.init = function(){
        $scope.finish_load='loaded';
        $scope.users_loaded=true;
        $scope.accounts=getAccounts();
        outOF=$scope.accounts.length;
        set_pager($scope.accounts);
        $scope.reverse=true;
        $scope.sortBy('name');
    };
    
    $scope.sortBy = function(key) {
        $scope.predicate = key; $scope.reverse=!$scope.reverse;
        
        
        $(".sort").removeClass("asc");
        $(".sort").removeClass("desc");
        
        if($scope.reverse) {
            $("."+key).addClass("desc");
        }
        else {
            $("."+key).addClass("asc");
        }
        
    };
    
    $scope.init_accounts = function() {
        
    };
    
    getIndexByAccountID = function(account_id) {
        var ret=0;
        for(i in $scope.accounts) {
            if($scope.accounts[i].account.id==account_id) {
                ret = i;
            }
        }
        return ret;
    };
    
    
    $scope.setExpiry = function(account_id) {
        var index=getIndexByAccountID(account_id);
        var account_data=$scope.accounts[index];
        var f_data=account_data;
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/templates/accountEdit.html',
          keyboard:true,
          controller: accountEditCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        }); 
        
        
        modalInstance.result.then(function (addPlayer) {

        }, function () {

        });
    };
    
    $scope.changePackages = function(account_id) {
        var index=getIndexByAccountID(account_id);  
        var pack=$scope.accounts[index].account.pricingPackage;
        if(!confirm("Sure?")) {
            $scope.accounts[index].account.pricingPackage=$scope.accounts[index].account.bu_pricingPackage;
            return;
        }
        
        $scope.accounts[index].account.bu_pricingPackage=$scope.accounts[index].account.pricingPackage;
        
        setPackageAccount(account_id,pack);
        
        
            
    };
    
    $scope.removeOrgs = function() {
      var selctedAr=$filter('filter')($scope.accounts, {checked:true});
      if(selctedAr.length==0)
          return;
      if(!confirm("Are you sure you want to remove "+selctedAr.length+" accounts marked?"))  
          return;
      
      var ids=[];
      for(i in selctedAr) {
          ids.push(selctedAr[i].account.id);
      }
      
      
      for(i in selctedAr) {
            var account_id=selctedAr[i].account.id;
            var res=setAccountInactive(account_id);
            deleteUser(account_id);
        }

        //setAccountInactive();
    };
    
    makeDataReadyToSend = function(editData) {
      var editDataJson=JSON.stringify(editData);
      editDataJson=editDataJson.replace(/'/g,"\\\"");
      editDataJson=editDataJson.replace(/&/g,"___amp___");
      return editDataJson;
    };

    
    deleteUser = function(userID){
        for(i in $scope.accounts) {
            if($scope.accounts[i].account.id==userID) {
                if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.accounts.splice(i, 1);
                }
                else {
                    $scope.$apply(function () {
                        $scope.accounts.splice(i, 1);
                    });
                }
                
                
            }
        }
    };
    
    
    ///////////////////////////////////////////////
    
    var users_per_page=30;
    var current_page=1;
    set_pager = function(data) {
        var total_users=data.length;
        var max_user_place=current_page*users_per_page;
        var limit_min=-users_per_page;
        var limit_max=max_user_place;
        
        if(max_user_place>total_users) {
            limit_max=total_users;
            var gap=max_user_place-total_users;
            limit_min=-(users_per_page-gap);
        }
        $scope.limitMax=limit_max;
        $scope.users_per_page=limit_min;
        
        setPagerAmount(total_users,max_user_place,current_page);
        
        $scope.total=total_users;
    };
    
    var checkAll=false;
    $scope.checkAll = function() {
        checkAll=!checkAll;
        var filteredArray = $filter('orderBy')($scope.accounts, $scope.predicate);
        filteredArray =  $filter('filter')(filteredArray, $scope.search);
        filteredArray =  $filter('filter')(filteredArray, $scope.filterMultiple);
        filteredArray =  $filter('limitTo')(filteredArray, $scope.limitMax);
        filteredArray =  $filter('limitTo')(filteredArray, $scope.users_per_page);

        checkAll=$(".check_all").is(":checked");
        
        for(i in filteredArray) {
            var id=filteredArray[i].id;
            filteredArray[i].checked=checkAll;
        }

        updateChecked();

    };
    
    updateChecked = function() {
        var testAr=$filter('filter')($scope.accounts, {checked:true});
        $scope.current_check=testAr.length;
        return testAr.length;
    };
    
    setPagerAmount = function(total_users,max_user_place,current_page) {
        var max=current_page*users_per_page;
        if(max>total_users)
            max=total_users;
        var min=(current_page-1)*users_per_page+1;
        $scope.amount=min+"-"+max;
    };
    
    $scope.sortBy = function(key) {
        $scope.predicate = key; $scope.reverse=!$scope.reverse;
        
        
        $(".sort").removeClass("asc");
        $(".sort").removeClass("desc");
        
        if($scope.reverse) {
            $("."+key).addClass("desc");
        }
        else {
            $("."+key).addClass("asc");
        }
        
    };
    
    /////////////////////
    
    $scope.goNextPage = function() {
        var total_users=$scope.accounts.length;
        var max_allowed=Math.ceil(total_users/users_per_page);
        
        if(current_page>=max_allowed)
            return;
        current_page++;
        set_pager($scope.accounts);
    };   
    
    
    $scope.goPerviousPage = function() {
        if(current_page<2)
            return;
        current_page--;
        set_pager($scope.accounts);
    };
    ////////
    
    
    
    $scope.$watch('org_filter', function() {
        
    });
    
    
    
});


var accountEditCtrl = function ($scope, $http, $modalInstance, f_data ,WheeldoService) {
    console.log(f_data);
    
    $scope.account=f_data.account;
    $scope.org=f_data.org;
    
    $scope.initDatePicker = function() {
    };
    
    $scope.showWeeks = false;
    $scope.openCal = function() {

          $scope.cal_opened = true;
        
      };
      
    $scope.dateOptions = {
        'year-format': "'yy'",
        'starting-day': 1
    };
    
    $scope.dateChanged = function() {
        var or=$scope.account.validUntil;
        var jsTime=or.getTime();
        var new_date=jsTime/1000;
        
        var m_fixed=or.getMonth()+1;
        var m=m_fixed>9?m_fixed:"0"+m_fixed;
        var d=or.getDate()>9?or.getDate():"0"+or.getDate();
        var y=or.getFullYear();
        
        $scope.account.validUntil=m+"/"+d+"/"+y;
        
        
        setExpiryDate($scope.account.id,new_date);

    };
    
    
    $scope.close = function () {
          $modalInstance.dismiss('cancel');
     };
    
};



var gameTeamCtrl = function ($scope, $http, $modalInstance, f_data ,WheeldoService) {
    $scope.data_ready=false;
    $scope.copyRow=f_data.copyRow;
    var outOF=0;
    
    
    $.ajax({
        type: "post",
        url: DATA_PATH+'gt',
        data:{
            op:"getAppTeam",
            copyID:f_data.copyID
        },
        success: function(data, textStatus, jqXHR) {
            if($scope.$$phase || $scope.$root.$$phase) {
                $scope.data_ready=true;
                $scope.data=data;
            }
            else {
                $scope.$apply(function () {
                    $scope.data_ready=true;
                    $scope.data=data;
                });
            }
            
            outOF=data.users.length;

        }
    });
    
    
    $scope.getUserLink = function(index) {
        var copyID=f_data.copyID;
        var userID=$scope.data.users[index].userID;
        $scope.data.users[index].wait=true;
        $.ajax({
            type: "post",
            url: DATA_PATH+'gt',
            data:{
                op:"getUserLink",
                copyID:copyID,
                userID:userID
            },
            success: function(data, textStatus, jqXHR) {
                if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.data.users[index].link=data.link;
                    $scope.data.users[index].wait=false;
                }
                else {
                    $scope.$apply(function () {
                        $scope.data.users[index].link=data.link;
                        $scope.data.users[index].wait=false;
                    });
                }
            }
        });
    };
    
    var indexesSent=[];
    $scope.sendInvitation = function(index) {
        var copyID=f_data.copyID;
        var userID=$scope.data.users[index].userID;
        $scope.data.users[index].wait=true;
        $.ajax({
            type: "post",
            url: DATA_PATH+'gt',
            data:{
                op:"sendInvitation",
                copyID:copyID,
                userID:userID
            },
            success: function(data, textStatus, jqXHR) {
                indexesSent.push(index);
                if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.data.users[index].wait=false;
                    $scope.data.users[index].email_res=data.status;
                }
                else {
                    $scope.$apply(function () {
                        $scope.data.users[index].wait=false;
                        $scope.data.users[index].email_res=data.status;
                    });
                }
            }
        });
    };
    
    $scope.editEmail = function() {
        $(".email_content").slideToggle("fast");
    };

    $scope.sendAll = function() {
        indexToSend=0;
        sendNextInvitation();
        
    };
    
    function forceApply(callback) {
        if($scope.$$phase || $scope.$root.$$phase) {
            callback();
        }
        else {
            $scope.$apply(function () {
                callback();
            });
        }
    };
    
    forceApply(function(){});
    
    var indexToSend=0;
    sendNextInvitation = function() {
        if(indexesSent.length>=$scope.data.users.length) {
            
            forceApply(function(){$scope.sending="All invitations were sent.";});
            return;
        }
        
        if(indexesSent.indexOf(indexToSend)<0) {
            /// start send //
            setSendingText(indexToSend);
            var copyID=f_data.copyID;
            var userID=$scope.data.users[indexToSend].userID;
            if($scope.$$phase || $scope.$root.$$phase) {$scope.data.users[indexToSend].wait=true;}else {$scope.$apply(function () {$scope.data.users[indexToSend].wait=true;});}
            $.ajax({
                type: "post",
                url: DATA_PATH+'gt',
                data:{
                    op:"sendInvitation",
                    copyID:copyID,
                    userID:userID
                },
                success: function(data, textStatus, jqXHR) {
                    if($scope.$$phase || $scope.$root.$$phase) {
                        $scope.data.users[indexToSend].wait=false;
                        $scope.data.users[indexToSend].email_res=data.status;
                    }
                    else {
                        $scope.$apply(function () {
                            $scope.data.users[indexToSend].wait=false;
                            $scope.data.users[indexToSend].email_res=data.status;
                        });
                    }
                    indexesSent.push(indexToSend);
                    indexToSend++
                    sendNextInvitation();
                }
            });
            /////////////////
            
            
            
            
        }
        else {
            indexToSend++;
            sendNextInvitation();
        }

    };
    
    setSendingText = function(index) {
        $scope.sending="Sending "+(index+1)+" out of "+outOF+", please wait...";
    };
    
    $scope.saveEmail = function() {
        $scope.not="Saving...";
        var copyID=f_data.copyID;
        $.ajax({
            type: "post",
            url: DATA_PATH+'gt',
            data:{
                op:"updateEmailContent",
                copyID:copyID,
                app_email_title:$scope.copyRow.app_email_title,
                app_email_content:$scope.copyRow.app_email_content
            },
            success: function(data, textStatus, jqXHR) {
                if($scope.$$phase || $scope.$root.$$phase) {
                    $scope.not="Saved ("+getCurrTime()+")";
                }
                else {
                    $scope.$apply(function () {
                        $scope.not="Saved ("+getCurrTime()+")";
                    });
                }
                
            }
        });
    };
    
    $scope.closeEmail = function() {
        $(".email_content").slideUp("fast");
    };
    
    
    getCurrTime = function() {
        var d = new Date();
        var h=d.getHours()>9?d.getHours():"0"+d.getHours();
        var m=d.getMinutes()>9?d.getMinutes():"0"+d.getMinutes();
        var s=d.getSeconds()>9?d.getSeconds():"0"+d.getSeconds();
        return h+":"+m+":"+s;
    };
};

var insightCtrl = function ($scope, $http, $modalInstance, f_data ,WheeldoService) {

    $scope.insight=f_data.insight;
    
    $scope.title="Add new insight";
    $scope.save_text="Add this insight";
    if(f_data.insight_index>=0) {
        $scope.title="Edit insight";
        $scope.save_text="Save & Close";
    };
    
    
    
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
    
    $scope.saveAddInsight = function() {
        var new_insight=$scope.insight;
        
        if(new_insight.data.headline=="") {
            alert("Please insert headline!");
            return;
        };
        
        $modalInstance.close(new_insight);
    };
    
};



app.controller('adminsController', function ($scope, $timeout, $routeParams, $location, $http, $modal, WheeldoService, $filter) {
    var total_users;
    var users;
    
    $scope.pager_min=0;
    $scope.pager_max=66;
    $scope.users_loaded=false;

    $scope.init = function() {
        $scope.finish_load='loaded';
        $scope.users_loaded=true;
        var res=getAdmins();
        $scope.admins=res.admins;
        set_pager($scope.admins);
        $scope.reverse=true;
        $scope.sortBy('name');
    };
    
    $scope.addAdmin = function() {
        var admin_alt={
            name:"",
            email:""
        }
        var f_data={};
        f_data.admin=admin_alt;
        var t=new Date().getTime();
        var modalInstance = $modal.open({
          templateUrl: DATA_PATH+'app/popups/addAdmin.html?t='+t,
          keyboard:true,
          windowClass: 'addAdmin',
          controller: addAdminCtrl,
          resolve: {
              f_data:function () {
                return f_data;
              }
          }
        });
          
        modalInstance.result.then(function (new_admin) {
            var res=saveAdmin(0,new_admin.name,new_admin.email);
            var new_admin={
                id:res.id,
                name:new_admin.name,
                bu_name:new_admin.name,
                email:new_admin.email,
                bu_email:new_admin.email,
                new_password:""
            }

            if($scope.$$phase || $scope.$root.$$phase) {
                $scope.admins.push(new_admin);
            }
            else {
                $scope.$apply(function () {
                    $scope.admins.push(new_admin);
                });
            }
        }, function () {
            
        }); 
    };
    
    getIndexByID = function(id) {
      var index=-1;
      for(i in $scope.admins) {
          var admin=$scope.admins[i];
          if(admin.id==id) {
             index=i;
             break;
          }
      }
      return index;
    };
    
    $scope.removeUsers = function() {
      var selctedAr=$filter('filter')($scope.admins, {checked:true});
      if(selctedAr.length==0)
          return;
      if(!confirm("Are you sure you want to remove "+selctedAr.length+" admins marked?"))  
          return;
      
      var ids=[];
      for(i in selctedAr) {
          ids.push(selctedAr[i].id);
      }
      

      var res=setTeamOp("remove",makeDataReadyToSend(ids));
      if(res.status=="ok") {
        for(i in selctedAr) {
            deleteUser(selctedAr[i].id);
        }
      }
      else {
          alert("Error!");
      }
    };
    
    deleteUser = function(userID){
        for(i in $scope.admins) {
            if($scope.admins[i].id==userID) {
                $scope.admins.splice(i, 1);
            }
        }
    };
        
    makeDataReadyToSend = function(editData) {
      var editDataJson=JSON.stringify(editData);
      editDataJson=editDataJson.replace(/'/g,"\\\"");
      editDataJson=editDataJson.replace(/&/g,"___amp___");
      return editDataJson;
    };
    
    $scope.deleteAdmin = function(admin_id) {
        if(!confirm("Are you sure you want to delete the admin account?"))
            return;
        var index=getIndexByID(admin_id);
        deleteAdmin(admin_id);
        $scope.admins.splice(index,1);
    };
    
    $scope.editAdmin = function(admin_id) {
        for(i in $scope.admins) {
            $scope.admins[i].editable=false;
            $scope.admins[i].name=$scope.admins[i].bu_name;
            $scope.admins[i].email=$scope.admins[i].bu_email;
        }
        var index=getIndexByID(admin_id);
        $scope.admins[index].editable=true;
    };
    
    $scope.saveAdmin = function(admin_id) {
        
        var index=getIndexByID(admin_id);
        
        if(!$scope.admins[index].name) {
            alert("Please insert admin name!");
            return;
        }
        
        
        if(!$scope.admins[index].email) {
            alert("Please insert valid email address!");
            return;
        }
        
        
        var res=saveAdmin($scope.admins[index].id,$scope.admins[index].name,$scope.admins[index].email);
        $scope.admins[index].bu_name=$scope.admins[index].name;
        $scope.admins[index].bu_email=$scope.admins[index].email;
        
        for(i in $scope.admins) {
            $scope.admins[i].editable=false;
        }
    };
    
    $scope.resetUserPassword = function(admin_id) {
        if(!confirm("Reset admin password?"))
            return;
        var index=getIndexByID(admin_id);
        var res=resetAdminPassword(admin_id,$scope.admins[i].new_password);
        if(res.status=="ok")
            alert("Admin password has been changed. New password: '"+$scope.admins[i].new_password+"'.");
        $scope.admins[i].new_password="";
    };
    
    var users_per_page=30;
    var current_page=1;
    set_pager = function(data) {
        var total_users=data.length;
        var max_user_place=current_page*users_per_page;
        var limit_min=-users_per_page;
        var limit_max=max_user_place;
        
        if(max_user_place>total_users) {
            limit_max=total_users;
            var gap=max_user_place-total_users;
            limit_min=-(users_per_page-gap);
        }
        $scope.limitMax=limit_max;
        $scope.users_per_page=limit_min;
        
        setPagerAmount(total_users,max_user_place,current_page);
        
        $scope.total=total_users;
    };
    
    var checkAll=false;
    $scope.checkAll = function() {
        checkAll=!checkAll;
        var filteredArray = $filter('orderBy')($scope.admins, $scope.predicate);
        filteredArray =  $filter('filter')(filteredArray, $scope.search);
        filteredArray =  $filter('filter')(filteredArray, $scope.filterMultiple);
        filteredArray =  $filter('limitTo')(filteredArray, $scope.limitMax);
        filteredArray =  $filter('limitTo')(filteredArray, $scope.users_per_page);

        checkAll=$(".check_all").is(":checked");
        
        for(i in filteredArray) {
            var id=filteredArray[i].id;
            filteredArray[i].checked=checkAll;
        }
        
        updateChecked();
    };
    
    updateChecked = function() {
        var testAr=$filter('filter')($scope.admins, {checked:true});
        $scope.current_check=testAr.length;
        return testAr.length;
    };
    
    setPagerAmount = function(total_users,max_user_place,current_page) {
        var max=current_page*users_per_page;
        if(max>total_users)
            max=total_users;
        var min=(current_page-1)*users_per_page+1;
        $scope.amount=min+"-"+max;
    };
    
    $scope.sortBy = function(key) {
        $scope.predicate = key; $scope.reverse=!$scope.reverse;
        
        
        $(".sort").removeClass("asc");
        $(".sort").removeClass("desc");
        
        if($scope.reverse) {
            $("."+key).addClass("desc");
        }
        else {
            $("."+key).addClass("asc");
        }
        
    };
    
    
    $scope.$watch('org_filter', function() {
        
    });

});




var addAdminCtrl = function ($scope, $http, $modalInstance, f_data ,WheeldoService) {
    $scope.save_text = "Add admin and send invitation";
    $scope.admin=f_data.admin;
    
    $scope.saveAdmin = function() {

        if(!$scope.admin.name) {
            alert("Please insert admin name!");
            return;
        }
        
        
        if(!$scope.admin.email) {
            alert("Please insert valid email address!");
            return;
        }

        $modalInstance.close($scope.admin);
    };
    
    
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
   };
};


var_dump = function(some) {
    console.log(some);
};