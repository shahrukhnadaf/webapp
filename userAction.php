<?php
//load and initialize database class
require_once 'DB.class.php';
$db = new DB();

$tblName = 'studentdata';

if($_POST['action'] == 'download'){
    $servername = "127.0.0.1";
    $username = "root";
    $password = "root1234";
    $dbname = "students";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    $returnData = array(
            'status' => 'ok',
            'msg' => json_encode('success'),
            'data' => ' '
        );

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM studentdata";
    $result = $conn->query($sql);


    //$query = $db->query("SELECT * FROM studentdata ORDER BY id DESC");
    if($result->num_rows > 0){
        $delimiter = ",";
        $filename = "d.csv";
    
    //create a file pointer
        $f = fopen('d.csv', 'w');
    
    //set column headers
        $fields = array('MISID', 'FullName', 'YearOfAdmission', 'Fees', 'CGPA');
        fputcsv($f, $fields, $delimiter);
    
    //output each row of the data, format line as csv and write to file pointer
        while($row = $result->fetch_assoc()){
            $lineData = array($row['MISID'], $row['FullName'], $row['YearOfAdmission'], $row['Fees'], $row['CGPA']);
            fputcsv($f, $lineData, $delimiter);
        }
    
    //move back to beginning of file
        fseek($f, 0);
    
    //set headers to download file rather than displayed
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
    
    //output all remaining data on a file pointer
        fpassthru($f);
    }
//exit;
     $file = urldecode($_REQUEST["d.csv"]); // Decode URL-encoded string
    $filepath = $file;
    
    // Process download
    if(file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush(); // Flush system output buffer
        readfile($filepath);
        //exit;
    }
    echo json_encode($returnData);
}

if(($_POST['action'] == 'edit') && !empty($_POST['MISID'])){
    //update data
    $userData = array(
        'FullName' => $_POST['FullName'],
        'YearOfAdmission' => $_POST['YearOfAdmission'],
        'Fees' => $_POST['Fees'],
        'CGPA' => $_POST['CGPA']
    );
    $condition = array('MISID' => $_POST['MISID']);
    $update = $db->update($tblName, $userData, $condition);
    $fn = $userData['FullName'];
    $yoi = $userData['YearOfAdmission'];
    $f = $userData['Fees'];
    $c = $userData['CGPA'];
    $m = $condition['MISID'];
   // $query = "UPDATE ".$tblName." SET  FullName = '".$fn."', YearOfAdmission = '".$yoi."', Fees = '".$f."', CGPA = '".$c."' WHERE MISID = '".$m."';";
    
    if($update){
        $returnData = array(
            'status' => 'ok',
            'msg' => 'success',
            'data' => $userData
        );
    }else{
        $returnData = array(
            'status' => 'error',
            'msg' => json_encode($userData),
            'data' => ''
        );
    }
    
    echo json_encode($returnData);
}elseif(($_POST['action'] == 'delete') && !empty($_POST['MISID'])){
    //delete data
    $condition = array('MISID' => $_POST['MISID']);
    $delete = $db->delete($tblName, $condition);
    if($delete){
        $returnData = array(
            'status' => 'ok',
            'msg' => 'User data has been deleted successfully.'
        );
    }else{
        $returnData = array(
            'status' => 'error',
            'msg' => 'Some problem occurred, please try again.'
        );
    }
    
    echo json_encode($returnData);
}
elseif($_POST['action'] == 'add'){
    $userData = array(
        'MISID' => $_POST['mis'],
        'FullName' => $_POST['fullname'],
        'YearOfAdmission' => $_POST['yoi'],
        'Fees' => $_POST['fee'],
        'CGPA' => $_POST['cg']
    );
    $add = $db->insert($tblName, $userData);
    if($add){
        $returnData = array(
            'status' => 'ok',
            'msg' => json_encode($userData)
        );
    }else{
        $returnData = array(
            'status' => 'error',
            'msg' => json_encode($userData)
        );
    }
    
   echo json_encode($returnData);
}
die();
?>
