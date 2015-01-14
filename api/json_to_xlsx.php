<?php
header('Content-Type:application/json');
header('Content-Type: text/html; charset=utf-8');
$ds=DIRECTORY_SEPARATOR;

/** Error reporting */
error_reporting(E_ALL);

require_once dirname(__FILE__) . $ds .'vendor'. $ds .'PHPExcel'. $ds .'PHPExcel.php';

require_once dirname(__FILE__) . $ds .'vendor'. $ds .'PHPExcel'. $ds .'PHPExcel' . $ds .'Writer' . $ds . 'Excel2007.php';

if(empty($_POST)):
	$data = file_get_contents("php://input");
	$data_array=json_decode($data,true);
        if(!is_array($data_array)){
            //echo $data;
            
            $pairs = explode('&', $data);
            foreach($pairs as $pair):
                list($key, $value) = explode('=', $pair, 2);
                $_POST[$key]=$value;
            endforeach;
        
            //print_r(parse_str($data));
        }
        else {
            $_POST=$data_array;
        }
endif;


// fix array: 
$fixed_array=array();
foreach($_POST as $key=>$val):
    //echo urldecode ($key)."=>".$val."<br>";
    $dec=urldecode ($key);
    
    $ex1=explode("[",$dec);
    $row=(int)$ex1[0];
    $fixed_array[$row][]=$val;

endforeach;



// Create new PHPExcel object
//echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Set properties
//echo date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("Wheeldo system");
$objPHPExcel->getProperties()->setLastModifiedBy("Wheeldo system");
$objPHPExcel->getProperties()->setTitle("Wheeldo XLSX Document");
$objPHPExcel->getProperties()->setSubject("Wheeldo XLSX Document");
$objPHPExcel->getProperties()->setDescription("Wheeldo XLSX Document");


// Add some data
//echo date('H:i:s') . " Add some data\n";
$objPHPExcel->setActiveSheetIndex(0);

$let_arr=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

$row_c=1;
foreach($fixed_array as $row):
    $col_c=0;
    $it=$row;
    if(is_array($it[0])) {

        $it=$it[0];
    }
    
    foreach($it as $val):
        $val=urldecode ($val);
        $objPHPExcel->getActiveSheet()->SetCellValue($let_arr[$col_c].$row_c, $val);
        $col_c++;
    endforeach;
    $row_c++;
endforeach;

// Rename sheet
//echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Wheeldo data');

		
// Save Excel 2007 file
//echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

$stamp_name=time();
$file_name="excel_dw/".$stamp_name.".xlsx";
$objWriter->save($file_name);

// Echo done
//echo date('H:i:s') . " Done writing file.\r\n";

echo $stamp_name;