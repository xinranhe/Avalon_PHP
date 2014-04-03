<?php
include 'authCheck.php';
include 'userConfig.php';
?>
<html>
<head>
    <title>Create New DataType</title>
    <link href="form.css" type="text/css" rel="stylesheet">
    <link href="generalBody.css" rel="stylesheet">
    <link href="table.css" rel="stylesheet">

    <script src="TableFilter/tablefilter_all_min.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/sortabletable.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/tfAdapter.sortabletable.js" language="javascript" type="text/javascript"></script>
</head>
<body>
<?php
include "htmlHeader.php";
?>
<h1>Manage Data Type</h1>
<HR>
<script language="JavaScript" type="text/javascript">
    function validateForm() {
        fieldName = ["dataTypeName"];
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
<form name="NewData" method="get" action="createDataType.php">
    <FIELDSET>
        <LEGEND>Current DataTypes</LEGEND>
        <table class="full" id="currentDataType" cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th>DataType Id</th>
                <th>DataType Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            include 'dbConfig.php';
            $sqlStr = "select * from DataType";
            $results = execQuery($sqlStr);
            while($row = mysqli_fetch_row($results)) {
                echo "<tr class='New'>";
                echo "<td>" . $row[0] . "</td>";
                echo "<td>" . $row[1] . "</td>";
                echo "<td>" . $row[2] . "</td>";
                echo "<td>";
                if(isAdmin()) {
                   echo "<a href='deleteDataType.php?dataTypeId=". $row[0] . "'><font color='red'>Delete</font></a>";
                }
                echo "</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </FIELDSET>
    <FIELDSET>
        <LEGEND>Create New Data Type</LEGEND>
        <table border="1" class="fullWidth">
            <tr>
                <td style="width: 20%">Data Type Name:</td>
                <td style="width: 80%"><input type="text" name="dataTypeName" class="fullWidth"></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td rowspan="2">
                    <textarea name="dataTypeDesc" rows="5" class="fullWidth">Description of the new DataType.</textarea>
                </td>
            </tr>
            <tr>
                <td><input type="submit" value="Create New DataType" style="height:30px; width:100%;" onclick="return validateForm();"></td>
            </tr>
        </table>
    </FIELDSET>
</form>
<script language="javascript" type="text/javascript">
    var props = {
        sort: true,
        filters_row_index:1,
        paging: true,
        paging_length: 10,
        results_per_page: ['# rows per page',[10,20,50]],
        col_0: "none",
        col_3: "none",
        rows_counter: true,
        rows_counter_text: "Rows:",
        btn_reset: true,
        btn_next_page_html: '<a href="javascript:;" style="margin:3px;">Next ></a>',
        btn_prev_page_html: '<a href="javascript:;" style="margin:3px;">< Previous</a>',
        btn_last_page_html: '<a href="javascript:;" style="margin:3px;"> Last >|</a>',
        btn_first_page_html: '<a href="javascript:;" style="margin:3px;"><| First</a>',
        loader: true,
        loader_html: '<h4 style="color:green;">Loading, please wait...</h4>'
    };
    var tf1 = setFilterGrid("currentDataType", props);
</script>
<?php
include "htmlFooter.php";
?>
</body>
</html>