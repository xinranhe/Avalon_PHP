<?php
    include 'dbConfig.php';
    if(isset($_GET['itemType']) && isset($_GET['itemId'])) {
        $itemType = $_GET['itemType'];
        $itemId = $_GET['itemId'];

        // get correspondence between DataTypeId and DataTypeName
        $sqlStr = "select DataTypeId, Name from DataType";
        $results = execQuery($sqlStr);
        $dataTypeId2Name = array();
        while($row = mysqli_fetch_row($results)) {
            $dataTypeId2Name[$row[0]] = $row[1];
        }

        if($itemType == 'Data') {
            $con=getDBConnection();
            $sqlStr = "select Data.Name, Version, DataType.Name, UserName, CreateTime, Data.Description
                       from Data, DataType
                       where Data.DataTypeId = DataType.DataTypeId and DataId = ?";
            $stmt = $con->prepare($sqlStr);
            $stmt->bind_param("s", $itemId);
            $stmt->execute();
            $results = $stmt->get_result();
            $row = mysqli_fetch_row($results);
            $dataJson = array();
            $dataJson['dataName'] = $row[0];
            $dataJson['version'] = $row[1];
            $dataJson['typeName'] = $row[2];
            $dataJson['user'] = $row[3];
            $dataJson['createTime'] = $row[4];
            $dataJson['description'] = $row[5];
            $dataJson['dataId'] = $itemId;
            echo json_encode($dataJson, JSON_PRETTY_PRINT);
        }
        else if($itemType == 'Module') {
            $con=getDBConnection();
            $sqlStr = "select Module.Name, Version, ModuleType.Name, UserName, CreateTime, Module.Description, InputArguments, OutputArguments, ModuleParameters
                       from Module, ModuleType
                       where Module.ModuleTypeId = ModuleType.ModuleTypeId and ModuleId= ? ";
            $stmt = $con->prepare($sqlStr);
            $stmt->bind_param("s", $itemId);
            $stmt->execute();
            $results = $stmt->get_result();
            $row = mysqli_fetch_row($results);
            $dataJson = array();
            $dataJson['moduleName'] = $row[0];
            $dataJson['version'] = $row[1];
            $dataJson['typeName'] = $row[2];
            $dataJson['user'] = $row[3];
            $dataJson['createTime'] = $row[4];
            $dataJson['description'] = $row[5];
            $dataJson['moduleId'] = $itemId;

            $inputArgFields = explode(';',$row[6]);
            $dataJson['inputNum'] = intval($inputArgFields[0]);
            $dataJson['inputTypeName'] = array();
            $dataJson['inputTypeId'] = array();
            $dataJson['inputName'] = array();
            for($i=1;$i<=$dataJson['inputNum'];$i++) {
                $tempFields = explode(':', $inputArgFields[$i]);
                $dataJson['inputName'][] = $tempFields[0];
                $dataJson['inputTypeName'][] = $dataTypeId2Name[intval($tempFields[1])];
                $dataJson['inputTypeId'][] = intval($tempFields[1]);
            }

            $inputArgFields = explode(';',$row[7]);
            $dataJson['outputNum'] = intval($inputArgFields[0]);
            $dataJson['outputTypeName'] = array();
            $dataJson['outputTypeId'] = array();
            $dataJson['outputName'] = array();
            for($i=1;$i<=$dataJson['outputNum'];$i++) {
                $tempFields = explode(':' , $inputArgFields[$i]);
                $dataJson['outputName'][] = $tempFields[0];
                $dataJson['outputTypeName'][] = $dataTypeId2Name[intval($tempFields[1])];
                $dataJson['outputTypeId'][] = intval($tempFields[1]);
            }

            $inputArgFields = explode(';',$row[8]);
            $dataJson['paraNum'] = intval($inputArgFields[0]);
            $dataJson['paraName'] = array();
            for($i=1;$i<=$dataJson['paraNum'];$i++) {
                $tempFields = explode(':', $inputArgFields[$i]);
                $dataJson['paraName'][] = $tempFields[0];
            }

            echo json_encode($dataJson, JSON_PRETTY_PRINT);
        }
    }
?>