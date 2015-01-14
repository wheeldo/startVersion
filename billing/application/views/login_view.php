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
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
        <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" media="screen" />
        <script type="text/javascript" src="/assets/bootstrap/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="/assets/css/style.css" media="screen" />
        <script type="text/javascript" src="/assets/js/scr.js"></script>
        <script type="text/javascript">
            loginScripts();
        </script>
    </head>
    <body class="login">
        <div class="content">
           <form name="login" role="form" action="/index.php/login/loginCheck" method="post">
            <div class="form-group">
              <label for="exampleInputEmail1">Email address</label>
              <input type="email" class="form-control" id="email" placeholder="Enter email">
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password</label>
              <input type="password" class="form-control" id="password" placeholder="Password">
            </div>
            <button type="button" class="btn btn-default" id="submit_login">Submit</button>
          </form>
            
        <div class="alert alert-danger fade in">
         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
         <h4>Error</h4>
         <p id="em_data"></p>
       </div>

        </div>
    </body>
</html>




