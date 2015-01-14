<?php
if(!empty($_FILES)) {
    $csv = array();

    // check there are no errors
    if($_FILES['csv']['error'] == 0){
        $name = $_FILES['csv']['name'];
        $ex=explode('.', $_FILES['csv']['name']);
        $ext = strtolower($ex[count($ex)-1]);
        $type = $_FILES['csv']['type'];
        $tmpName = $_FILES['csv']['tmp_name'];

        // check the file is a csv
        if($ext === 'csv'){
            if(($handle = fopen($tmpName, 'r')) !== FALSE) {
                // necessary if a large csv file
                set_time_limit(0);

                $row = 0;

                while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    // number of fields in the csv
                    $num = count($data);

                    // get the values from the csv
                    $csv[$row]['userName'] = $data[0];
                    $csv[$row]['userEmail'] = $data[1];
                    $csv[$row]['userDepartment'] = isset($data[2]) ? $data[2] : "";
                    $csv[$row]['userPosition'] = isset($data[3]) ? $data[3] : "";
                    $csv[$row]['userLevel'] = isset($data[4]) ? $data[4] : "";
                    $csv[$row]['userEmpID'] = isset($data[5]) ? $data[5] : "";


                    // inc the row
                    $row++;
                }
                fclose($handle);
            }
        }
        else {
            echo "Error - file type";
        }
    }
    else {
        echo 'Error - Unknown';
    }
  if(count($csv)>0) {
      $c=0;
      $users=array();
      foreach($csv as $nu):
            $users[$c][0]=$nu['userName'];
            $users[$c][1]=$nu['userEmail'];
            $users[$c][2]=$nu['userEmpID'];
            $c++;
      endforeach;
      //header('Content-type: application/json');
      echo json_encode($users);
  }
}
else {
    echo "[]";
} 
   
