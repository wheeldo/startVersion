<?php
require_once('../modules/modules.php');
require_once('../checkLogin.php');
$ds=DIRECTORY_SEPARATOR;
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
require_once dirname(__FILE__) . $ds . '..' . $ds .'vendor'. $ds .'PHPExcel'. $ds .'PHPExcel' . $ds .'IOFactory.php';


if(isset($_POST['get_table'])):
    $sqldb=@mysql_connect(DB_HOST,USER,PASSWORD) or die("error - unable to connect to database server");
    @mysql_select_db($_POST['get_table'],$sqldb);
    $result = mysql_query("SHOW TABLES");
    $tables=array();
    while ($row = mysql_fetch_array($result)):
        $tables[]=$row[0];
    endwhile;
    header('Content-Type:application/json');
    echo json_encode($tables);
    die();
endif;

if(isset($_POST['get_columns'])):
    $db=$_POST['db'];
    $table=$_POST['table'];
    $sqldb=@mysql_connect(DB_HOST,USER,PASSWORD) or die("error - unable to connect to database server");
    @mysql_select_db($db,$sqldb);
    $result = mysql_query("SHOW COLUMNS FROM $table");
    $columns=array();
    while ($row = mysql_fetch_array($result)):
        $columns[]=$row[0];
    endwhile;
    header('Content-Type:application/json');
    echo json_encode($columns);
    die();
endif;

if(isset($_POST['searchandreplace'])):
    $db=$_POST['db'];
    $table=$_POST['table'];
    $sqldb=@mysql_connect(DB_HOST,USER,PASSWORD) or die("error - unable to connect to database server");
    @mysql_select_db($db,$sqldb);
    
    $fields=$_POST['query'];

    $where="WHERE `".key($_POST['where_case'])."`='{$_POST['where_case'][key($_POST['where_case'])]}'";
    //var_dump($fields);
    //echo $where;
    
    $dbop->updateWhereDB($table,$fields,$where);
    die();
endif;







if(!empty($_FILES)):
    ini_set('memory_limit', '512M');
    set_time_limit(240);
    $file_name=$_FILES['file']['tmp_name'];


    if ($_FILES["file"]["error"] > 0) {
            die("Return Code: " . $_FILES["file"]["error"] . "<br>");
    }
    else{
        echo "Upload: " . $_FILES["file"]["name"] . "<br>";
        //echo "Type: " . $_FILES["file"]["type"] . "<br>";
        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
        //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

        if (file_exists("uploads/" . $_FILES["file"]["name"])){
             echo $_FILES["file"]["name"] . " already exists. ";
        }
        else {
            move_uploaded_file($_FILES["file"]["tmp_name"],
            "uploads/" . $_FILES["file"]["name"]);
            //echo "Stored in: " . "uploads/" . $_FILES["file"]["name"];
        }
    }
    
    
    $file_name="uploads/" . $_FILES["file"]["name"];

    $objPHPExcel = PHPExcel_IOFactory::load($file_name);
        
    $rowIterator=$objPHPExcel->getActiveSheet()->getRowIterator();


    $i=0;
    $result=array();
    $array_data = array();
    foreach($rowIterator as $row){
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
        if(1 == $row->getRowIndex ()) continue;//skip first row
        $rowIndex = $row->getRowIndex ();
        $array_data[$rowIndex] = array('A'=>'', 'B'=>'','C'=>'','D'=>'');
        set_time_limit(5);


        foreach ($cellIterator as $cell) {
            if('A' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('B' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('C' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = PHPExcel_Style_NumberFormat::toFormattedString($cell->getCalculatedValue(), 'YYYY-MM-DD');
            } else if('D' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('E' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('F' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('G' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('H' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('I' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('J' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('K' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('L' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('M' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('N' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('O' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('P' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('Q' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('R' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } else if('S' == $cell->getColumn()){
                $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
            } 
            

        }


        $i++;
    }
    
    
    //var_dump($array_data);
    $_SESSION['json_hash']=md5(time());
    $json=json_encode($array_data);
    file_put_contents("uploads/".$_SESSION['json_hash'].".json",$json); 

endif;


if(!empty($_POST)):
    
    var_dump($_POST);
    
    
endif;

//var_dump($_SESSION);
//$arr=json_decode(file_get_contents("uploads/".$_SESSION['json_hash'].".json"),true);
//var_dump($arr);

        
?>

<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script>
            
            <?if(isset($_SESSION['json_hash'])&&file_exists("uploads/".$_SESSION['json_hash'].".json")):?>
                var upload_obj=<?=file_get_contents("uploads/".$_SESSION['json_hash'].".json")?>;
                $(document).ready(function(){
                    $("#total").html(upload_obj.length);
                });
                //jQuery.parseJSON(
                //var json_file=jQuery.parseJSON(upload_obj);
            <?endif;?>
            
            getTables = function(db) {
                if(db=="0")
                    return;
                 $.ajax({
                    type: "post",
                    dataType:"json",
                    url: document.URL ,
                    data:{
                        "get_table":db
                    },
                    success: function(data) {
                        $("#table").html("");
                        $("#table").append('<option value="0"></option>');
                        for(i in data) {
                            var t_name=data[i];
                            $("#table").append('<option value="'+t_name+'">'+t_name+'</option>');
                        }
                    }
                });
            };
            
            
            getColumns = function(table) {
                if(table=="0")
                    return;
                 $.ajax({
                    type: "post",
                    dataType:"json",
                    url: document.URL ,
                    data:{
                        db:$("#db").val(),
                        get_columns:1,
                        table:table
                        
                    },
                    success: function(data) {
                        table_columns=data;
                        showMapOptions()
                    }
                });
            };
            
            
            showMapOptions = function() {
                $(".columns").html("");
                $(".fields_wrap").html("");
                $(".columns").append('<option value="0"></option>');
                for(i in table_columns) {
                    var column=table_columns[i];
                    $(".columns").append('<option value="'+column+'">'+column+'</option>');
                    
                    $(".fields_wrap").append('<div class="field_row"><label>'+column+':</label><select class="file_fields fields_query" id="'+column+'_field" field_name="'+column+'"></select></div>');
                    
                    
                }
                
                
                
                //console.log(upload_obj[2]);
                $(".file_fields").html("");
                
                
                $(".file_fields").append('<option value="0"></option>');
                for(i in upload_obj[2]) {
                    var file_field=upload_obj[2][i];
                    $(".file_fields").append('<option value="'+i+'">'+file_field+'</option>');
                }
                
                
                $("#map_wrap").show();
            };
            
            var table_columns=[];
            
            var upload_obj_current_key=0;
            var c=1;
            search_and_update = function() {
                //$("#map_wrap").hide();
                $(".proccess_wrap").show();
                
                $("#res").html(c);
                
                
                
                
                if(!upload_obj[upload_obj_current_key]) {
                    upload_obj_current_key++;
                    search_and_update();
                    return;
                }
            
               var user_obj=upload_obj[upload_obj_current_key];
               
               
               var query_fields={};
               
               
               $(".fields_query").each(function(){
                   var value=$(this).val();
                   var field_name=$(this).attr("field_name");
                   
                   if(value!="0") {
                       query_fields[field_name]=upload_obj[upload_obj_current_key][value]; 
                   }

               });
               
               
               
               
               var where_case={};

               where_case[$("#key_table").val()]=upload_obj[upload_obj_current_key][$("#key_file").val()];
               
               
               $.ajax({
                    type: "post",
                    url: document.URL ,
                    data:{
                        db:$("#db").val(),
                        table:$("#table").val(),
                        query:query_fields,
                        where_case:where_case,
                        searchandreplace:1
                        
                    },
                    success: function(data) {
                        c++;
                        upload_obj_current_key++;
                        search_and_update();
                    }
                });
               
               //console.log(query_fields);
            };
        </script>
        <style>
            #map_wrap {
                display:none;
            }
            
            .row {
                margin-bottom:20px;
            }
            
            .field_row {
               margin-bottom:5px; 
            }
            
            .field_row label {
                display:block;
                width:150px;
                float:left;
                    
            }
            
            .proccess_wrap {
                display:none;
            }
        </style>
    </head>
    <body>
        <h4>Upload xslx file</h4>
        <form id="file_upload_form"  method="post" enctype="multipart/form-data" action="">
            <input type="file" class="file" name="file"  />
            <input type="submit" />
        </form>
        <hr>
        <h4>Map table</h4>
        <select id="db" onchange="getTables(this.value)">
            <option value="0"></option>
            <?$result = mysql_query("SHOW DATABASES");while ($row = mysql_fetch_array($result)):?>
            <option value="<?=$row[0]?>"><?=$row[0]?></option>
            <?endwhile;?>
        </select>
        
        <select id="table" onchange="getColumns(this.value)">
            
        </select>
        
        <div id="map_wrap">
            <hr>
            <h4>Mapping</h4>
            <div class="row">
                <label>Map key(search & replace with this key):</label>
                Table column:
                <select class="columns" id="key_table"></select>
                File column:
                <select class="file_fields" id="key_file"></select>
            </div>
            
            <div class="row fields_wrap">
                
            </div>
            
            
            <button type="button" onclick="search_and_update();">Search &amp; Update</button>
        
        </div>
        
        
        <div class="proccess_wrap">
            Please wait... <br>
            Processing <span id="res">0</span> out of <span id="total">1</span>
            
        </div>

        
    </body>
</html>