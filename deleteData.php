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
   // set_error_handler('handleError');

    $isSuccess = true;

    if(isset($_GET["dataId"])) {

        try{

            echo "Try delete data with Id: " . $_GET["dataId"] . "<br><br>";

            //identity check
            $con=getDBConnection();
            $sqlStr = "select UserName from Data where DataId= ?";
            $stmt = $con->prepare($sqlStr);
            $stmt->bind_param("s", $_GET["dataId"]);
            $stmt->execute();
            $results = $stmt->get_result();
            $row = mysqli_fetch_row($results);
            $userName = $row[0];

            if($userName==$_SESSION['user'] || isAdmin()) {

                $con=getDBConnection();
                $sqlStr = "delete from Data where DataId= ?";
                $stmt = $con->prepare($sqlStr);
                $typeId = $_GET["dataId"];
                $stmt->bind_param("s", $typeId);
                $stmt->execute();


                if(mysqli_stmt_affected_rows($stmt)!=0) {
                    $isSuccess = true;
                }
                else {
                    echo "Unfounded data Id<br><br>";
                    $isSuccess = false;
                }
            } else {
                $isSuccess = false;
                echo "Unauthorized user<br><br>";
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
        echo "<font color='green'>Data deletion Succeeded!</font><br><br>";
    } else {
        echo "<font color='red'>Data deletion Failed!</font><br><br>";
    }

    echo "The brower will go back to previous page in 3 seconds.<br>";
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
    echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. $_SERVER['HTTP_REFERER'] . '">';
?>