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
$requestArray = array("Rerun", "Pause", "Stop", "Delete", "Resume");


if(isset($_GET["request"]) && isset($_GET["expId"]) && in_array($_GET["request"], $requestArray)) {

    try{
        //identity check
        $con=getDBConnection();
        $sqlStr = "select UserName from Experiment where ExpId= ?";
        $stmt = $con->prepare($sqlStr);
        $stmt->bind_param("s", $_GET["expId"]);
        $stmt->execute();
        $results = $stmt->get_result();
        $row = mysqli_fetch_row($results);
        $userName = $row[0];

        if($userName==$_SESSION['user'] || isAdmin()) {

            function updateExpStatus($status, $message) {
                $con=getDBConnection();
                $sqlStr = "update Experiment set ExpStatus =?, Message=? where ExpId= ?";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("sss", $status, $message, $_GET["expId"]);
                $stmt->execute();
            }
            function updateNodeStatusFromTo($fromStatus, $toStatus, $message) {
                $con=getDBConnection();
                $sqlStr = "update Node set NodeStatus =?, Message=? where ExpId= ? and NodeStatus=?";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("ssss", $toStatus, $message, $_GET["expId"],$fromStatus);
                $stmt->execute();
            }

            function updateNewNodeStatus($status, $message) {
                updateNodeStatusFromTo("New", $status, $message);
            }

            $request = $_GET["request"];
            if($request === "Rerun") {
                $con=getDBConnection();
                $sqlStr = "update Node set NodeStatus = 'New' where ExpId= ?";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("s",$_GET["expId"]);
                $stmt->execute();

                updateExpStatus("New", '');
            }
            else if($request === "Pause") {
                updateExpStatus("Pausing", '');
                updateNodeStatusFromTo("Running", "Pausing", 'manual exp pausing');
            }
            else if($request === "Resume") {
                updateExpStatus("Running", '');
                updateNodeStatusFromTo("Pausing", "Running", 'Continue Exec');
            }
            else if($request === "Stop") {
                updateExpStatus("Failed", '');
                updateNodeStatusFromTo("Running", "Aborted", 'manual exp stop');
                updateNodeStatusFromTo("New", "Aborted", 'manual exp stop');
		  updateNodeStatusFromTo("Pausing", "Aborted", 'manual exp stop');
            }
            else if($request === "Delete") {
                $con=getDBConnection();
                $sqlStr = "delete from Node where ExpId= ?";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("s",$_GET["expId"]);
                $stmt->execute();

                $con=getDBConnection();
                $sqlStr = "delete from Experiment where ExpId= ?";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("s",$_GET["expId"]);
                $stmt->execute();

                $con=getDBConnection();
                $sqlStr = "delete from NodeDependence where ExpId= ?";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("s",$_GET["expId"]);
                $stmt->execute();
            }

            $isSuccess = true;
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
    echo "<font color='green'>Alter Experiment Succeeded!</font><br><br>";
} else {
    echo "<font color='red'>Alter Experiment Failed!</font><br><br>";
}

echo "The brower will go back to previous page in 3 seconds.<br>";
echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. $_SERVER['HTTP_REFERER'] . '">';
?>