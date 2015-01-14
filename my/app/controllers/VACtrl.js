var VACtrl = function ($scope, $http, $modalInstance, f_data ) {
    
    
    
    
    $scope.answer=f_data.ans;
    
    
    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
     };
     
     
     $scope.save = function() { 
            var ans={};
            ans.data=$scope.answer;
            if(!$("#answer").hasClass("ng-valid")) {
                alert("Please fill out your answer!");
                return;
          }
          $scope.answer.img_bitmap=$("#img_bitmap").val();
          $scope.answer.img_link=$("#img_link").val();

          
          $modalInstance.close(ans);

     };
     
     
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
            $("#upload_target").attr("onload","onFrmaeLoad()");
            formObj.submit();
        return;
      }
    
};