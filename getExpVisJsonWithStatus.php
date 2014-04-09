<?php
    include "dbConfig.php";
    include "fileLocationConfig.php";

    if(isset($_GET["expId"])) {
        $expId = $_GET["expId"];
    } else {
        exit();
    }
    //Step 1: Check if we have exp vis json file
    $visJsonFileName = $expId . "_vis.json";
    $fullVisFilePath = $expDir . DIRECTORY_SEPARATOR . $expId .DIRECTORY_SEPARATOR . $visJsonFileName;
    //echo $fullVisFilePath . "<br>";
    $visJsonStr = file_get_contents($fullVisFilePath);

    //echo "<pre>" . $visJsonStr . "</pre>";

    // get all node status from DB by expId
    $nodeStatus = Array();

    $con=getDBConnection();
    $sqlStr = "select NodeStatus from Node where ExpId = ? order by NodeId";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("s", $expId);
    $stmt->execute();
    $results = $stmt->get_result();

    while($row = mysqli_fetch_row($results)) {
        $nodeStatus[] = $row[0];
    }
    $p = 0;
    $jsonObj = json_decode($visJsonStr);
    foreach($jsonObj->nodeDataArray as $item) {
        // put node Status into the json
        $item->nodeStatus = $nodeStatus[$p];
        $item->nodeId = strval($p);
        if($item->nodeType=="Module" && $nodeStatus[$p]!="New") {
            $item->stdOutLink = "showStdOutput.php?expId=" . $expId . "&nodeId=" .$p;
        }
        if($item->nodeType=="Data") {
            $item->infoLink = "showItemInfo.php?itemType=Data&itemId=" . $item->dataArray->dataId;
        } else {
            $item->infoLink = "showItemInfo.php?itemType=Module&itemId=" . $item->dataArray->moduleId . "&paramStr=" . $item->parameters;
        }
        if($nodeStatus[$p]=="Finished") {
            // put download link for port
            $pn = 1;
            foreach($item->bottomArray as $port) {
                $port->myPortId = strval($pn);
                $linkStr = "downloadFile.php?expId=" . $expId . "&nodeId=" .$p . "&portId=" . $pn;
                $saveResultLink = "newDataFromOutput.php?expId=" . $expId . "&nodeId=" .$p . "&portId=" . $pn . "&dataTypeId=" . strval($port->portTypeId);
                $port->downloadLink = $linkStr;
                $port->saveLink = $saveResultLink;
                $port->showLink = getShowLink($expId, $p, $pn, $port->portTypeName);
                $pn++;
            }
        }
        $p++;
    }

    function getShowLink($expId, $nodeId, $portId, $dataType) {
        $linkStr = "showPlainText.php?";
        $linkStr .= "expId=" . $expId . "&nodeId=" .$nodeId . "&portId=" . $portId;
        return $linkStr;
    }

    $newJsonStr = json_encode($jsonObj,JSON_PRETTY_PRINT);
    echo $newJsonStr;
?>