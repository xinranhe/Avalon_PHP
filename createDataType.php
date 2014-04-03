<?php
    include 'authCheck.php';
    include 'dbConfig.php';

    function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    set_error_handler('handleError');

    if(isset($_GET["dataTypeName"]) && isset($_GET["dataTypeDesc"])) {
        // Step 1: get new data type id
        try{

            $sqlStr = "select max(DatatypeId)+1 from DataType";
            $results = execQuery($sqlStr);
            $row = mysqli_fetch_row($results);
            $typeId = intval($row[0]);

            echo "New DataTypeId:" . $typeId . "<br><br>";

            $con=getDBConnection();
            $sqlStr = "insert into DataType values(?,?,?)";
            $stmt = $con->prepare($sqlStr);
            $stmt->bind_param("dss", $typeId, $_GET["dataTypeName"], $_GET["dataTypeDesc"]);
            $stmt->execute();

            echo "<font color='green'>New Data Type creation Succeeded!</font><br>";

        } catch (Exception $e) {
            echo "<font color='red'>New Data Type creation Failed!</font><br>";
            echo 'Caught exception: ' .  $e->getMessage(). "<br><br>";
        }
    }
    else {
        echo "<font color='red'>New Data Type creation Failed!</font><br>";
    }
    echo "The brower will go back to previous page in 3 seconds.<br>";
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
    echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. $_SERVER['HTTP_REFERER'] . '">';
?>