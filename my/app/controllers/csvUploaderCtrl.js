var csvUploaderCtrl = function ($scope, $http, $modalInstance, f_data ,WheeldoService) {
    $scope.data_expected=f_data.data_expected;
    $scope.res_mark=getHash();
    $scope.uploadReady=false;
    
    
    $scope.exp_file=f_data.exp_file;
    
    $scope.onFileSelect = function($files) {
        $(".loader").show();
        var file=$files[0];
        var ex=file.name.split(".");
        var ext=ex[ex.length-1];
        if(ext!="csv"&&ext!="xls"&&ext!="xlsx") {
            $(".loader").hide();
            alert("Please upload CSV or Excel file!");
            return;
        }
        $(".info_type").html(file.type);
        var size=Math.round(file.size/1024);
        $(".info_size").html(size+"Kb");
        var formObj=$("#file_upload_form");
        var action=formObj.attr("action");
        var target=formObj.attr("target");

        $(".main_image").hide();
        $("#voter_preview_image").attr("src","");
        $(".loadImage").show();
        $("#upload_target").attr("onload","onCsvFileLoad()");
        formObj.submit();
        ifUploadDone();
    return;
  }
  


    ifUploadDone = function() {
        var jqxhr = $.get( "/uploads/csv/"+$scope.res_mark.hash+".txt", function() {
            var as_json=$.parseJSON(jqxhr.responseText);
            loadCsvData(as_json);
        })
        .fail(function() {
            setTimeout(ifUploadDone,500);
        });
    };
  
    loadCsvData = function(csvData) {
        $(".loader").hide();
        $scope.$apply(function () {
          $scope.csvData=csvData;
          $scope.uploadReady=true;
        });
    };
    
    $scope.removeRow = function(index) {
        for(i in $scope.csvData) {
            $scope.csvData[i].splice(index, 1);
        }
        
    };
  

    $scope.move_left = function(index) {
        if(index>0) {
            var currCol=$scope.csvData[index];
            var toCol=$scope.csvData[index-1];
            $scope.csvData[index-1]=currCol;
            $scope.csvData[index]=toCol;
        }
    };
    
    $scope.move_right = function(index) {
        if(index<$scope.data_expected.length-1) {
            var currCol=$scope.csvData[index];
            var toCol=$scope.csvData[index+1];
            $scope.csvData[index+1]=currCol;
            $scope.csvData[index]=toCol;
        }
    };
    
   
    $scope.cancel = function () {
         $modalInstance.dismiss('cancel');
    };
    $scope.save = function() { 
        $modalInstance.close($scope.csvData);
    };
     
     
     
};