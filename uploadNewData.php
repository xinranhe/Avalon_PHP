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

    if(isset($_POST["dataName"]) && isset($_POST["dataType"]) && isset($_POST["dataVersion"])
        && isset($_POST["dataDesc"])) {
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
        // limit file size to 200k
        echo "uploaded Fileinfo:<br>";
        if ($_FILES["dataFile"]["error"] > 0)
        {
            echo "Error: " . $_FILES["dataFile"]["error"] . "<br><br>";
            echo "<font color='red'>New Data upload failed!</font><br><br>";
        }
        else
        {
            //echo info
            echo "Upload: " . $_FILES["dataFile"]["name"] . "<br>";
            echo "Type: " . $_FILES["dataFile"]["type"] . "<br>";
            echo "Size: " . ($_FILES["dataFile"]["size"] / 1024) . " kB<br>";
            echo "Stored in: " . $_FILES["dataFile"]["tmp_name"]. "<br><br>";
            // create folder
            include 'fileLocationConfig.php';
            $folderPath = $uploadDataDir . DIRECTORY_SEPARATOR . $data["dataId"];

            echo "Create folder:" . $folderPath . "<br><br>";
            $oldmask = umask(0);
            mkdir($folderPath, 0777);
            umask($oldmask);
            $fullFilePath = $folderPath . DIRECTORY_SEPARATOR . $_FILES["dataFile"]["name"];
            echo "File uploaded to: " . $fullFilePath . "<br><br>";
            move_uploaded_file($_FILES["dataFile"]["tmp_name"], $fullFilePath);
            chmod($fullFilePath, 0666);
            // insert the new Data into PendingItem table
            $sqlStr = sprintf("insert into PendingItem values('%s','%s','%s','%s','%s','New','')",
                $data["dataId"], 'Data', $data["dataId"], $_FILES["dataFile"]["name"], $jsonStr);
            echo "Insert new Data into Pending Table" . $sqlStr . "<br><br>";
            include 'dbConfig.php';

            $con=getDBConnection();
            $sqlStr = "insert into PendingItem values(?,'Data',?,?,?,'New','')";
            $stmt = $con->prepare($sqlStr);
            $stmt->bind_param("ssss", $data["dataId"], $data["dataId"], $_FILES["dataFile"]["name"], $jsonStr);
            $stmt->execute();

            echo "<font color='green'>New Data upload succeeded!</font><br><br>";
        }
    } else {
        echo "<font color='red'>New Data upload failed!</font><br><br>";
    }
    echo "The brower will go back to previous page in 3 seconds.<br>";
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
    echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. $_SERVER['HTTP_REFERER'] . '">';
?>