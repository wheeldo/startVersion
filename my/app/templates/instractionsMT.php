    <?php
    $lang=$_GET['lang'];
    
    switch($lang):
        case "he":
            
        
    ?>
    <p class="ng-binding">
            הטיפוס מתחיל כאן.
    </p>
    <p class="ng-binding">
            בכל נקודה בטיפוס מחכה לך שאלת אתגר, תשובות נכונות יקדמו אותך בטיפוס בעוד טעות תגרום לך להחליק וליפול.
    </p>
    <p class="ng-binding">
            שימ/י לב!          </p>
    <p class="ng-binding">
            ככל שתענה/י מהר יותר על שאלות האתגר כך תטפס/י גבוה יותר.
    </p>
    <p ng-show="false" class="ng-binding" style="display: none;">
            לאחר כל שלוש שאלות תינתן לך האפשרות לכתוב שאלת אתגר למטפסים/ות האחרים/ות. תשובה שגויה שלהם/ן על שאלתך, תקדם אותך ב -200 רגל נוספים.
    </p>
    <p class="ng-binding">
            מי י/תכבוש את הפסגה?            </p>
    <br>
    <p class="ng-binding">
            בהצלחה!           </p>
    
    
    <?php
        break;
    
        case "en":
   ?>   

            <p class="ng-binding">
                    Your climb starts here.
            </p>
            <p class="ng-binding">
                    There are set challenges on each waypoint for you to answer. Correct answers will help you climb higher while wrong ones will make you fall.
            </p>
            <p class="ng-binding">
                    So BE CAREFUL!          </p>
            <p class="ng-binding">
                    The faster you complete each challenge, the higher your climb will be for that challenge.
            </p>
            <p ng-show="false" class="ng-binding" style="display: none;">
                    After every three challenges you will have the option of writing your own question that will challenge the other players. Whenever they answer your question incorrectly you will climb 200 feet higher. 
            </p>
            <p class="ng-binding">
                    Who will climb the higher climb?            </p>
            <br>
            <p class="ng-binding">
                    Good luck!           </p>

   <?php         
        break;
    endswitch;
    
    ?>
    