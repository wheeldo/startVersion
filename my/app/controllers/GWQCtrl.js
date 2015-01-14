var GWQCtrl = function ($scope, $modalInstance, f_data) {
      
      

      
    $scope.q_types=f_data.q_types;
    $scope.q=f_data.q;
    
    
    
//    console.log($scope.q_types);
//    console.log($scope.q.type);

    $scope.new_value="";
    



   // alert($scope.q.options.length);
//    alert(typeof($scope.q.type));
//    alert(typeof($scope.q_types[1].id));

    $scope.save_text="Add this question";
    if(f_data.q_index>=0) {
        $scope.save_text="Save & Close";
    }


    $scope.initCtrl = function() {

    };

    $scope.TypesLoaded = function() {

        var q_type=$scope.q.type;
        $(".type_wrapper").hide();
        $(".type_wrapper[data_type="+q_type+"]").show();
    };


    var q_type=1;
    $scope.loadQForm = function() {
        q_type=$("#q_type").val();
        $(".type_wrapper").hide();
        $(".type_wrapper[data_type="+q_type+"]").show();
    };

    setSelectPickecr = function() {
       $('.selectpicker').selectpicker({
             'selectedText': 'cat' 
        }); 
    };

    $scope.setSelect = function() {

    };

    $scope.delValue = function(index) {
        alert(index);
    };

    $scope.addValue = function() {
        var new_value=$("#new_value").val();
        var value={
              text:new_value
        };
        
        
        if($scope.q.values.length>=12) {
            return;
        }
        $scope.q.values.push(value);
        $scope.new_value="";
        $("#new_value").val("");
    };
    
    $scope.addOption = function() {
        var new_option=$("#new_option").val();
        var option={
              text:new_option
        };
        
        if($scope.q.options.length>=8) {
            return;
        }
        
        $scope.q.options.push(option);
        $scope.new_option="";
        $("#new_option").val("");
    };
    
    $scope.addConditionTrueOption = function() {
        var new_option=$("#new_condition_options_true").val();
        var option={
              text:new_option
        };
        
        if($scope.q.conditionMultiSelect.options_true.length>=8) {
            return;
        }
        
        $scope.q.conditionMultiSelect.options_true.push(option);
        $scope.new_condition_options_true="";
        $("#new_condition_options_true").val("");
    };
    
    $scope.addConditionFalseOption = function() {
        var new_option=$("#new_condition_options_false").val();
        var option={
              text:new_option
        };
        
        if($scope.q.conditionMultiSelect.options_false.length>=8) {
            return;
        }
        
        $scope.q.conditionMultiSelect.options_false.push(option);
        $scope.new_condition_options_false="";
        $("#new_condition_options_false").val("");
    };

    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
     };

    $scope.saveAddQuestionGW = function() {
          var new_question={};
          new_question=$scope.q;


          if(new_question.type==0) {
               alert("Please select your question type!");
               return;
          }

          if(!$("#text").hasClass("ng-valid")) {
                alert("Please fill out the instruction box!");
                return;
          }

          switch(new_question.type) {
              case 2:
                  if(new_question.values.length<3) {
                      alert("Please enter at least 3 values!");
                      return;
                  }
              break;
              
              case 3:
                  if(new_question.options.length<3) {
                      alert("Please enter at least 3 options!");
                      return;
                  }
              break;
              
              case 4:

                  var value=parseInt(new_question.condition.value);
                  if(!value || value>99 || value<1) {
                      alert("Please insert condition value between 1-99");
                      return;
                  }
                  
                  if(!$("#instruction_true").hasClass("ng-valid")) {
                      alert("Please fill out the `If true instruction` box!");
                      return;
                  }
                  
                  if(!$("#instruction_false").hasClass("ng-valid")) {
                      alert("Please fill out the `If false instruction` box!");
                      return;
                  }
                  
                  
              break;
              
              
              
          }
          
          //validation done
          $modalInstance.close(new_question);

     };
      

};