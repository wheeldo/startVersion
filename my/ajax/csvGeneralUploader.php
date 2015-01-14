<?php

$res=array();
header('Content-Type:application/json');
header('Content-Type: text/html; charset=utf-8');
$allowed_ext=array();
$allowed_ext[]="csv";
$allowed_ext[]="xls";
$allowed_ext[]="xlsx";

$ds=DIRECTORY_SEPARATOR;

if ($_FILES["csv"]["error"] > 0){
    echo "Return Code: " . $_FILES["csv"]["error"] . "<br>";
}
else {
    $ex=explode(".",$_FILES["csv"]["name"]);
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
        
        require_once dirname(__FILE__) . $ds . '..' . $ds .'vendor'. $ds .'PHPExcel'. $ds .'PHPExcel' . $ds .'IOFactory.php';
        
        $file_name=$_FILES['csv']['tmp_name'];
        
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
                }

            }
            
            
            $i++;
        }
        
        
        
        foreach($array_data as $row) {
            $c=0;
            foreach($row as $cell):
                $result[$c][]=$cell;
                $c++;
            endforeach;
        }
        
        var_dump($result);
        
        
        $base_path=dirname(__FILE__);
        $file_location=$base_path.$ds."..".$ds."uploads".$ds."csv".$ds.$_POST['res_mark'].".txt";
        
        file_put_contents($file_location, json_encode($result)); 
        $res['status']="ok";
        $res['data']=$result;
        echo json_encode($res);
        die();
    }

    elseif(($handle = fopen($_FILES['csv']['tmp_name'], 'r')) !== FALSE) {
        $result=array();
        // necessary if a large csv file
        set_time_limit(5);

        $row = 0;

        while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // number of fields in the csv
            $num = count($data);

            for($i=0;$i<$num;$i++):
                $result[$i][]=utf8_encode($data[$i]);
            endfor;

            $row++;
        }
        fclose($handle);

        
        $ds=DIRECTORY_SEPARATOR;
        $base_path=dirname(__FILE__);
        $file_location=$base_path.$ds."..".$ds."uploads".$ds."csv".$ds.$_POST['res_mark'].".txt";
        //unlink($file_location);
        file_put_contents($file_location, utf8_encode(json_encode($result, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE))); 
        $res['status']="ok";
        $res['data']=$result;
        echo json_encode($res);
        die();
    }
}