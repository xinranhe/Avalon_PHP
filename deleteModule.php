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

    if(isset($_GET["moduleId"])) {
        // Step 1: get new module type id
        try{

            //identity check
            $con=getDBConnection();
            $sqlStr = "select UserName from Module where ModuleId= ?";
            $stmt = $con->prepare($sqlStr);
            $stmt->bind_param("s", $_GET["moduleId"]);
            $stmt->execute();
            $results = $stmt->get_result();
            $row = mysqli_fetch_row($results);
            $userName = $row[0];

            if($userName==$_SESSION['user'] || isAdmin()) {

                echo "Try delete module with Id: " . strval($_GET["moduleId"]) . "<br><br>";

                $con=getDBConnection();
                $sqlStr = "delete from Module where ModuleId= ?";
                $stmt = $con->prepare($sqlStr);
                $typeId = $_GET["moduleId"];
                $stmt->bind_param("s", $typeId);
                $stmt->execute();

                if(mysqli_stmt_affected_rows($stmt)!=0) {
                    $isSuccess = true;
                }
                else {
                    $isSuccess = false;
                    echo "Unfounded module id<br><br>";
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
        $isSuccess = false;
        echo "Invalid request<br><br>";
    }
    if($isSuccess) {
        echo "<font color='green'>Module deletion Succeeded!</font><br><br>";
    } else {
        echo "<font color='red'>Module deletion Failed!</font><br><br>";
    }
    echo "The brower will go back to previous page in 3 seconds.<br>";
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
    echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. $_SERVER['HTTP_REFERER'] . '">';
?>