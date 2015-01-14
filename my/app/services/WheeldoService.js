//This handles retrieving data and is used by controllers. 3 options (server, factory, provider) with 
//each doing the same thing just structuring the functions/data differently.
app.service('WheeldoService', ['$http',function () {
    
    getPublishedApps = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getPublishedApps");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    }; 
    
    getMarketApps = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getMarketApps");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    }; 
    
    getGameCategories = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getGameCategories");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    }; 
    
    
    getApp = function(appID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getApp&appID="+appID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getAppInfo = function(appID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getAppInfo&appID="+appID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getCopyInfo = function(copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getCopyInfo&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    getNewCopyID = function(appID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getNewCopyID&appID="+appID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getEditData = function(appID,copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getEditData&appID="+appID+"&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    loadPrevEditData = function(appID,copyID,loadGameID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=loadPrevEditData&appID="+appID+"&copyID="+copyID+"&loadGameID="+loadGameID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    
    setEditData = function(appID,copyID,data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=setEditData&appID="+appID+"&copyID="+copyID+"&data="+data);
            if (request.status === 200) {
                try{
                       res=jQuery.parseJSON(request.responseText);
                }
                catch(err){
                       res={status:'error',error:'decoding'};
                }
            }
        return res;
    }; 
    
    
    getAppReport = function(appID,copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getAppReport&appID="+appID+"&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getTeamsList = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getTeamsList");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getTeamsListNoC = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getTeamsListNoC");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getPreviousGames = function(appID,copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getPreviousGames&appID="+appID+"&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    addPlayerToCopyID = function(appID,copyID,name,email,empID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=addPlayerToCopyID&appID="+appID+"&copyID="+copyID+"&name="+name+"&email="+email+"&empID="+empID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    checkPassword = function(password) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=checkPassword&password="+password);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    resetPassword = function(password) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=resetPassword&password="+password);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getOrgInfo = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getOrgInfo");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getUserInfo = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getUserInfo");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    saveMysettings = function(userData) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=saveMysettings&userData="+userData);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getHash = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getHash");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getCsvFile = function(name,data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getCsvFile&name="+name+"&data="+data);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    setTeamOp = function(op,data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'app/ajax/teamOp.aspx', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op="+op+"&data="+data);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    updateTeamCacheFile = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'app/ajax/teams.aspx', true);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send();
            if (request.status === 200) {
                saveing_status=99;
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    getGuessWotFilteredData = function(appID,copyID,data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getGuessWotFilteredData&appID="+appID+"&copyID="+copyID+"&data="+data);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getAccounts = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getAccounts");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    updateAccountData = function(account_id,key,value) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=updateAccountData&account_id="+account_id+"&key="+key+"&value="+value);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };

    setExpiryDate = function(account_id,validUntil) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=setExpiryDate&account_id="+account_id+"&validUntil="+validUntil);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    loadSlidesPics = function(copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=loadSlidesPics&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    getAdmins = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getAdmins");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    saveAdmin = function(id,name,email) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=saveAdmin&id="+id+"&name="+name+"&email="+email);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    
    deleteAdmin = function(admin_id) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=deleteAdmin&admin_id="+admin_id);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    resetAdminPassword = function(id,new_password) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=resetAdminPassword&id="+id+"&new_password="+new_password);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getUserFullDetails = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getUserFullDetails");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getGuessWotFullReport = function(appID,copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getGuessWotFullReport&appID="+appID+"&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getPricingPackages = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getPricingPackages");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    setAccountInactive = function(account_id) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=setAccountInactive&account_id="+account_id);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    setPackageAccount = function(account_id,pack) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=setPackageAccount&account_id="+account_id+"&pack="+pack);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    insertLog = function(type,more) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'ajax/insert_log.aspx', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("type="+type+"&more="+more);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getTokensLeft = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getTokensLeft");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    setBid = function(am) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=setBid&am="+am);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    getCountries = function() {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=getCountries");
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    
    createBSShopper = function(data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=createBSShopper&data="+data);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    sendGameLink2User = function(copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=sendGameLink2User&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    get_templates = function(appID,copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=get_templates&q=get&appID="+appID+"&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    save_templates = function(appID,copyID,data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', true);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=get_templates&q=set&appID="+appID+"&copyID="+copyID+"&data="+data);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    create_new_template = function(appID,copyID,data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=get_templates&q=new&appID="+appID+"&copyID="+copyID+"&data="+data);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    checkIfImageUploaded = function(appID,form_name,copyID) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=checkIfImageUploaded&appID="+appID+"&form_name="+form_name+"&copyID="+copyID);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };
    
    
    regFormData = function(appID,copyID,type,data) {
        var res;
        var request = new XMLHttpRequest();
            request.open('POST',  DATA_PATH+'gt', false);  // `false` makes the request synchronous
            request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            request.send("op=regFormData&appID="+appID+"&copyID="+copyID+"&type="+type+"&data="+data);
            if (request.status === 200) {
                res=jQuery.parseJSON(request.responseText);
            }
        return res;
    };


}]);