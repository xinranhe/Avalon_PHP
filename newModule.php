<?php
    include 'authCheckAdmin.php';
    include 'dbConfig.php';
?>
<?php
if(isset($_GET['moduleId'])) {
    $moduleId = $_GET['moduleId'];
    $con=getDBConnection();
    $sqlStr = "select Name,Version,Description,ModuleTypeId,InputArguments,OutputArguments,ModuleParameters from Module where ModuleId= ?";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("s", $moduleId);
    $stmt->execute();
    $sqlResult = $stmt->get_result();
    $row = mysqli_fetch_row($sqlResult);
    $moduleName =$row[0];
    $moduleVersion = $row[1];
    $newVersion = strval(floatval($moduleVersion) + 0.1);
    $moduleDesc = $row[2];
    $moduleTypeId = $row[3];
    $inputStr = $row[4];
    $outputStr = $row[5];
    $paramStr = $row[6];

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
            return "";
        }
        for($i=1;$i<=$argNum;$i++) {
            $tempFileds = explode(':', $fields[$i]);
            $name = $tempFileds[0];
            $typeName = $id2name[intval($tempFileds[1])];
            $resultStr .= "<option>" . $name . ':' . $typeName . "</option>";
        }
        return $resultStr;
    }
    function getParamsSelectionFromString($paraString) {
        $resultStr = '';
        $fields = explode(';', $paraString);
        $argNum = intval($fields[0]);
        if($argNum==0) {
            return "";
        }
        for($i=1;$i<=$argNum;$i++) {
            $resultStr .= "<option>" . $fields[$i] . "</option>";
        }
        return $resultStr;
    }
}
?>

<html>
<head>
    <title>Avalon-Upload New Module</title>
    <style>
        .full {width:100%;}
        .wrapper{
            border: 1px dashed red;
            height: 150px;
            overflow-x: scroll;
            overflow-y: scroll;
            width: 150px;
        }
        .wrapper:selection{
            width:150px;
            border:1px solid #ccc
        }
    </style>
    <link href="form.css" rel="stylesheet" type="text/css">
    <link href="generalBody.css" rel="stylesheet">
</head>
<body>
<?php
    include "htmlHeader.php";
?>
<h1><?php
    if(isset($moduleId)) {
        echo "Update Module: " . $moduleName;
    }
    else {
        echo "Upload New Module";
    }
?></h1><hr>
<script language="JavaScript" type="text/javascript">
    function validateForm() {
        fieldName = ["moduleName", "moduleVersion", "moduleMainFile"];
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
<form method="post" name="updateModule" action="uploadNewModule.php" enctype="multipart/form-data">
    <?php
        function showDataType() {
            $sqlStr = "select DataTypeId, Name, Description from DataType";
            $results = execQuery($sqlStr);
            while($row = mysqli_fetch_row($results)) {
                if(isset($moduleId) && $row[0]==$moduleTypeId) {
                    echo "<option selected value=\"" . $row[0] . "\">" . $row[1] . "</option>";
                } else {
                    echo "<option value=\"" . $row[0] . "\">" . $row[1] . "</option>";
                }
            }
        }
    ?>
    <FIELDSET>
        <legend>Basic Information</legend>
        <table border="1" class="fullWidth">
            <tr>
                <td style="width: 20%">Module Type:</td>
                <td style="width: 80%">
                    <select name="moduleType" class="fullWidth">
                        <?php
                        $sqlStr = "select ModuleTypeId, Name, Description from ModuleType";
                        $results = execQuery($sqlStr);
                        while($row = mysqli_fetch_row($results)) {
                            echo "<option value=\"" . $row[0] . "\">" . $row[1] . ":" . $row[2] . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Module Name:</td>
                <td><input type="text" name="moduleName" class="fullWidth" <?php if(isset($moduleId)) echo "value='". $moduleName . "'"; ?>></td>
            </tr>
            <tr>
                <td>Version:</td>
                <td><input type="text" name="moduleVersion" <?php if(isset($moduleId))
                        echo " value='". $newVersion . "'";
                    else echo " value='1.0'";
                    ?> class="fullWidth"></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>
                    <textarea name="moduleDesc" rows="5" class="fullWidth"><?php if(isset($moduleId))
                            echo $moduleDesc;
                        else echo "Description of the uploaded Module.";?></textarea>
                </td>
            </tr>
        </table>
    </FIELDSET>
    <FIELDSET>
        <legend>Arguments and Parameters</legend>
    <table style="width: 100%;"><tr>
    <td style="width: 33%"><FIELDSET>
        <legend>Input Arguments</legend>
        <table>
            <tr>
                <td rowspan="3" style="width: 45%">
                        <select id="inputArguSelect" style="width: 100%; overflow-y: scroll;" size="5">
                            <?php
                                if(isset($moduleId)) {
                                    echo getArgSelectionFromString($inputStr);
                                }
                            ?>
                        </select>
                </td>
                <td style="width: 10%">
                    Name:
                </td>
                <td style="width: 45%">
                    <input type="text" id="inputArguName" class="fullWidth">
                </td>
            </tr>
            <tr>
                <td>
                    Type:
                </td>
                <td>
                    <select id="inputArguType" class="fullWidth">
                        <?php
                            showDataType();
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="button" value="Add" onclick="addNewArgument('input')" style="width: 40%">
                    <input type="button" value="Delete" onclick="deleteSelectedOptionById('inputArguSelect')" style="width: 40%">
                </td>
            </tr>
        </table>
    </FIELDSET></td>

    <td style="width: 33%">
    <FIELDSET>
        <legend>Output Arguments</legend>
        <table>
            <tr>
                <td rowspan="3" style="width: 45%">
                    <select id="outputArguSelect" style="width: 100%; overflow-y: scroll;" size="5">
                        <?php
                        if(isset($moduleId)) {
                            echo getArgSelectionFromString($outputStr);
                        }
                        ?>
                    </select>
                </td>
                <td style="width: 10%">
                    Name:
                </td>
                <td style="width: 45%">
                    <input type="text" id="outputArguName" class="fullWidth">
                </td>
            </tr>
            <tr>
                <td>
                    Type:
                </td>
                <td>
                    <select id="outputArguType" class="fullWidth">
                        <?php
                            showDataType();
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>

                </td>
                <td>
                    <input type="button" value="Add" onclick="addNewArgument('output')" style="width: 40%">
                    <input type="button" value="Delete" onclick="deleteSelectedOptionById('outputArguSelect')" style="width: 40%">
                </td>
            </tr>
        </table>
    </FIELDSET></td>
     <td style="width: 33%"><FIELDSET>
            <legend>Module Parameters</legend>
             <table>
                 <tr>
                     <td rowspan="3" style="width: 45%">
                         <select id="paraSelect" style="width: 100%; overflow-y: scroll;" size="5">
                             <?php
                             if(isset($moduleId)) {
                                 echo getParamsSelectionFromString($paramStr);
                             }
                             ?>
                         </select>
                     </td>
                     <td style="width: 10%">
                         Name:
                     </td>
                     <td style="width: 45%">
                         <input type="text" id="paraName" class="fullWidth">
                     </td>
                 </tr>
                 <tr>
                     <td>
                     </td>
                     <td>
                         <input type="button" value="Add" onclick="addNewParameter()" style="width: 40%">
                         <input type="button" value="Delete" onclick="deleteSelectedOptionById('paraSelect')" style="width: 40%">
                     </td>
                 </tr>
             </table>
     </FIELDSET></td>
    </table>
    </FIELDSET>
    <FIELDSET>
        <legend>Upload Files</legend>
        <table style="width: 100%">
            <tr>
                <td>Main File:</td>
                <td><input type="file" name="moduleMainFile"></td>
            </tr>
            <tr>
                <td>Other Files:</td>
                <td><input type="file" name="moduleOtherFiles[]" multiple></td>
            </tr>
        </table>
    </FIELDSET>
    <FIELDSET>
        <LEGEND>Submit</LEGEND>
        <table class="fullWidth">
            <tr>
                <td style="width: 10%"></td>
                <td style="width: 35%"><input type="submit" value="Upload" onclick="return generateArgumentString();" style="height:30px; width:100%;"></td>
                <td style="width: 10%"></td>
                <td style="width: 35%"><input type="reset" value="Reset" style="height:30px; width:100%;"></td>
                <td style="width: 10%"></td>
            </tr>
        </table>
    </FIELDSET>
    <input type="hidden" id="inputArg" name="inputArg" value="" />
    <input type="hidden" id="outputArg" name="outputArg" value="" />
    <input type="hidden" id="paraArg" name="paraArg" value="" />
 </form>
<script language="javascript">
    function addNewArgument(nameStr) {
        var typeIdStr = nameStr + "ArguType";
        var nameIdStr = nameStr + "ArguName";
        var selectIdStr = nameStr + "ArguSelect";

        var e = document.getElementById(typeIdStr);
        var tempInputTypeId = e.options[e.selectedIndex].value;
        var tempInputTypeName = e.options[e.selectedIndex].text;

        var tempInputName = document.getElementById(nameIdStr).value;

        var inputArgus = document.getElementById(selectIdStr);
        inputArgus.options[inputArgus.options.length] = new Option(tempInputName + ':' + tempInputTypeName,
            tempInputName + ':' + tempInputTypeId + ';');
    }
    function addNewParameter() {
        var tempParaName = document.getElementById("paraName").value;
        var inputArgus = document.getElementById("paraSelect");
        inputArgus.options[inputArgus.options.length] = new Option(tempParaName ,
            tempParaName + ';');
    }
    function deleteSelectedOptionById(selectId) {
        var e = document.getElementById(selectId);
        var selectIndex = e.selectedIndex;
        e.removeChild(e[selectIndex]);
    }
    function generateArgumentString()
    {
        if(!validateForm()) {
            return false;
        }
        var inputArgOptions = document.getElementById("inputArguSelect");
        var outputArgOptions = document.getElementById("outputArguSelect");
        var paraOptions = document.getElementById("paraSelect");

        var inputArguStr = inputArgOptions.length.toString() + ';';
        for(i=0; i<inputArgOptions.length; i++) {
            inputArguStr += inputArgOptions[i].value;
        }

        var outputArgStr = outputArgOptions.length.toString() + ';';
        for(i=0;i<outputArgOptions.length;i++) {
            outputArgStr += outputArgOptions[i].value;
        }

        var paraStr = paraOptions.length.toString() + ';';
        for(i=0; i< paraOptions.length; i++) {
            paraStr += paraOptions[i].value;
        }
        // write the result to hidden
        document.getElementById('inputArg').value = inputArguStr;
        document.getElementById('outputArg').value = outputArgStr;
        document.getElementById('paraArg').value = paraStr;
        return true;
    }
</script>
<?php
include "htmlFooter.php";
?>
</body>
</html>