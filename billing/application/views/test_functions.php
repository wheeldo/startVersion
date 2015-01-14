<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Billing control</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" media="screen" />
        <script type="text/javascript" src="/assets/bootstrap/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="/assets/css/style.css" media="screen" />
        <script type="text/javascript" src="/assets/js/scr.js"></script>
        <script type="text/javascript">
            test_functions();
        </script>
    </head>
    <body class="tests">
        <div style="width:800px;">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
          <li><a href="#create_shopper" data-toggle="tab">Create shopper</a></li>
          <li><a href="#token" data-toggle="tab">Shoppers Actions</a></li>
          <li><a href="#flow" data-toggle="tab">Flow</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div class="tab-pane active" id="create_shopper">
                <div class="content">
                    <h4>Create shopper</h4>
                    <div class="form-group">
                       <label for="exampleInputEmail1">Wheeldo user id:</label>
                       <input type="text" class="form-control create_shopper" name="userID" placeholder="Wheeldo user id">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">First name:</label>
                       <input type="text" class="form-control create_shopper" name="firstName" placeholder="First name">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">Last name:</label>
                       <input type="text" class="form-control create_shopper" name="lastName" placeholder="Last name">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">Address:</label>
                       <input type="text" class="form-control create_shopper" name="address1" placeholder="Address">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">City:</label>
                       <input type="text" class="form-control create_shopper" name="city" placeholder="City">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">State:</label>
                       <input type="text" class="form-control create_shopper" name="state" placeholder="State">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">Country:</label>
                       <input type="text" class="form-control create_shopper" name="country" placeholder="Country">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">Zip code:</label>
                       <input type="text" class="form-control create_shopper" name="zipCode" placeholder="Zip code">
                     </div>
                     <div class="form-group">
                       <label for="exampleInputEmail1">Phone:</label>
                       <input type="text" class="form-control create_shopper" name="phone" placeholder="Phone">
                     </div>
                    <button type="button" class="btn btn-default check_func" func="create_shopper">Create shopper</button>
                    <div class="response create_shopper_res">

                    </div>
                 </div>
            </div>
            <div class="tab-pane" id="token">
                <div class="content">
                    <h4>Get token</h4>
                    <div class="form-group">
                       <label for="exampleInputEmail1">Shopper:</label>
                       <select class="get_token get_shopper_data" name="shopperId">
                           <?foreach($shoppers as $shopper):?>
                           <option value="<?=$shopper->shopper_id?>"><?=$shopper->userID?> - <?=$shopper->firstName?> <?=$shopper->lastName?></option>
                           <?endforeach;?>
                       </select>
                     </div>
                    
                    <div class="form-group">
                       <label for="exampleInputEmail1">Select product:</label>
                       <select class="get_token" name="contractId">
                          <?foreach($contracts as $contract):?>
                           <option value="<?=$contract['id']?>"><?=$contract['name']?> - <?=$contract['price']?></option>
                           <?endforeach;?>
                       </select>
                     </div>
                    <button type="button" class="btn btn-default check_func" func="get_token">Get token</button>
                    <button type="button" class="btn btn-default check_func" func="get_shopper_data">Get shopper data</button>
                    <div class="response get_token_res">

                    </div>
                    <div class="response get_shopper_data_res">

                    </div>
                 </div>
            </div>
            <div class="tab-pane" id="flow">
                
                <div class="content">
                    <h4>Flow (Purchase page)</h4>
                    <div class="form-group">
                       <label for="exampleInputEmail1">Select user:</label>
                       <select class="flow" name="shopperId">
                           <?foreach($users as $user):?>
                           <option value="<?=$user->userID?>"><?=$user->userName?>, <?=$user->userEmail?> (<?=$user->organizationName?> <?=$user->organizationID?>)</option>
                           <?endforeach;?>
                       </select>
                    </div>
                    
                    <div class="packages_wrap">
                      <?foreach($contracts as $contract):?>
                      <div class="radio">
                        <label>
                          <input type="radio" name="optionsRadios" id="optionsRadios1" value="<?=$contract['id']?>" checked>
                            <?=$contract['name']?> - <?=$contract['price']?>
                        </label>
                      </div>
                      <?endforeach;?>
                    </div>
                 </div>
                
            </div>
        </div>
        </div>
        
        
    </body>
</html>




