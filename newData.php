<?php
    include 'authCheck.php';
    include 'dbConfig.php';
?>
<html>

<?php
    if(isset($_GET['dataId'])) {
        $dataId = $_GET['dataId'];
        $con=getDBConnection();
        $sqlStr = "select Name, Version, Description, DataTypeId from Data where DataId= ?";
        $stmt = $con->prepare($sqlStr);
        $stmt->bind_param("s", $dataId);
        $stmt->execute();
        $sqlResult = $stmt->get_result();
        $row = mysqli_fetch_row($sqlResult);
        $dataName =$row[0];
        $dataVersion = $row[1];
        $newVersion = strval(floatval($dataVersion) + 0.1);
        $dataDesc = $row[2];
        $dataTypeId = $row[3];
    }
?>

<head>
    <title>Upload New Data</title>
    <link href="form.css" rel="stylesheet">
    <link href="generalBody.css" rel="stylesheet">
</head>
<body>
    <?php
        include "htmlHeader.php";
    ?>
    <h1><?php
            if(isset($dataId)) {
                echo "Update Data: " . $dataName;
            } else {
                echo "Upload New Data";
            }
        ?></h1>
    <HR>
    <script language="JavaScript" type="text/javascript">
        function validateForm() {
            fieldName = ["dataName", "dataVersion", "dataFile"];
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
    <form name="NewData" method="post" action="uploadNewData.php" enctype="multipart/form-data">
       <input type="hidden" name="MAX_FILE_SIZE" value="512000" />
       <FIELDSET>
           <LEGEND>Basic Information</LEGEND>
            <table border="1" class="fullWidth">
            <tr>
                <td style="width: 20%">Data Type:</td>
                <td style="width: 80%">
                    <select name="dataType" class="fullWidth">
                        <?php
                            $sqlStr = "select DataTypeId, Name, Description from DataType";
                            $results = execQuery($sqlStr);
                            while($row = mysqli_fetch_row($results)) {
                                if(isset($dataId) && $row[0]==$dataTypeId) {
                                    echo "<option selected value=\"" . $row[0] . "\">" . $row[1] . ":" . $row[2] . "</option>";
                                }
                                else {
                                    echo "<option value=\"" . $row[0] . "\">" . $row[1] . ":" . $row[2] . "</option>";
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Data Name:</td>
                <td><input type="text" name="dataName" class="fullWidth" <?php if(isset($dataId)) echo "value='". $dataName . "'"; ?>></td>
            </tr>
            <tr>
                <td>Version:</td>
                <td><input type="text" name="dataVersion" <?php if(isset($dataId))
                                                            echo " value='". $newVersion . "'";
                                                            else echo " value='1.0'";
                                                          ?> class="fullWidth"></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td>
                    <textarea name="dataDesc" rows="5" class="fullWidth"><?php if(isset($dataId))
                            echo $dataDesc;
                        else echo "Description of the uploaded Data.";?></textarea>
                </td>
            </tr>
            <tr>
                <td>Upload File:</td>
                <td><input type="file" name="dataFile"></td>
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
    <?php
    include "htmlFooter.php";
    ?>
</body>
</html>