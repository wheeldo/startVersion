<?php
header('Content-Type:application/json');
header('Content-Type: text/html; charset=utf-8');
$allowed_ext=array();
$allowed_ext[]="csv";
$allowed_ext[]="xls";
$allowed_ext[]="xlsx";

$ds=DIRECTORY_SEPARATOR;


if ($_FILES["file"]["error"] > 0){
    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
}
else{
    $ex=explode(".",$_FILES["file"]["name"]);
    $ext=strtolower($ex[(count($ex)-1)]);
    /////////// security checks: ///////////////////
    // check no 1:
    if(!in_array($ext, $allowed_ext)) {
        $res['status']="faild";
        $res['error']="Your file is not a csv or excel file!";
        echo json_encode($res);
        die();
    } 
    
    
    
    if($ext=="xls"||$ext=="xlsx") {
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        require_once dirname(__FILE__) . $ds .'vendor'. $ds .'PHPExcel'. $ds .'PHPExcel' . $ds .'IOFactory.php';
        
        $file_name=$_FILES['file']['tmp_name'];
        
        if (!file_exists($file_name)) {
            exit("Please run Aviadtest.php first." . EOL);
        }

        
        $objPHPExcel = PHPExcel_IOFactory::load($file_name);

        $rowIterator=$objPHPExcel->getActiveSheet()->getRowIterator();
        

        $i=0;
        $result=array();
        $array_data = array();
        foreach($rowIterator as $row){
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            //if(1 == $row->getRowIndex ()) continue;//skip first row
            $rowIndex = $row->getRowIndex ();
            $array_data[$rowIndex] = array('A'=>'', 'B'=>'','C'=>'','D'=>'');
            set_time_limit(5);


            foreach ($cellIterator as $cell) {
                
                
                foreach(range('a','z') as $j):
                    $let=strtoupper ($j);
                
                    if($let == $cell->getColumn()){
                        $array_data[$rowIndex][$cell->getColumn()] = $cell->getCalculatedValue();
                    }
                
                endforeach;

            }
            
            
            $i++;
        }
    }
    
    
}


$res=array();

$res['data']=$array_data;
$res['res_mark']=$_POST['res_mark'];


//echo json_encode($res);

$url=$_POST['url_notify'];

$postArray=array();
$postArray['res_mark']=$_POST['res_mark'];
$array_data_no_indx=array();
$rowsC=0;
foreach($array_data as $data):
    $array_data_no_indx[$rowsC]=array();
    $colC=0;
    foreach($data as $cell):
        $array_data_no_indx[$rowsC][]=$cell;
        $colC++;
    endforeach;

    $rowsC++;
endforeach;

//var_dump($array_data_no_indx);

$postArray['data']=json_encode($array_data_no_indx);


function doRequest($url,$postArray) {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postArray);
    $response = curl_exec($ch); 
    curl_close($ch);
    return $response;
}




$r=doRequest($url,$postArray);
var_dump($r);

