<div class="wheeldoSlider" id="slider_app_<?=$_POST['appID']?>">
    <div class="slide_screen create_game_stages create_game_stage1">
        <div class="top">
            <h1>
                Create a new 'Virtual Discussion' Game
            </h1>
        </div>
        <div class="middle">
            <table class="app_edit">
                <tr>
                    <td style="width:75px;"><img class="app_icon" src="img/app_icon.png" alt="app name" /></td>
                    <td style="width:675px;padding-left:10px;">
                        <div class="app_name">
                            <div>How do you like to name this copy of 'Virtual Discussion'?</div>
                            <input type="text" class="app_name_input autoTxT" id="game_name" title="Enter a name for your game" />
                        </div>                    
                        <iframe width="685px" height="360px" border="0" src="http://wheeldo.com.loc/apps/Mountaineer/edit/734"></iframe> 
                    </td>
                </tr>
            </table>
        </div>
        <div class="bottom">
            <a href="javascript:void(0)" class="load_previous_game">Load previous Game</a>
            <a href="javascript:void(0)" class="cancel hide">Cancel</a>
            <a href="javascript:void(0)" class="ready_to_publish next">Ready to Publish</a>
        </div>
    </div>
    <div class="slide_screen create_game_stages">
        <div class="top">
            <h1>
                Publish 'My Social Trivia'
            </h1>
            <h5>Copy of Social Trivia</h5>
        </div>
        <div class="middle">
            <table class="recipients">
                <tr>
                    <th>Recipients:</th>
                    <td class="recipients_th"><input type="radio" value="new" name="recipients" checked="checked" /> New team</td>
                    <td class="recipients_th"><input type="radio" value="exist" name="recipients" /> Existing team</td>
                    <td class="recipients_th"><input type="radio" value="file" name="recipients" /> Load players from file</td>
                </tr>
            </table>
            
            <div class="team_wrap">
                <div class="new choose_recipients">
                    <div class="left">
                        <input type="text" class="autoTxT" id="new_team_name" title="Enter a name for your new team" />
                    </div>
                    <div class="right">
                        This team has a total of: <br />
                        <span class="team_num">6</span> Players <a href="javascript:void(0)">Show</a>
                    </div>
                     <br class="clr" />
                     <div class="team_members">
                     </div>
                    <div>
                        <table style="width:100%;">
                            <tr>
                                <td><input type="text" class="autoTxT" id="new_team_player_name" title="Player name" /></td>
                                <td><input type="text" class="autoTxT" id="new_team_player_email" title="Player E-Mail" /></td>
                                <td style="padding-left:10px;width:180px;"><a href="javascript:add_new_player()" class="add_player">Add player to team</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="exist choose_recipients">
                    <div class="left">

                            <select id="selected_team">
                                    <option value="0_0">Please select</option>
                                    <option value="1_12">team test</option>
                                    <option value="2_80">hello team</option>
                                    <option value="3_23">hi team</option>
                                    <option value="4_57">sado team</option>

                            </select>
                    </div>
                    <div class="right">
                        This team has a total of: <br />
                        <span class="team_num">6</span> Players <a href="javascript:void(0)">Show</a>
                    </div>
                     <br class="clr" />
                </div>
                <div class="file choose_recipients">
                    <div class="left">
                        <input type="text" class="autoTxT" id="file_team_name" title="Enter a name for your new team" />
                    </div>
                    <div class="right">
                        This team has a total of: <br />
                        <span class="team_num">6</span> Players <a href="javascript:void(0)">Show</a>
                    </div>
                     <br class="clr" />
                     <div class="team_members">
                     </div>
                    <div>
                        <form id="file_upload_form" method="post" enctype="multipart/form-data" action="ajax/upload_team.php" onsubmit="set_ajax_load()">
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <input type="file" name="csv" id="file" class="customfile-input">
                                    </td>
                                    <td style="width:150px;padding-left:10px;">
                                        <input type="submit" value="Upload" class="upload-button" />
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <iframe id="upload_target" name="upload_target" onload="uploadDone()" src="" style="width:0;height:0;border:0px solid #fff;"></iframe>
                    </div>
                </div>
            </div>
            <div class="email_content">
                <h4>Email Content</h4>
                <input type="text" class="autoTxT" id="email_subject" title="Enter the subject of the mail" />
                <textarea id="email_content" class="autoTxT" title="Enter the content of the mail"></textarea>
            </div>
        </div>
        <div class="bottom">
            <a href="javascript:void(0)" class="back arrow_back prev"><img src="img/back.png" /></a>
<!--            <a href="javascript:void(0)" class="preview_game">Preview</a>-->
            <a href="javascript:void(0)" class="cancel hide">Cancel</a>
            <a href="javascript:void(0)" class="ready_to_publish publish" appID="<?=$_POST['appID']?>">Publish</a>
        </div>
    </div>
    <div class="slide_screen create_game_stages">
        <table class="publish">
            <tr>
                <td>
                    <div id="wait_img"></div>
                    <div id="wait_text"></div>
                </td>
            </tr>
        </table>
    </div>
</div>
