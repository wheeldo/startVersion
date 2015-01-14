var triviaQCtrl = function ($scope, $modalInstance, f_data) {
     
   $scope.q=f_data.q;

   
   $scope.q_index=f_data.q_index;
   $scope.bonus=f_data.q.is_bonus=="Y"?1:0;
   
   $scope.save_text="Add this question";
   if(f_data.q_index>=0) {
       $scope.save_text="Save & Close";
   }
   
   $scope.show_bonus = "hide";
   if(f_data.show_bonus) {
       $scope.show_bonus = "";
   }
   
   
   
   var slide_q=false;
   $scope.hide_more=false;
   if(f_data.slide_q || f_data.hide_more) {
       $scope.hide_more=true;
       slide_q=true;
   }
   
   
//   var input = $('input');
//   input.trigger('input');

   $scope.initCtrl = function() {
        setCheckbox();
        setOnChangeAnswer();
        setRemoveAnswer();
        setRightAnswer();
        setMoveUpAndDown();
        var answer=f_data.q.answer; 
        $(".wrongWright").removeClass("right");
        $("#answer").val(answer);
        $(".answer[answer="+answer+"]").find(".wrongWright").addClass("right");
   };


    $scope.$watch('q', function(newVal) {
        loadAfterLoad();
    });

   loadAfterLoad = function() {
      
   };
   
   
   $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
   };
   
   $scope.contentLoad = function() {
        //$("input").attr("readonly",true)
        setTimeout("setDataExist()",1000);
   }
   
   setDataExist = function () {
      //$("input").attr("readonly",false);
   };
   
   $scope.saveAddQuestionTrivia = function() {
        var new_question={};
        new_question.q_data={};
        if($scope.q.q_id || $scope.q.q_id=="0") new_question.q_data.q_id=$scope.q.q_id;
        new_question.q_data.question=$("#question").val();
        new_question.q_data.extra=$("#extra").val();
        new_question.q_data.more_link=$("#more_link").val();
        new_question.q_data.is_bonus=$("#bonus").val();
        new_question.q_data.answer=$("#answer").val();
        new_question.q_data.A=$("#A").val();
        new_question.q_data.B=$("#B").val();
        new_question.q_data.C=$("#C").val();
        new_question.q_data.D=$("#D").val();
        new_question.q_data.userID=$scope.q.userID;
        
        
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
        
        $modalInstance.close(new_question);
        
   };
    
   setCheckbox = function() {
      $(".checkbox").unbind("click");
      $(".checkbox").click(function(){
          var val=$(this).find(".checkbox_input").val();
          if(val=="1") {
              $(this).find(".checkbox_input").val(0);
              $(this).removeClass("checked");
          }
          else {
              $(this).find(".checkbox_input").val(1);
              $(this).addClass("checked");
          }
      });
    };
    
    setOnChangeAnswer = function() {
      $(".answer").unbind("keyup");
      $(".answer").find("input").keyup(function(){
         var len=$(this).val().length;
         if(len>0) {
             $(this).parent().addClass("valid");
         }
         else {
             $(this).parent().removeClass("valid");
         }
      });
    };
    
    setRemoveAnswer = function() {
      $(".answer .remove a").unbind("click");
      $(".answer .remove a").click(function(){
         $(this).parent().parent().find("input").val("");
         $(this).parent().parent().find("input").trigger("keyup");
         $(this).parent().parent().removeClass("valid");
      });
      
    };
    
    setRightAnswer = function() {
      $(".wrongWright").click(function(){
         var answer=$(this).attr("answer"); 
         $(".wrongWright").removeClass("right");
         $("#answer").val(answer);
         $(this).addClass("right");
      });
    };
    
    setMoveUpAndDown = function() {
      $(".moveUp").unbind("click");
      $(".moveUp").click(function(){
          var answer=$(this).parent().parent().attr("answer");

          var asNum=getNumLet(answer);

          if(asNum>1) {
              switchAnswers(answer,getLetNum(asNum-1));
          }
      });
      
      $(".moveDown").unbind("click");
      $(".moveDown").click(function(){
          var answer=$(this).parent().parent().attr("answer");

          var asNum=getNumLet(answer);

          if(asNum<4) {
              switchAnswers(answer,getLetNum(asNum+1));
          }
      });
      
      
    };
    
    switchAnswers = function(fromLet,toLet) {
        //console.log("move "+fromLet+" to "+toLet);
        
        
        // move the currect answer:
        var answer=$("#answer").val();
        if(answer==fromLet) {
            //console.log("move right answer");
            $("#answer").val(toLet);
            answer=toLet;
        }
        else if(answer==toLet) {
            //console.log("move to right answer");
            $("#answer").val(fromLet);
            answer=fromLet;
        }
        
        
        $(".wrongWright").removeClass("right");
        $(".wrongWright[answer="+answer+"]").addClass("right");
        
        
        var fromVal=$("#"+fromLet).val();
        var toVal=$("#"+toLet).val();
        
        $("#"+fromLet).val(toVal);
        $("#"+toLet).val(fromVal);
    };
    
    
    getNumLet = function(let) {
        switch(let) {
            case "A": return 1; break;
            case "B": return 2; break;
            case "C": return 3; break;
            case "D": return 4; break;
        } 
    };
    
    getLetNum = function(num) {
        switch(num) {
            case 1: return "A"; break;
            case 2: return "B"; break;
            case 3: return "C"; break;
            case 4: return "D"; break;
        } 
    };
};