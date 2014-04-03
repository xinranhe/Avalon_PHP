<?php
    include 'authCheck.php';
    include 'dbConfig.php';
    include 'userConfig.php';

    function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    set_error_handler('handleError');

    $isSuccess = true;

    if(isset($_GET["dataTypeId"])) {
        // Step 1: get new data type id
        try{

            if(isAdmin())
            {
                echo "Try delete datatype with Id: " . strval($_GET["dataTypeId"]) . "<br><br>";

                $con=getDBConnection();
                $sqlStr = "delete from DataType where DatatypeId= ?";
                $stmt = $con->prepare($sqlStr);
                $typeId = intval($_GET["dataTypeId"]);
                $stmt->bind_param("d", $typeId);
                $stmt->execute();

                if(mysqli_stmt_affected_rows($stmt)!=0) {
                    $isSuccess = true;
                }
                else {
                    echo "Unfounded datatype id<br><br>";
                }
            } else {
                $isSuccess = false;
                echo "Unarthorized user<br><br>";
            }

        } catch (Exception $e) {
            $isSuccess = false;
            echo 'Caught exception: ' .  $e->getMessage() . "<br><br>";
        }
    }
    else {
        echo "Invalid request<br><br>";
        $isSuccess = false;
    }
    if($isSuccess) {
        echo "<font color='green'>Data Type deletion Succeeded!</font><br><br>";
    } else {
        echo "<font color='red'>Data Type deletion Failed!</font><br><br>";
    }
    echo "The brower will go back to previous page in 3 seconds.<br>";
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
    echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. $_SERVER['HTTP_REFERER'] . '">';
?>