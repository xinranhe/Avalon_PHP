<?php
    include 'authCheck.php';
    include 'dbConfig.php';
    include 'fileLocationConfig.php';

    function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    set_error_handler('handleError');

    if(isset($_POST["expName"]) && isset($_POST["expDesc"]) && isset($_POST["expJson"])) {
        try {
            $expName = $_POST["expName"];
            $expJson = $_POST["expJson"];
            $expDesc = $_POST["expDesc"];
            $expId = substr(md5(rand()), 0, 7);

            // first step get all nodes
            $nodeNum = 0;
            $json = json_decode($expJson);
            $nodeNameToId = Array();

            $myExpJsonArray = Array();
            $myExpNodeArray = Array();
            $myExpEdgeArray = Array();

            // get all nodes
            foreach($json->nodeDataArray as $item) {
                $nodeName = $item->key;
                $nodeNameToId[$nodeName] = $nodeNum;
                $nodeNum += 1;

                $nodeItemId = $item->itemId;
                $nodeType = $item->nodeType;

                $tempNewNode = Array();
                $tempNewNode["itemId"] = $nodeItemId;
                $tempNewNode["nodeType"] = $nodeType;

                // dump parameters
                if($nodeType == "Module") {
                    $tempNewNode["parameters"] = $item->parameters;
                }
                $myExpNodeArray[] = $tempNewNode;
            }

            // get all edges
            foreach($json->linkDataArray as $edge) {
                $fromId = intval($nodeNameToId[$edge->from]);
                $toId   = intval($nodeNameToId[$edge->to]);
                $fromPortId = intval(explode('_',$edge->fromPort)[1]);
                $toPortId = intval(explode('_',$edge->toPort)[1]);

                $edgeStr = $fromId. "_" . $fromPortId . ":" . $toId. "_" . $toPortId;
                $myExpEdgeArray[] = $edgeStr;
            }

            // generate full exp json array
            $myExpJsonArray["name"] = $expName;
            $myExpJsonArray["userId"] = $_SESSION["user"];
            date_default_timezone_set('America/Los_Angeles');
            $myExpJsonArray["createTime"] = date('Y-m-d H:i:s');
            $myExpJsonArray["description"] = $expDesc;
            $myExpJsonArray["nodesArray"] = $myExpNodeArray;
            $myExpJsonArray["edgesArray"] = $myExpEdgeArray;

            $myExpJsonStr = json_encode($myExpJsonArray, JSON_PRETTY_PRINT);

            // create folder to save exp visual file
            $folderPath = $uploadExpDir . DIRECTORY_SEPARATOR . $expId;
            echo "Create folder:" . $folderPath . "<br><br>";
            $oldmask = umask(0);
            mkdir($folderPath, 0777);
            umask($oldmask);

            $expVisFileName = $expId . "_vis.json";
            $fullFilePath = $folderPath . DIRECTORY_SEPARATOR . $expVisFileName;
            $fileHandle = fopen($fullFilePath, "w") or die('Cannot open file:  '.$fullFilePath);
            fwrite($fileHandle, $expJson);
            fclose($fileHandle);
            // save exp json as visual file
            echo "Save exp vis file in json format to" . $fullFilePath . "<br><br>";

            // insert the new exp into DB
            $sqlStr = sprintf("insert into PendingItem values('%s', 'Exp', '%s', '%s', '%s', 'New', '')"
                ,$expId, $expId, $expVisFileName,$myExpJsonStr);

            echo $sqlStr . "<br><br>";

            $con=getDBConnection();
            $sqlStr = "insert into PendingItem values(?, 'Exp', ?, ?, ?, 'New', '')";
            $stmt = $con->prepare($sqlStr);
            $stmt->bind_param("ssss", $expId, $expId, $expVisFileName,$myExpJsonStr);
            $stmt->execute();
            $results = $stmt->get_result();

            echo "<font color='green'>New Exp upload succeeded!</font><br><br>";
        } catch (Exception $e) {
            echo "<font color='red'>New Exp upload failed!</font><br>";
            echo 'Caught exception: ' .  $e->getMessage(). "<br><br>";
        }

    }
    else {
        echo "<font color='red'>New Exp upload failed!</font><br><br>";
    }

    echo "The brower will go back to current experiment page in 3 seconds.<br>";
    echo "<a href=\"expList.php\">GO BACK</a>";
    echo '<META HTTP-EQUIV=Refresh CONTENT="3; URL='. "expList.php" . '">';
?>