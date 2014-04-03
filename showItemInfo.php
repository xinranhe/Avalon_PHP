<?php
include 'authCheck.php';
include 'dbConfig.php';
?>

<?php
    if($_GET['itemId'] && $_GET['itemType']) {
        $itemId = $_GET['itemId'];
        $itemType = $_GET['itemType'];
        if($itemType=="Module") {
            if(isset($_GET['paramStr'])) {
                $paramStr = $_GET['paramStr'];
            }
            else {
                exit;
            }
        }
    } else {
        exit;
    }
?>


<html>
<head>
    <title><?php echo $itemType; ?></title>
    <link href="form.css" type="text/css" rel="stylesheet">
    <link href="generalBody.css" rel="stylesheet">
    <link href="table.css" rel="stylesheet">
</head>
<body>

<?php if($itemType=="Module"): ?>

<h1>Module Info</h1>
<HR>
<table border="1" class="fullWidth">
    <?php
    $con=getDBConnection();
    $sqlStr = "select Module.Name, ModuleType.Name, Version, CreateTime , UserName, Module.Description, "
        ."InputArguments, OutputArguments, ModuleParameters, ModuleId from Module, ModuleType where Module.ModuleTypeId = ModuleType.ModuleTypeId and Module.ModuleId = ?";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("s", $itemId);
    $stmt->execute();
    $results = $stmt->get_result();
    $row = mysqli_fetch_row($results);

    function getArgSelectionFromString($ioString) {
        $resultStr = '';
        $sqlStr = "select DataTypeId, Name from DataType";
        $results = execQuery($sqlStr);
        $id2name = Array();
        while($row = mysqli_fetch_row($results)) {
            $id2name[$row[0]] = $row[1];
        }
        $fields = explode(';', $ioString);
        $argNum = intval($fields[0]);
        if($argNum==0) {
            return "No arguments";
        }
        $resultStr .= "<select class='fullWidth' size='5'>";
        for($i=1;$i<=$argNum;$i++) {
            $tempFileds = explode(':', $fields[$i]);
            $name = $tempFileds[0];
            $typeName = $id2name[intval($tempFileds[1])];
            $resultStr .= "<option>" . $name . ':' . $typeName . "</option>";
        }
        $resultStr .= "</select>";
        return $resultStr;
    }
    function getParamsSelectionFromString($paraString, $inputParams) {
        $resultStr = '';
        $fields = explode(';', $paraString);
        $truefields = explode(';', $inputParams);
        $argNum = intval($fields[0]);
        if($argNum==0) {
            return "No parameters";
        }
        $resultStr .= "<select class='fullWidth' size='5'>";
        for($i=1;$i<=$argNum;$i++) {
            $resultStr .= "<option>" . $fields[$i] . ": " . $truefields[$i] . "</option>";
        }
        $resultStr .= "</select>";
        return $resultStr;
    }

    echo "<tr><td style='width: 20%;'>Name</td>";
    echo "<td style='width: 80%;'>" . $row[0] . "</td></tr>";

    echo "<tr><td>Parameters</td>";
    echo "<td>" . getParamsSelectionFromString($row[8], $paramStr) . "</td></tr>";

    echo "<tr><td>Module Type</td>";
    echo "<td>" . $row[1] . "</td></tr>";

    echo "<tr><td>Version</td>";
    echo "<td>" . $row[2] . "</td></tr>";

    echo "<tr><td>Create Time</td>";
    echo "<td>" . $row[3] . "</td></tr>";

    echo "<tr><td>User Name</td>";
    echo "<td>" . $row[4] . "</td></tr>";

    echo "<tr><td>Description</td>";
    echo "<td><textarea class='fullWidth' rows='5'>" . $row[5] . "</textarea></td></tr>";

    echo "<tr><td>Input Arguments</td>";
    echo "<td>" . getArgSelectionFromString($row[6]) . "</td></tr>";

    echo "<tr><td>Output Arguments</td>";
    echo "<td>" . getArgSelectionFromString($row[7]) . "</td></tr>";
    ?>
</table>

<?php else: ?>
    <h1>Data Info</h1>
    <HR>
    <table border="1" class="fullWidth">
        <?php
        $con=getDBConnection();
        $sqlStr = "select Data.Name, DataType.Name, Version, CreateTime, UserName, Data.Description, DataId from Data, DataType where Data.DataTypeId = DataType.DataTypeId and DataId = ?";
        $stmt = $con->prepare($sqlStr);
        $stmt->bind_param("s", $itemId);
        $stmt->execute();
        $results = $stmt->get_result();
        $row = mysqli_fetch_row($results);
        echo "<tr><td style='width: 20%;'>Name</td>";
        echo "<td style='width: 80%;'>" . $row[0] . "</td></tr>";
        echo "<tr><td>Data Type</td>";
        echo "<td>" . $row[1] . "</td></tr>";
        echo "<tr><td>Version</td>";
        echo "<td>" . $row[2] . "</td></tr>";
        echo "<tr><td>Create Time</td>";
        echo "<td>" . $row[3] . "</td></tr>";
        echo "<tr><td>User Name</td>";
        echo "<td>" . $row[4] . "</td></tr>";
        echo "<tr><td>Description</td>";
        echo "<td><textarea class='fullWidth' rows='5'>" . $row[5] . "</textarea></td></tr>";
        ?>
    </table>

<?php endif; ?>
</body>
</html>