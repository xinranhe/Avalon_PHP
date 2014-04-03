<?php
include 'authCheck.php';
include 'dbConfig.php';
if(isset($_GET["dataId"])) {

    $con=getDBConnection();
    $sqlStr = "select FileLocation from Data where DataId= ? ";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("s", $_GET["dataId"]);
    $stmt->execute();
    $results = $stmt->get_result();
    $row = mysqli_fetch_row($results);

    $fullFilePath = $row[0];
    //header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$fullFilePath\"");
    echo file_get_contents($fullFilePath);
    exit();
}
?>
