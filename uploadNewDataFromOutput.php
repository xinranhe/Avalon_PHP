<?php
include 'authCheck.php';

function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('handleError');
?>
<html>
<head>
    <script>
        function loaded()
        {
            window.setTimeout(CloseMe, 3000);
        }

        function CloseMe()
        {
            window.close();
        }
    </script>
</head>
<body onLoad="loaded()">

<?php
if(isset($_POST["dataName"]) && isset($_POST["dataType"]) && isset($_POST["dataVersion"])
    && isset($_POST["dataDesc"]) && isset($_POST["expId"]) && isset($_POST["portId"]) && isset($_POST["nodeId"])) {
    $data = array();
    $data["dataId"] = substr(md5(rand()), 0, 7);
    $data["dataTypeId"] = intval($_POST["dataType"]);
    $data["name"] = $_POST["dataName"];
    $data["version"] = $_POST["dataVersion"];
    $data["userId"] = $_SESSION["user"];
    date_default_timezone_set('America/Los_Angeles');
    $data["createTime"] = date('Y-m-d H:i:s');
    $data["description"] = $_POST["dataDesc"];
    $jsonStr = json_encode($data, JSON_PRETTY_PRINT);

    echo "New Data Json:" . $jsonStr . "<br><br>";

    // handle upload file

    // create folder
    include 'fileLocationConfig.php';
    $folderPath = $uploadDataDir . DIRECTORY_SEPARATOR . $data["dataId"];

    echo "Create folder:" . $folderPath . "<br><br>";
    $oldmask = umask(0);
    mkdir($folderPath, 0777);
    umask($oldmask);

    $portId = strval(intval($_POST['portId'])-1);

    $dataFileName = $_POST["expId"] . "_" . $_POST["nodeId"] . "_" . $portId . ".out";
    $fromPath = $expDir . DIRECTORY_SEPARATOR . $_POST["expId"] . DIRECTORY_SEPARATOR . $dataFileName;
    $fullFilePath = $folderPath . DIRECTORY_SEPARATOR . $dataFileName;
    echo "Copy File from " . $fromPath . " to: " . $fullFilePath . "<br><br>";
    copy($fromPath, $fullFilePath);

    chmod($fullFilePath, 0666);
    // insert the new Data into PendingItem table
    $sqlStr = sprintf("insert into PendingItem values('%s','%s','%s','%s','%s','New','')",
            $data["dataId"], 'Data', $data["dataId"], $dataFileName, $jsonStr);
    echo "Insert new Data into Pending Table" . $sqlStr . "<br><br>";

    include 'dbConfig.php';

    $con=getDBConnection();
    $sqlStr = "insert into PendingItem values(?,'Data',?,?,?,'New','')";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("ssss", $data["dataId"], $data["dataId"], $dataFileName, $jsonStr);
    $stmt->execute();

    echo "<font color='green'>New Data upload succeeded!</font><br><br>";
} else {
    echo "<font color='red'>New Data upload failed!</font><br><br>";
}
echo "The window will be closed in 3 seconds.<br>";
?>
</body>
</html>