<html>

<head>
    <title>Pruduct Search</title>
    <style>
        :root {
            --bodyWidth: 700px;
        }

        body {
            width: var(--siteWidth);
            margin: 0px;
            padding: 0px;
            border: 0px;
            font-family: Times New Roman;
        }

        div.mainbox {
            width: var(--bodyWidth);
            margin: auto;
            text-align: center;
            border-style: solid;
            border-color: rgb(175, 175, 175);
        }

        div.title {
            width: var(--bodyWidth);
            height: 55px;
        }

        p.title {
            margin: 0px;
            height: 50px;
            font-style: italic;
            font-size: 40px;
            color: black;
        }

        .divideLine {
            margin: auto;
            width: calc(var(--bodyWidth) - 10px);
            height: 2px;
            background-color: rgb(175, 175, 175);
        }

        #form {
            margin-top: 10px;
            text-align: left;
            font-size: 20px;
            margin-left: 20px;
            line-height: 40px;
        }

        input.keywordInput {
            width: 150px;
        }

        select.categoryInput {
            width: 250px;
        }

        input.checkbox {
            margin-left: 20px;
        }

        input.shipping {
            margin-left: 38px;
        }

        #milesInput {
            width: 60px;
            margin-left: 25px;
        }

        #milesLabel {
            margin-left: 0px;
            color: grey;
        }

        #hereLabel {
            font-weight: normal;
            color: grey;
        }

        #zipRadio {
            margin-left: 411px;
            display: inline-block;
            vertical-align: top;
        }

        #zipInput {
            width: 150px;
            display: inline-block;
            vertical-align: top;
        }

        input[type=submit] {
            margin-left: 250px;
        }

        #iframe {
            margin:auto;
            width:1400px;
            outline: dotted;
        }




        table {
            width:1400px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 2px solid rgb(200, 200, 200);
            font-size: 20px;
        }

        img {
            width:100px;
        }
    </style>
</head>

<body onload="getLocation()">
<script>
    function getLocation() {
        var s = document.createElement("script");
        s.src = "http://ip-api.com/json/?callback=setLocation";
        document.body.appendChild(s);
    }

    var zip;

    function setLocation(json) {
        zip = json.zip;
        document.getElementById("submitButton").removeAttribute("disabled");
        document.getElementById("hereRadio").setAttribute("value", zip);
    }

</script>

<?php
/**/
$requestObj;
$itemJSON;
function requestFinding() {
    if (isset($_POST["keyword"])) {
        //var_dump($_POST);
        $OPERATION_NAME = 'findItemsAdvanced';
        $SERVICE_VERSION = '1.0.0';
        $SECURITY_APPNAME = 'YangYu-CSCI571-PRD-7a6d8bb94-950c1bc5';
        $RESPONSE_DATA_FORMAT = 'JSON';
        $paginationInput_entriesPerPage = '20';
        $keywords = $_POST["keyword"];
        $categoryArray = array('All' => NULL, 'Art' => '550', 'Baby' => '2984', 'Books' => '267', 'CSA' => '11450',
            'CTN' => '58058', 'HB' => '26395', 'Music' => '11233', 'VGC' => '1249');
        $categoryId = $categoryArray[$_POST["category"]];
        $buyerPostalCode = isset($_POST["nearbySearch"]) ? $_POST["centerpoint"] : NULL;
        $MaxDistance = isset($_POST["nearbySearch"]) ? $_POST["distance"] : NULL;
        $LocalPickupOnly = isset($_POST["shipping1"]) ? "true" : NULL;
        $FreeShippingOnly = isset($_POST["shipping2"]) ? "true" : NULL;
        $Condition = NULL;
        if (isset($_POST["condition1"])) {
            $Condition[] = $_POST["condition1"];
        }
        if (isset($_POST["condition2"])) {
            $Condition[] = $_POST["condition2"];
        }
        if (isset($_POST["condition3"])) {
            $Condition[] = $_POST["condition3"];
        }
        $requestURL = "https://svcs.ebay.com/services/search/FindingService/v1?";
        $requestURL = $requestURL . "OPERATION-NAME=" . $OPERATION_NAME;
        $requestURL = $requestURL . "&SERVICE-VERSION=" . $SERVICE_VERSION;
        $requestURL = $requestURL . "&SECURITY-APPNAME=" . $SECURITY_APPNAME;
        $requestURL = $requestURL . "&RESPONSE-DATA-FORMAT=" . $RESPONSE_DATA_FORMAT;
        $requestURL = $requestURL . "&REST-PAYLOAD";
        $requestURL = $requestURL . "&paginationInput.entriesPerPage=" . $paginationInput_entriesPerPage;
        $requestURL = $requestURL . "&keywords=" . rawurlencode($keywords);
        if (isset($categoryId)) {
            $requestURL = $requestURL . "&categoryId=" . $categoryId;
        }
        if (isset($buyerPostalCode)) {
            $requestURL = $requestURL . "&buyerPostalCode=" . $buyerPostalCode;
        }
        $filterCount = 0;
        if (isset($MaxDistance)) {
            $requestURL = $requestURL . "&itemFilter($filterCount).name=Maxdistance";
            $requestURL = $requestURL . "&itemFilter($filterCount).value=" . $MaxDistance;
            $filterCount++;
        }
        if (isset($FreeShippingOnly)) {
            $requestURL = $requestURL . "&itemFilter($filterCount).name=FreeShippingOnly";
            $requestURL = $requestURL . "&itemFilter($filterCount).value=" . $FreeShippingOnly;
            $filterCount++;
        }
        if (isset($LocalPickupOnly)) {
            $requestURL = $requestURL . "&itemFilter($filterCount).name=LocalPickupOnly";
            $requestURL = $requestURL . "&itemFilter($filterCount).value=" . $LocalPickupOnly;
            $filterCount++;
        }
        $requestURL = $requestURL . "&itemFilter($filterCount).name=HideDuplicateItem";
        $requestURL = $requestURL . "&itemFilter($filterCount).value=true";
        $filterCount++;
        if (!is_null($Condition)){
            $requestURL = $requestURL . "&itemFilter($filterCount).name=Condition";
            for ($i = 0; $i < count($Condition); $i++) {
                $requestURL = $requestURL . "&itemFilter($filterCount).value($i)=" . $Condition[$i];
            }
            $filterCount++;
        }
        //echo $requestURL;
        $jsontext = file_get_contents($requestURL);
        //echo $jsontext;
        global $requestObj;
        $requestObj = json_decode($jsontext);
        //var_dump($requestObj);
    }
}
function processFinding() {
    global $requestObj;
    $searchResult = $requestObj->{'findItemsAdvancedResponse'}[0]->{'searchResult'}[0];
    $itemCount = $searchResult->{'@count'};
    $item = array();
    for ($i = 0; $i < $itemCount; $i++) {
        $itemTemp = $searchResult->{'item'}[$i];
        $item[$i]["Index"] = $i;
        $item[$i]["Photo"] = $itemTemp->{'galleryURL'}[0];
        $item[$i]["Name"] = $itemTemp->{'title'}[0];
        $item[$i]["Price"]["Currency"] = $itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'@currencyId'};
        $item[$i]["Price"]["Value"] = $itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'__value__'};
        //$item[$i]["Price"] = $itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'@currencyId'} . $itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'__value__'};
        $item[$i]["Zip"] = isset($itemTemp->{'postalCode'}[0]) ? $itemTemp->{'postalCode'}[0] : 'N/A';
        $item[$i]["Condition"] = isset($itemTemp->{'condition'}[0]) ? $itemTemp->{'condition'}[0]->{'conditionDisplayName'}[0] : 'N/A';
        if (isset($itemTemp->{'shippingInfo'}[0]->{'shippingServiceCost'}[0]->{'__value__'})) {
            $item[$i]["Shipping"] = $itemTemp->{'shippingInfo'}[0]->{'shippingServiceCost'}[0]->{'__value__'};
            if ($item[$i]["Shipping"] == '0.0') {
                $item[$i]["Shipping"] = 'FreeShipping';
            } else {
                $item[$i]["Shipping"] = '$' . $item[$i]["Shipping"];
            }
        } else {
            $item[$i]["Shipping"] = 'N/A';
        }
    }
    global $itemJSON;
    $itemJSON['Title'] = 'fingdingResult';
    $itemJSON['Header'] = array('Index', 'Photo', 'Name', 'Price', 'Zip code', 'Condition', 'Shipping Option');
    $itemJSON['Item'] = $item;
    $itemJSON = json_encode($itemJSON);
    //var_dump($itemJSON);
}
if (isset($_POST["keyword"])) {
    requestFinding();
    processFinding();
}
/*$test = "test";
echo ($test);*/
?>

<div class="mainbox">
    <div class="title">
        <p class="title">Product Search</p>
    </div>
    <div class="divideLine"></div>
    <form name="myform" method="POST" id="form" target="iframe" onsubmit="receiveJSON();event.preventDefault();event.stopPropagation()">
        <b>Keyword</b>
        <input class="keywordInput" type="text" name="keyword" maxlength="255" size="100" value="USC" required/>
        <br/>
        <b>Category</b>
        <select class="categoryInput" name="category">
            <option value="All" selected>All Categories</option>
            <option value="Art">Art</option>
            <option value="Baby">Baby</option>
            <option value="Books">Books</option>
            <option value="CSA">Clothing, Shoes & Accessories</option>
            <option value="CTN">Computer/Tablets & Networking</option>
            <option value="HB">Health & Beauty</option>
            <option value="Music">Music</option>
            <option value="VGC">Video Games & Consoles</option>
        </select>
        <br/>
        <b>Condition</b>
        <input class="checkbox" type="checkbox" name="condition1" value="New">New
        <input class="checkbox" type="checkbox" name="condition2" value="Used">Used
        <input class="checkbox" type="checkbox" name="condition3" value="Unspecified">Unspecified
        <br/>
        <b>Shipping Options</b>
        <input class="shipping" type="checkbox" name="shipping1" value="LocalPickup">Local Pickup
        <input class="shipping" type="checkbox" name="shipping2" value="FreeShipping">Free Shipping
        <br/>
        <input class="nearbySearch" type="checkbox" name="nearbySearch" value="enabled"
               onchange="enableSearch(this)"><b>Enable Nearby Search</b>
        <input id="milesInput" type="text" name="distance" maxlength="255" size="100" placeholder="10" value="10"
               disabled>
        <b id="milesLabel">miles from</b>
        <input id="hereRadio" type="radio" name="centerpoint" value="Here" checked disabled onchange="disableZip(this)">
        <id id="hereLabel">Here</id>
        <br>
        <input id="zipRadio" type="radio" name="centerpoint" value="Zipcode" onchange="enableZip(this)" disabled>
        <input type="text" name="zipcode" id="zipInput" maxlength="5" placeholder="zipcode" disabled required>
        <br/>
        <input id="submitButton" type="submit" value="Search" disabled>
        <input type="reset" value="Clear">
    </form>

</div>
<div id="iframe" name="iframe">

</div>


<script type="text/javascript">
    function enableSearch(checkbox) {
        if (checkbox.checked == true) {
            document.getElementById("milesInput").removeAttribute("disabled");
            document.getElementById("zipRadio").removeAttribute("disabled");
            document.getElementById("hereRadio").removeAttribute("disabled");
            document.getElementById("milesLabel").setAttribute("style", "color: black");
            document.getElementById("hereLabel").setAttribute("style", "color: black");
            if (document.getElementById("zipRadio").checked == true) {
                document.getElementById("zipInput").removeAttribute("disabled");
            } else {
                document.getElementById("zipInput").setAttribute("disabled", "disabled");
            }
        } else {
            document.getElementById("milesInput").setAttribute("disabled", "disabled");
            document.getElementById("zipRadio").setAttribute("disabled", "disabled");
            document.getElementById("zipInput").setAttribute("disabled", "disabled");
            document.getElementById("hereRadio").setAttribute("disabled", "disabled");
            document.getElementById("milesLabel").setAttribute("style", "color: grey");
            document.getElementById("hereLabel").setAttribute("style", "color: grey");
        }
    }

    function disableZip(radio) {
        if (radio.checked == true) {
            document.getElementById("zipInput").setAttribute("disabled", "disabled");
        }
    }

    function enableZip(radio) {
        if (radio.checked == true) {
            document.getElementById("zipInput").removeAttribute("disabled");
        }
    }
    function receiveJSON() {
        jsonObj = <?php echo $itemJSON; ?>;
        document.getElementById("iframe").innerHTML = generateHTML(jsonObj);
    }

    function receiveJSON1() {
        jsonObj = <?php echo $itemJSON; ?>;
        document.getElementById("iframe").innerHTML = generateHTML(jsonObj);
        hWin = window.open("", "Assignment4", "height=600,width=1200");
        jsonObj.onload = generateHTML(jsonObj);
        hWin.document.write(html_text);
        hWin.document.close();
    }

    function generateHTML(jsonObj) {
        root = jsonObj.DocumentElement;
        html_text = "<html><head><title>Highest-grossing Films</title></head><body style='font-family:Times New Roman'>";
        html_text += "<table  >";
        html_text += "<tbody>";
        html_text += "<tr>";
        // output the headers
        var tableHeader = jsonObj.Header;
        for (i = 0; i < tableHeader.length; i++) {
            html_text += "<th>" + tableHeader[i] + "</th>";
        }
        html_text += "</tr>";
        // output out the values
        var items = jsonObj.Item;
        for (i = 0; i < items.length; i++) //do for all films (one per row)
        {
            itemNodeList = items[i]; //get properties of a film (an object)
            html_text += "<tr>";      //start a new row of the output table
            var item_keys = Object.keys(itemNodeList);
            for (j = 0; j < item_keys.length; j++) {
                prop = item_keys[j];
                if (prop == 'Price') {
                    html_text += "<td>"
                    if (itemNodeList[prop].Currency == "USD") {
                        html_text += '$';
                    }
                    html_text += itemNodeList[prop].Value + "</td>";
                } else if (item_keys[j] == "Photo") {//handle images separately
                    if (itemNodeList[prop] == "") {
                        html_text += "<td>" + "</td>";
                    } else {
                        html_text += "<td><img src='" + itemNodeList[prop] + "'></td>";
                    }
                } else {
                    html_text += "<td>" + itemNodeList[prop] + "</td>";
                }

            }
            html_text += "</tr>";
        }
        html_text += "</tbody>";
        html_text += "</table>";
        html_text += "</body></html>";
        return html_text;
    }
</script>

</body>

</html>