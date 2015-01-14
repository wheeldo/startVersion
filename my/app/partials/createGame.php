<div class="loader_afterload {{finish_load}}" ng-init="setEditScreenFunctions()">
    <div class="breadcrumbs">
        <div><a href="/#/">GameBoard</a></div>
        <div class="arrow"></div>
        <div>Create New `{{appData.name}}` Game</div>
        <div class="page_action"> <!--floating-->
            <div class="actions_wrapper">
                <button type="button" class="system_button blue_button save_edit" style="float:left;" ng-click="previewGame()" ng-show="appData.preview=='1'">Preview</button>
                <button type="button" class="system_button green_button" style="float:right;" id="saveAndPublishButton" ng-show="copyData.appCopyTerminate" ng-click="saveAndPublish()">Publish via Email</button>
                <button type="button" class="system_button green_button" style="float:right;" id="saveAndPublishButton" ng-show="copyData.appCopyTerminate" ng-click="sendGameLink()">Send me a Link</button>
                
            </div>
        </div>
        <br class="clr" />
    </div>
    <div class="createGameTopBar">
        <h4 style='margin-bottom:5px;'>Enter the name of your game</h4>
        <input class="inputMark" type="text" placeholder="Enter the name of your game" name="game_name" ng-model="copyData.appCopyName" id="game_name" ng-minlength=3 ng-maxlength=30 maxlength="30" required ng-focus ng-init="setSaveCopyName()" />
<!--        <button type="button" class="system_button green_button" style="float:right;" id="saveAndPublishButton" ng-show="copyData.appCopyTerminate" ng-click="saveAndPublish()">Publish Game via Email</button>-->
<!--        <button type="button" class="system_button blue_button save_edit" style="float:right;" ng-click="saveData()">Save</button>-->
        
        <br class="clr" />
    </div>
    <div class="save_bar"><span class="save_text"></span></div>
    <div class="create_game_wrapper" ng-include="template"></div>
</div>
<div class="loader_beforeload {{finish_load}}">
    <img src="img/wait_ge.gif" />
</div>

<style>
    .page_action {
        float:right !important;
        margin-right:0px !important;
    }
    
    .page_action .system_button {
        margin-left:10px;
    }
    
    .page_action.floating {
        position:fixed;
        top:0px;
        width:1100px;
        height:45px;
        background-image:url('img/fade_overlay.png');
        background-repeat:repeat;
        -webkit-border-radius: 0 0 8px 8px;
        border-radius: 0 0 8px 8px;
        z-index:500;
    }
    
    .page_action.floating .actions_wrapper {
/*        width:360px;*/
        float:right;
        margin-top:8px;
    }
</style>