<?php
    include 'authCheck.php';
    include 'dbConfig.php';
?>

<?php
if(isset($_GET['expId'])) {
    $expId = $_GET['expId'];
    $con=getDBConnection();
    $sqlStr = "select Name, Description from Experiment where ExpId= ?";
    $stmt = $con->prepare($sqlStr);
    $stmt->bind_param("s", $expId);
    $stmt->execute();
    $sqlResult = $stmt->get_result();
    $row = mysqli_fetch_row($sqlResult);
    $expName =$row[0];
    $expDesc = $row[1];
}
?>

<html>
<head>
    <title>
        Avalon-Create New Experiment
    </title>
    <!-- Include the GoJS library. -->
    <script language="javascript" src="go.js"></script>
    <!-- Include the filter table. -->
    <script src="TableFilter/tablefilter_all_min.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/sortabletable.js" language="javascript" type="text/javascript"></script>
    <script src="TableFilter/tfAdapter.sortabletable.js" language="javascript" type="text/javascript"></script>
    <link href="form.css" rel="stylesheet" type="text/css">
    <link href="table.css" rel="stylesheet">
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
<script language="JavaScript" type="text/javascript">
    function validateForm() {
        fieldName = ["expName"];
        // TODO: Check the validity of the experiment
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
<body<?php
        if(isset($expId)) {
            echo ' onload="loadAndGetExpVis(\'' . $expId . "')\"";
        }
    ?>>
    <?php
        include "htmlHeader.php";
    ?>
    <H1>
        <?php
            if(isset($expId)) {
                echo "Clone Exp: " . $expName;
            }
            else {
                echo "Create New Experiment";
            }
        ?>
    </H1><HR/>
    <!--Main table: separate the reset of the page into three part-->
    <table style="width: 100%; height: 100%">
        <!--First Row: Form for exp general information-->
        <tr><td style="height: 10%">
                <form method="post" action="uploadNewExp.php">
                    <input type="hidden" name="expJson" id="expJson" value="">
                    <fieldset><legend>Exp Info</legend>
                        <table>
                            <tr>
                                <td style="width: 5%">Exp Name:</td>
                                <td style="width: 10%"><input type="text" name="expName" class="fullWidth"
                                        <?php if(isset($expId)) echo " value='" . $expName . " (cloned)'"; ?>
                                ></td>
                                <td style="width: 2%"></td>
                                <td style="width: 5%">Description:</td>
                                <td style="width: 20%"><textarea rows="4" class="fullWidth" name="expDesc" class="fullWidth"><?php
                                        if(isset($expId))
                                        {
                                            echo $expDesc .  "    (This experiment is cloned from exp:" . $expId . ")";
                                        }
                                        else {
                                            echo "This is the description of the experiment";
                                        }
                                    ?></textarea></td>
                                <td style="width: 2%"></td>
                                <td style="width: 10%"><input type="submit" value="Submit Experiment" onclick="return prepareForExpSubmit();" style="height:30px; width:100%;"></td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
        </td></tr>
        <!--Second Row: Drawing area and item info panel-->
        <tr><td style="height: 70%">
            <table class="fullBoth"><tr>
                <td style="width: 80%">
                   <div id="myDiagramDiv" style="width:98%; height:100%; border-style:solid; border-width:5px;"></div>
                </td>
                <td style="width: 18%">
                    <fieldset style="height: 100%"><legend>Item Info</legend>
                        <div id="itemInfoField">
                            Information for selected Item.
                        </div>
                    </fieldset>
                </td>
            </tr></table>
        </td></tr>
        <!--Third Row: Current Data and Module-->
        <tr><td style="height: 20%">
            <table class="fullBoth"><tr>
                    <td style="width: 50%; height: 100%">
                        <fieldset><legend>Data</legend>
                        <table style="width:100%; height: 100%" id="dataListTable" cellpadding="0" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Version</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>Create Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sqlStr = "select Data.Name, Version, DataType.Name, UserName, CreateTime, DataId from Data, DataType where Data.DataTypeId = DataType.DataTypeId";
                            $results = execQuery($sqlStr);
                            while($row = mysqli_fetch_row($results)) {
                                echo "<tr class='New' onclick=\"dataRowClick('" . $row[5] . "', this)\">";
                                echo "<td>" . $row[0] . "</td>";
                                echo "<td>" . $row[1] . "</td>";
                                echo "<td>" . $row[2] . "</td>";
                                echo "<td>" . $row[3] . "</td>";
                                echo "<td>" . $row[4] . "</td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table></fieldset>
                    </td>
                    <td style="width: 50%; height: 100%">
                        <fieldset><legend>Module</legend>
                        <table style="width:100%; height: 100%" id="moduleListTable" cellpadding="0" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Version</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>Create Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sqlStr = "select Module.Name, Version, ModuleType.Name, UserName, CreateTime, ModuleId from Module, ModuleType where Module.ModuleTypeId = ModuleType.ModuleTypeId";
                            $results = execQuery($sqlStr);
                            while($row = mysqli_fetch_row($results)) {
                                echo "<tr class='New' onclick=\"moduleRowClick('" . $row[5] . "', this)\">";
                                echo "<td>" . $row[0] . "</td>";
                                echo "<td>" . $row[1] . "</td>";
                                echo "<td>" . $row[2] . "</td>";
                                echo "<td>" . $row[3] . "</td>";
                                echo "<td>" . $row[4] . "</td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table></fieldset>
                    </td>
            </tr></table>
        </td></tr>
    </table>
    <script language="JavaScript">
        var $ = go.GraphObject.make;  //for conciseness in defining node templates

        myDiagram =
            $(go.Diagram, "myDiagramDiv",  //Diagram refers to its DIV HTML element by id
                {
                    initialContentAlignment: go.Spot.Center
                });
        var portSize = 8;

        myDiagram.nodeTemplate =
            $(go.Node, "Table",
                { locationObjectName: "BODY",
                    locationSpot: go.Spot.Center,
                    selectionObjectName: "BODY"
                },
                new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),

                // the body
                $(go.Panel, "Auto",
                    { row: 1, column: 1, name: "BODY",
                        stretch: go.GraphObject.Fill },
                    $(go.Shape, "Rectangle",
                        { fill: "#BFBFBF",
                            minSize: new go.Size(156, 56) }),
                    $(go.TextBlock,
                        { margin: 10, textAlign: "center" },
                        new go.Binding("text", "itemName"))
                ),  // end Auto Panel body

                // the Panel holding the top port elements, which are themselves Panels,
                // created for each item in the itemArray, bound to data.topArray
                $(go.Panel, "Horizontal",
                    { row: 0, column: 1,
                        itemTemplate:
                            $(go.Panel, "Vertical",
                                { _side: "top",
                                    fromSpot: go.Spot.Top, toSpot: go.Spot.Top,
                                    fromLinkable: false, toLinkable: true, toMaxLinks: 1, cursor: "pointer"
                                    },
                                new go.Binding("portId", "portId"),
                                $(go.TextBlock,
                                    {textAlign: "center",
                                        text: "In",
                                        margin: 2},
                                    new go.Binding("text", "portTypeName")
                                ),

                                $(go.Shape, "Rectangle",
                                    { stroke: null,
                                        fill : "Blue",
                                        desiredSize: new go.Size(portSize, portSize),
                                        margin: new go.Margin(0, 1)
                                    }
                                )
                            )  // end itemTemplate
                    },
                    new go.Binding("itemArray", "topArray")
                ),  // end Horizontal Panel

                // the Panel holding the bottom port elements, which are themselves Panels,
                // created for each item in the itemArray, bound to data.bottomArray
                $(go.Panel, "Horizontal",
                    { row: 2, column: 1,
                        itemTemplate:
                            $(go.Panel, "Vertical",
                                { _side: "bottom",
                                    fromSpot: go.Spot.Bottom, toSpot: go.Spot.Bottom,
                                    fromLinkable: true, toLinkable: false, cursor: "pointer"
                                    },
                                new go.Binding("portId", "portId"),
                                $(go.Shape, "Rectangle",
                                    { stroke: null,
                                      desiredSize: new go.Size(portSize, portSize),
                                      fill : "Blue",
                                      margin: new go.Margin(0, 1) }
                                 ),

                                $(go.TextBlock,
                                    {textAlign: "center",
                                        text: "Out",
                                        margin: 2},
                                    new go.Binding("text", "portTypeName")
                                )
                            )  // end itemTemplate
                    },
                    new go.Binding("itemArray", "bottomArray")
                )  // end Horizontal Panel
            );  // end Node

        myDiagram.validCycle = go.Diagram.CycleNotDirected;
        // only allow new links between ports of the same color
        myDiagram.toolManager.linkingTool.linkValidation = sameDataType;

        // only allow reconnecting an existing link to a port of the same color
        myDiagram.toolManager.relinkingTool.linkValidation = sameDataType;
        // empty data link model only for debug

        myDiagram.model =
            $(go.GraphLinksModel,
                {
                    linkFromPortIdProperty: "fromPort",  // required information:
                    linkToPortIdProperty: "toPort",      // identifies data property names
                    nodeDataArray:
                        [],
                    linkDataArray: []}
            );

        // set selection handler
        var currentSelectNode = '';
        var infoPanel = document.getElementById("itemInfoField");
        myDiagram.addDiagramListener("ObjectSingleClicked", function (e) {
                    var elem = e.subject.part;
                    if(!(elem instanceof go.Link)) {
                        currentSelectNode = elem
                        if(elem.data.nodeType == "Data") {
                            showNodeDataInfoPanel(elem.data.dataArray);
                        }
                        else if(elem.data.nodeType == "Module") {
                            showNodeModuleInfoPanel(elem.data.dataArray, elem.data.parameters);
                        }
                    }
                }
            )
        function sameDataType(fromnode, fromport, tonode, toport) {
            return fromport.data.portTypeName === toport.data.portTypeName;
        }
        // function to handle new Item adding
        function insertNewItemToExp() {
            myDiagram.startTransaction("Add new Node");
            if(currentItemType == "Data") {
                var newNode = {
                    key: currentDataArray["dataName"],
                    itemName : currentDataArray["dataName"],
                    itemId: currentItemId,
                    dataArray : currentDataArray,
                    nodeType : "Data",
                    loc: "0, 0",
                    topArray: [],
                    bottomArray: []
                };
                var port = {portId: "out_1", portTypeName : currentDataArray["typeName"], portTypeId : -1};
                newNode.bottomArray.push(port);
                myDiagram.model.addNodeData(newNode);
            }
            else if(currentItemType == "Module") {
                // get Current parameters

                var newNode = {
                    key: currentDataArray["moduleName"],
                    itemName: currentDataArray["moduleName"],
                    itemId : currentItemId,
                    nodeType : "Module",
                    dataArray : currentDataArray,
                    parameters : getFilledParamsStr(),
                    loc: "0, 0",
                    topArray: [],
                    bottomArray: []
                };
                for(i=0;i<currentDataArray['inputNum'];i++) {
                    var port = {portId: "in_"+(i+1), portTypeName : currentDataArray["inputTypeName"][i]};
                    newNode.topArray.push(port);
                }
                for(i=0;i<currentDataArray['outputNum'];i++) {
                    var port = {portId: "out_"+(i+1), portTypeName : currentDataArray["outputTypeName"][i], portTypeId : currentDataArray["outputTypeId"][i]};
                    newNode.bottomArray.push(port);
                }
                myDiagram.model.addNodeData(newNode);
            }
            myDiagram.commitTransaction("Add new Node");
        }
        function getFilledParamsStr() {
            var resultStr = currentDataArray['paraNum'] + ';'
            for(i=0;i<currentDataArray['paraNum'];i++) {
                resultStr += document.getElementById("itemPara_"+i).value + ";";
            }
            return resultStr;
        }
        function prepareForExpSubmit() {
            if(!validateForm()) {
                return false;
            }
            var expJson = myDiagram.model.toJson();
            document.getElementById("expJson").value = expJson;
            return true;
        }
    </script>
    <!-- javascript to handle data table row click-->
    <script language="JavaScript">
        // global variables
        var currentItemType = '';
        var currentItemId = '';
        var currentItemRow = '';
        var currentDataArray = '';

        function dataRowClick(dataId, tableRow) {
            document.getElementById("itemInfoField").innerHTML =
                "Loading Data info..."
            updateInfoPanelBySelectedItem("Data", dataId);
            currentItemId = dataId;
            currentItemType = "Data";
            currentItemRow = tableRow;
        }
        function moduleRowClick(moduleId, tableRow) {
            document.getElementById("itemInfoField").innerHTML =
                "Loading Module info..."
            updateInfoPanelBySelectedItem("Module", moduleId);
            currentItemId = moduleId;
            currentItemType = "Module";
            currentItemRow = tableRow;
        }
        function getXMLHttp() {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            return xmlhttp;
        }
        function updateInfoPanelBySelectedItem(itemType, itemId) {
            xmlHttp = getXMLHttp();
            if(itemType=='Data') {
                xmlhttp.onreadystatechange = function()
                {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200)
                    {
                        try
                        {
                            dataArray = JSON.parse(xmlhttp.responseText);
                            currentDataArray = dataArray;
                            showDataInfoInPanel(dataArray);
                        }
                        catch(err) {
                            document.getElementById("itemInfoField").innerHTML =
                                "Error occur in loading data info"
                        }
                    }
                }
            }
            else if(itemType == 'Module')
            {
                xmlhttp.onreadystatechange = function()
                {
                    if (xmlhttp.readyState==4 && xmlhttp.status==200)
                    {
                        try {
                            dataArray = JSON.parse(xmlhttp.responseText);
                            currentDataArray = dataArray;
                            showModuleInfoInPanel(dataArray);
                        }
                        catch(err) {
                            document.getElementById("itemInfoField").innerHTML =
                                "Error occur in loading module info"
                        }
                    }
                }
            }
            xmlhttp.open("GET","getItemJson.php?itemType="+itemType+"&itemId="+itemId,true);
            xmlhttp.send();
        }
        // show data info and module info panel
        function showNodeDataInfoPanel(dataArray) {
            htmlStr = getDataBasicInfoStr(dataArray)
            document.getElementById("itemInfoField").innerHTML = htmlStr
        }
        function showNodeModuleInfoPanel(moduleArray, paramStr) {
            htmlStr = getModuleBasicInfoStr(moduleArray)
            htmlStr += "<table style='width: 100%'><tr><td style='width: 45%'>";
            htmlStr += getInputArgumentFieldsetStr(moduleArray);
            htmlStr += "</td>"
            htmlStr += "<td  style='width: 45%'>"
            htmlStr += getOutputArgumentFieldsetStr(moduleArray);
            htmlStr += "</td></tr></table>"
            htmlStr += getParaFieldsetStr(moduleArray);
            htmlStr += getParaActionFieldsetStr();
            document.getElementById("itemInfoField").innerHTML = htmlStr
            setParamsTextValue(moduleArray, paramStr);
        }
        // use AJAX to show Data Info in Panel
        function getDataBasicInfoStr(dataArray) {
            htmlStr = "<fieldset><legend>Basic Info</legend><table border='1' style='width: 100%'>";
            htmlStr += "<tr><td>Type:</td>"
            htmlStr += "<td>" + dataArray["typeName"] + "</td></tr>";
            htmlStr += "<tr><td>Name:</td>"
            htmlStr += "<td>" + dataArray["dataName"] + "</td></tr>";
            htmlStr += "<tr><td>Uploader:</td>"
            htmlStr += "<td>" + dataArray["user"] + "</td></tr>";
            htmlStr += "<tr><td>Create Time:</td>"
            htmlStr += "<td>" + dataArray["createTime"] + "</td></tr>";
            htmlStr += "<tr><td>Description:</td>"
            htmlStr += "<td><textarea readonly rows='5' class='notEditable'>" + dataArray["description"] + "</textarea></td></tr>"
            htmlStr += "</table></fieldset>"
            return htmlStr
        }
        function getModuleBasicInfoStr(moduleArray) {
            htmlStr = "<fieldset><legend>Basic Info</legend><table border='1' style='width: 100%'>";
            htmlStr += "<tr><td>Type:</td>"
            htmlStr += "<td>" + moduleArray["typeName"] + "</td></tr>";
            htmlStr += "<tr><td>Name:</td>"
            htmlStr += "<td>" + moduleArray["moduleName"] + "</td></tr>";
            htmlStr += "<tr><td>Uploader:</td>"
            htmlStr += "<td>" + moduleArray["user"] + "</td></tr>";
            htmlStr += "<tr><td>Create Time:</td>"
            htmlStr += "<td>" + moduleArray["createTime"] + "</td></tr>";
            htmlStr += "<tr><td>Description:</td>"
            htmlStr += "<td><textarea readonly rows='5' class='notEditable'>" + moduleArray["description"] + "</textarea></td></tr>"
            htmlStr += "</table></fieldset>"
            return htmlStr
        }
        function showDataInfoInPanel(dataArray) {
            htmlStr = getDataBasicInfoStr(dataArray)
            htmlStr += getActionFieldsetStr();
            document.getElementById("itemInfoField").innerHTML = htmlStr
        }
        function showModuleInfoInPanel(moduleArray) {
            htmlStr = getModuleBasicInfoStr(moduleArray)
            htmlStr += "<table style='width: 100%'><tr><td style='width: 45%'>";
            htmlStr += getInputArgumentFieldsetStr(moduleArray);
            htmlStr += "</td>"
            htmlStr += "<td  style='width: 45%'>"
            htmlStr += getOutputArgumentFieldsetStr(moduleArray);
            htmlStr += "</td></tr></table>"
            htmlStr += getParaFieldsetStr(moduleArray);
            htmlStr += getActionFieldsetStr();
            document.getElementById("itemInfoField").innerHTML = htmlStr
        }
        function getInputArgumentFieldsetStr(moduleArray) {
            htmlStr = "<fieldset><legend>Input</legend>";
            htmlStr += "<select size='3' class='fullWidth'>"
            for(i=0;i<moduleArray['inputNum'];i++)
            {
                htmlStr += "<option>";
                htmlStr += (i+1) + " " + moduleArray["inputName"][i] + ":" + moduleArray["inputTypeName"][i];
                htmlStr += "</option>"
            }
            htmlStr += "</select>"
            htmlStr += "</fieldset>"
            return htmlStr;
        }
        function getOutputArgumentFieldsetStr(moduleArray) {
            htmlStr = "<fieldset><legend>Output</legend>";
            htmlStr += "<select size='3' class='fullWidth'>"
            for(i=0;i<moduleArray['outputNum'];i++)
            {
                htmlStr += "<option>";
                htmlStr += (i+1) + " " + moduleArray["outputName"][i] + ":" + moduleArray["outputTypeName"][i];
                htmlStr += "</option>"
            }
            htmlStr += "</select>"
            htmlStr += "</fieldset>"
            return htmlStr
        }
        function getParaFieldsetStr(moduleArray) {
            htmlStr = "<fieldset><legend>Parameter</legend>";
            htmlStr += "<table class='fullWidth'>"
            for(i=0;i<moduleArray['paraNum'];i++)
            {
                htmlStr += "<tr>"
                htmlStr += "<td style='width: 20%'>" + moduleArray['paraName'][i] + ":</td>";
                htmlStr += "<td style='width: 75%'><input type='text' id='itemPara_" + i + "'></td>"
                htmlStr += "</tr>"
            }
            htmlStr += "</table>"
            htmlStr += "</fieldset>"
            return htmlStr
        }
        function setParamsTextValue(moduleArray, paramStr) {
            var fileds = paramStr.split(";")
            for(i=0;i<moduleArray['paraNum'];i++) {
                document.getElementById("itemPara_"+i).value = fileds[i+1];
            }
        }
        function getActionFieldsetStr()
        {
            htmlStr = "<fieldset><legend>Action</legend>";
            htmlStr += "<input type='button' value='Insert New Item' style='height:30px; width:100%;' onclick='insertNewItemToExp()'>"
            htmlStr += "</filedset>"
            return htmlStr;
        }
        function getParaActionFieldsetStr()
        {
            htmlStr = "<fieldset><legend>Action</legend>";
            htmlStr += "<input type='button' value='Set Parameters' style='height:30px; width:100%;' onclick='updateParamsStrOfCurrentSelNode()'>"
            htmlStr += "</filedset>"
            return htmlStr;
        }
        function updateParamsStrOfCurrentSelNode()
        {
            currentSelectNode.data.parameters = getFilledParamsStr()
        }

        // for clone exp show vis
        function loadAndGetExpVis(expId) {
            xmlHttp = getXMLHttp();
            xmlhttp.onreadystatechange = function()
            {
                if (xmlhttp.readyState==4 && xmlhttp.status==200)
                {
                    try
                    {
                        myDiagram.model = go.Model.fromJson(xmlhttp.responseText);
                    }
                    catch(err) {
                        alert("No or Incorrect experiment visualization file.")
                    }
                }
            }
            xmlhttp.open("GET","getExpVisJsonWithStatus.php?expId="+expId,true);
            xmlhttp.send();
        }
    </script>

    <!-- javascript to send data and module table-->
    <script language="javascript" type="text/javascript">
        var props = {
            sort: true,
            filters_row_index:1,
            paging: true,
            paging_length: 5,
            col_2: "select",
            col_3: "select",
            col_4: "none",
            loader: true,
            loader_html: '<h4 style="color:green;">Loading, please wait...</h4>'
        };
        var tf1 = setFilterGrid("dataListTable", props);
        var tf1 = setFilterGrid("moduleListTable", props);
    </script>
    <!--
    <?php
    include "htmlFooter.php";
    ?>
    -->
</body>
</html>