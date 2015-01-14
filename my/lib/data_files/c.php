<?php

function csv2arr($path) {
    $returnArr=array();
	if(($handle = fopen($path, 'r')) !== FALSE) {
            $result=array();
            // necessary if a large csv file
            set_time_limit(5);

            $row = 0;

            while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $returnArr[$row]['code']=$data[0];
                $returnArr[$row]['name']=$data[1];
                //var_dump($data);
                // number of fields in the csv
                $num = count($data);

                for($i=0;$i<$num;$i++):
                    $result[$i][]=utf8_encode($data[$i]);
                endfor;

                $row++;
            }
            fclose($handle);
            return $returnArr;
	}
	else {
		return false;
	}
}



$a=csv2arr("c.csv");

//var_dump($a);

foreach($a as $c):
    //var_dump($c);
    ?>
    <state>
            <code><?=$c['code']?></code>
            <name><?=$c['name']?></name>
    </state>
    <?
endforeach;