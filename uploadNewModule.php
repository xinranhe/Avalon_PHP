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

    if(isset($_POST["moduleName"]) && isset($_POST["moduleType"]) && isset($_POST["moduleVersion"])
        && isset($_POST["moduleDesc"]) && isset($_POST["inputArg"]) && isset($_POST["outputArg"]) &&
        isset($_POST["paraArg"])) {

        $module = array();
        $module["moduleId"] = substr(md5(rand()), 0, 7);
        $module["moduleTypeId"] = intval($_POST["moduleType"]);
        $module["name"] = $_POST["moduleName"];
        $module["version"] = $_POST["moduleVersion"];
        $module["userId"] = $_SESSION["user"];
        date_default_timezone_set('America/Los_Angeles');
        $module["createTime"] = date('Y-m-d H:i:s');
        $module["description"] = $_POST["moduleDesc"];
        $module["inputArguments"] = $_POST["inputArg"];
        $module["outputArguments"] = $_POST["outputArg"];
        $module["modelParameters"] = $_POST["paraArg"];
        $jsonStr = json_encode($module, JSON_PRETTY_PRINT);

        echo "New module Json:" . $jsonStr . "<br><br>";

        // handle upload file of mainFile
        // limit file size to 200k
        echo "uploaded Fileinfo:<br>";
        echo "Handle Module main File (exec entry):<br><br>";

        include 'fileLocationConfig.php';

        try{

            if ($_FILES["moduleMainFile"]["error"] > 0)
            {
                echo "Error: " . $_FILES["moduleMainFile"]["error"] . "<br><br>";
                echo "<font color='red'>New Data upload failed!</font><br><br>";
            }
            else
            {
                // echo info
                echo "Upload: " . $_FILES["moduleMainFile"]["name"] . "<br>";
                echo "Type: " . $_FILES["moduleMainFile"]["type"] . "<br>";
                echo "Size: " . ($_FILES["moduleMainFile"]["size"] / 1024) . " kB<br>";
                echo "Stored in: " . $_FILES["moduleMainFile"]["tmp_name"]. "<br><br>";
                // create folder
                $folderPath = $uploadModuleDir . DIRECTORY_SEPARATOR . $module["moduleId"];
                echo "Create folder:" . $folderPath . "<br>";
                $oldmask = umask(0);
                mkdir($folderPath, 0777);
                umask($oldmask);
                $fullFilePath = $folderPath . DIRECTORY_SEPARATOR . $_FILES["moduleMainFile"]["name"];
                echo "File uploaded to: " . $fullFilePath . "<br>";
                move_uploaded_file($_FILES["moduleMainFile"]["tmp_name"], $fullFilePath);
                chmod($fullFilePath, 0777);

                echo "Handle Module other Files<br><br>";
                if($_FILES["moduleOtherFiles"]) {
                    $file = $_FILES["moduleOtherFiles"];
                    $file_count = count($file['name']);

                    for ($i=0; $i < $file_count; $i++) {
                        echo "Upload: " . $file["name"][$i] . "<br>";
                        echo "Type: " . $file["type"][$i] . "<br>";
                        echo "Size: " . ($file["size"][$i] / 1024) . " kB<br>";
                        echo "Stored in: " . $file["tmp_name"][$i]. "<br><br>";
                        // create folder
                        $folderPath = $uploadModuleDir . DIRECTORY_SEPARATOR . $module["moduleId"];
                        $fullFilePath = $folderPath . DIRECTORY_SEPARATOR . $file["name"][$i];
                        echo "File uploaded to: " . $fullFilePath . "<br><br>";
                        move_uploaded_file($file["tmp_name"][$i], $fullFilePath);
                        chmod($fullFilePath, 0777);
                    }
                }

                // insert the new module into PendingItem table
                $sqlStr = sprintf("insert into PendingItem values('%s','%s','%s','%s','%s','New','')",
                    $module["moduleId"], 'Module', $module["moduleId"], $_FILES["moduleMainFile"]["name"], $jsonStr);
                echo "Insert new module into Pending Table" . $sqlStr . "<br>";

                include 'dbConfig.php';
                $con=getDBConnection();
                $sqlStr = "insert into PendingItem values(?,'Module',?,?,?,'New','')";
                $stmt = $con->prepare($sqlStr);
                $stmt->bind_param("ssss", $module["moduleId"], $module["moduleId"], $_FILES["moduleMainFile"]["name"], $jsonStr);
                $stmt->execute();
                $results = $stmt->get_result();

                echo "<font color='green'>New Module upload succeeded!</font><br><br>";
            }
        } catch (Exception $e) {
            echo "<font color='red'>New Module upload failed!</font><br>";
            echo 'Caught exception: ' .  $e->getMessage(). "<br><br>";
        }
    } else {
        echo "<font color='red'>New Module upload failed!</font><br><br>";
    }
    echo "The brower will go back to previous page in 3 seconds.<br>";
    echo "<a href=\"javascript:history.go(-1)\">GO BACK</a>";
    echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. $_SERVER['HTTP_REFERER'] . '">';
?>