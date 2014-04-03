<?php
include 'authCheck.php';
include 'dbConfig.php';
?>

<?php
if(isset($_GET['expId']) && isset($_GET['nodeId']) && isset($_GET['portId']) && isset($_GET['dataTypeId'])) {
    $con=getDBConnection();
    $expId = $_GET['expId'];
    $portId = $_GET['portId'];
    $nodeId = $_GET['nodeId'];

    $sqlStr = "select Name, Description from DataType where DataTypeId= ?";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("s", $_GET['dataTypeId']);
    $stmt->execute();
    $sqlResult = $stmt->get_result();
    $row = mysqli_fetch_row($sqlResult);
    $dataTypeStr = $row[0] . ":" . $row[1];
    $dataVersion = "1.0";
    $dataName = "Temp_Output";
    $dataDesc = "Output from exp:" . $expId . " Node:" . $nodeId . " Port:" . $portId;
}
?>
<html>
<head>
    <title>
        Avalon-Save Output As Data
    </title>
    <link href="form.css" rel="stylesheet" type="text/css">
    <link href="generalBody.css" rel="stylesheet">
    <style>
        .selectedRow {
            background-color: lightskyblue;
        }
        .normalRow {
            background-color: white;
        }
    </style>
</head>
<body>
<script language="JavaScript" type="text/javascript">
    function validateForm() {
        fieldName = ["dataName", "dataVersion"];
        var isValidate = true;
        var returnStr = ''
        for(var i=0;i<fieldName.length;i++) {
            var element = document.getElementsByName(fieldName[i])[0].value;
            if(element==null || element=="") {
                isValidate = false;
                returnStr += fieldName[i] + " CAN NOT BE EMPTY!\n";
            }
        }
        if(!isValidate) {
            alert(returnStr);
            return false;
        } else {
            return true;
        }
    }
</script>
<form name="NewDataFromOutput" method="post" action="uploadNewDataFromOutput.php">
    <input type="hidden" name="expId" value="<?php echo $expId;?>" />
    <input type="hidden" name="nodeId" value="<?php echo $nodeId;?>" />
    <input type="hidden" name="portId" value="<?php echo $portId;?>" />
    <input type="hidden" name="dataType" value="<?php echo $_GET['dataTypeId'];?>" />
    <FIELDSET>
        <LEGEND>Basic Information</LEGEND>
        <table border="1" class="fullWidth">
            <tr>
                <td style="width: 20%">Data Type:</td>
                <td style="width: 80%">
                    <?php
                        echo $dataTypeStr;
                    ?>
                </td>
            </tr>
            <tr>
                <td>Data Name:</td>
                <td><input type="text" name="dataName" class="fullWidth" <?php echo "value='". $dataName . "'"; ?>></td>
            </tr>
            <tr>
                <td>Version:</td>
                <td><input type="text" name="dataVersion" value='1.0' class="fullWidth"></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>
                    <textarea name="dataDesc" rows="5" class="fullWidth"><?php echo $dataDesc;?></textarea>
                </td>
            </tr>
        </table>
    </FIELDSET>
    <FIELDSET>
        <LEGEND>Submit</LEGEND>
        <table class="fullWidth">
            <tr>
                <td style="width: 10%"></td>
                <td style="width: 35%"><input type="submit" value="Upload" style="height:30px; width:100%;" onclick="return validateForm();"></td>
                <td style="width: 10%"></td>
                <td style="width: 35%"><input type="reset" value="Reset" style="height:30px; width:100%;"></td>
                <td style="width: 10%"></td>
            </tr>
        </table>
    </FIELDSET>
</form>
</body>