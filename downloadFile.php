<?php
include 'authCheck.php';
if(isset($_GET["expId"]) && isset($_GET["nodeId"]) && isset($_GET["portId"])) {
    $expId = $_GET["expId"];
    $nodeId = $_GET["nodeId"];
    $portId = $_GET["portId"];
    include 'fileLocationConfig.php';
    $fileName = $expId . '_' . $nodeId . '_' . $portId . '.out';
    $fullFilePath = $expDir . DIRECTORY_SEPARATOR . $expId . DIRECTORY_SEPARATOR . $fileName;

    //header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$fullFilePath\"");
    echo file_get_contents($fullFilePath);
    exit();
}
?>