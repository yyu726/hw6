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
            margin: auto;
            width: 1400px;
            outline: dotted;
        }


        table {
            width: 1400px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 2px solid rgb(200, 200, 200);
            font-size: 20px;
        }

        img {
            width: 100px;
        }
    </style>
</head>

<body onload="getLocation();receiveJSON()">
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
function searchRequest($form)
{
    $OPERATION_NAME = 'findItemsAdvanced';
    $SERVICE_VERSION = '1.0.0';
    $SECURITY_APPNAME = 'YangYu-CSCI571-PRD-7a6d8bb94-950c1bc5';
    $RESPONSE_DATA_FORMAT = 'JSON';
    $paginationInput_entriesPerPage = '20';
    $keywords = $form["keywords"];
    $categoryArray = array('All' => NULL, 'Art' => '550', 'Baby' => '2984', 'Books' => '267', 'CSA' => '11450',
        'CTN' => '58058', 'HB' => '26395', 'Music' => '11233', 'VGC' => '1249');
    $categoryId = $categoryArray[$form["category"]];
    $buyerPostalCode = isset($form["nearbySearch"]) ? $form["centerpoint"] : NULL;
    $MaxDistance = isset($form["nearbySearch"]) ? $form["distance"] : NULL;
    $LocalPickupOnly = isset($form["shipping1"]) ? "true" : NULL;
    $FreeShippingOnly = isset($form["shipping2"]) ? "true" : NULL;
    $Condition = NULL;
    if (isset($_POST["condition1"])) {
        $Condition[] = $form["condition1"];
    }
    if (isset($_POST["condition2"])) {
        $Condition[] = $form["condition2"];
    }
    if (isset($_POST["condition3"])) {
        $Condition[] = $form["condition3"];
    }
    $searchURL = "https://svcs.ebay.com/services/search/FindingService/v1?";
    $searchURL = $searchURL . "OPERATION-NAME=" . $OPERATION_NAME;
    $searchURL = $searchURL . "&SERVICE-VERSION=" . $SERVICE_VERSION;
    $searchURL = $searchURL . "&SECURITY-APPNAME=" . $SECURITY_APPNAME;
    $searchURL = $searchURL . "&RESPONSE-DATA-FORMAT=" . $RESPONSE_DATA_FORMAT;
    $searchURL = $searchURL . "&REST-PAYLOAD";
    $searchURL = $searchURL . "&paginationInput.entriesPerPage=" . $paginationInput_entriesPerPage;
    $searchURL = $searchURL . "&keywords=" . rawurlencode($keywords);
    if (isset($categoryId)) {
        $searchURL = $searchURL . "&categoryId=" . $categoryId;
    }
    if (isset($buyerPostalCode)) {
        $searchURL = $searchURL . "&buyerPostalCode=" . $buyerPostalCode;
    }
    $filterCount = 0;
    if (isset($MaxDistance)) {
        $searchURL = $searchURL . "&itemFilter($filterCount).name=Maxdistance";
        $searchURL = $searchURL . "&itemFilter($filterCount).value=" . $MaxDistance;
        $filterCount++;
    }
    if (isset($FreeShippingOnly)) {
        $searchURL = $searchURL . "&itemFilter($filterCount).name=FreeShippingOnly";
        $searchURL = $searchURL . "&itemFilter($filterCount).value=" . $FreeShippingOnly;
        $filterCount++;
    }
    if (isset($LocalPickupOnly)) {
        $searchURL = $searchURL . "&itemFilter($filterCount).name=LocalPickupOnly";
        $searchURL = $searchURL . "&itemFilter($filterCount).value=" . $LocalPickupOnly;
        $filterCount++;
    }
    $searchURL = $searchURL . "&itemFilter($filterCount).name=HideDuplicateItem";
    $searchURL = $searchURL . "&itemFilter($filterCount).value=true";
    $filterCount++;
    if (!is_null($Condition)) {
        $searchURL = $searchURL . "&itemFilter($filterCount).name=Condition";
        for ($i = 0; $i < count($Condition); $i++) {
            $searchURL = $searchURL . "&itemFilter($filterCount).value($i)=" . $Condition[$i];
        }
        $filterCount++;
    }
    $searchText = file_get_contents($searchURL);
    global $searchObj;
    $searchObj = json_decode($searchText);

    echo $searchURL;
    //echo $requestText;
    //var_dump($requestObj);
}

function searchProcess()
{
    global $searchObj;
    $searchResult = $searchObj->{'findItemsAdvancedResponse'}[0]->{'searchResult'}[0];
    $itemCount = $searchResult->{'@count'};
    $item = array();
    for ($i = 0; $i < $itemCount; $i++) {
        $itemTemp = $searchResult->{'item'}[$i];
        $item[$i]["Index"] = $i + 1;
        $item[$i]["Photo"] = isset($itemTemp->{'galleryURL'}[0]) ? $itemTemp->{'galleryURL'}[0] : 'N/A';
        $item[$i]["Name"] = isset($itemTemp->{'title'}[0]) ? $itemTemp->{'title'}[0] : 'N/A' ;
        $item[$i]["Price"]["Currency"] = isset($itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'@currencyId'}) ? $itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'@currencyId'} : 'N/A';
        $item[$i]["Price"]["Value"] = isset($itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'__value__'}) ? $itemTemp->{'sellingStatus'}[0]->{'currentPrice'}[0]->{'__value__'} : 'N/A';
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
        $item[$i]["ItemId"] = $itemTemp->{'itemId'}[0];
    }
    global $itemJSON;
    $itemJSON['Title'] = 'fingdingResult';
    $itemJSON['Header'] = array('Index', 'Photo', 'Name', 'Price', 'Zip code', 'Condition', 'Shipping Option');
    $itemJSON['Item'] = $item;
    $itemJSON = json_encode($itemJSON);
    //var_dump($itemJSON);
}

function detailRequest($itemId)
{
    $callName = 'GetSingleItem';
    $responseencoding = 'JSON';
    $appid = 'YangYu-CSCI571-PRD-7a6d8bb94-950c1bc5';
    $siteid = '0';
    $version = '967';
    $IncludeSelector = 'Description,Details,ItemSpecifics';

    $detailURL = 'http://open.api.ebay.com/shopping?';
    $detailURL = $detailURL . 'callname=' . $callName;
    $detailURL = $detailURL . '&responseencoding=' . $responseencoding;
    $detailURL = $detailURL . '&appid=' . $appid;
    $detailURL = $detailURL . '&siteid=' . $siteid;
    $detailURL = $detailURL . '&version=' . $version;
    $detailURL = $detailURL . '&ItemID=' . $itemId;
    $detailURL = $detailURL . '&IncludeSelector=' . $IncludeSelector;
    echo $detailURL;
    $detailText = file_get_contents($detailURL);
    global $detailObj;
    $detailObj = json_decode($detailText);
}

function detailProcess() {
    global $detailObj;
    $detailJSON = null;
    if ($detailObj->{'Ack'} == 'Failure'){
        $detailJSON['Row'] = array();
        $detailJSON['Description'] = '';
    }
    if ($detailObj->{'Ack'} == 'Success') {
        //$detailJSON['Description'] = $detailObj->{'Item'}->{'Description'};
        $detailJSON['Row']['Photo'] = isset($detailObj->{'Item'}->{'PictureURL'}[0]) ? $detailObj->{'Item'}->{'PictureURL'}[0] : 'N/A';
        $detailJSON['Row']['Title'] = isset($detailObj->{'Item'}->{'Title'}) ? $detailObj->{'Item'}->{'Title'} : 'N/A';
        $detailJSON['Row']['Subtitle'] = isset($detailObj->{'Item'}->{'Subtitle'}) ? $detailObj->{'Item'}->{'Subtitle'} : 'N/A';
        $detailJSON['Row']['Price']['Currency'] = isset($detailObj->{'Item'}->{'CurrentPrice'}->{'CurrencyID'}) ? $detailObj->{'Item'}->{'CurrentPrice'}->{'CurrencyID'} : 'N/A';
        $detailJSON['Row']['Price']['Value'] = isset($detailObj->{'Item'}->{'CurrentPrice'}->{'Value'}) ? $detailObj->{'Item'}->{'CurrentPrice'}->{'Value'} : 'N/A';
        $detailJSON['Row']['Location'] = isset($detailObj->{'Item'}->{'Location'}) ? $detailObj->{'Item'}->{'Location'} : 'N/A';
        $detailJSON['Row']['PostalCode'] = isset($detailObj->{'Item'}->{'PostalCode'}) ? $detailObj->{'Item'}->{'PostalCode'} : 'N/A';
        $detailJSON['Row']['Seller'] = isset($detailObj->{'Item'}->{'Seller'}->{'UserID'}) ? $detailObj->{'Item'}->{'Seller'}->{'UserID'} : 'N/A';
        $detailJSON['Row']['ReturnPolicy']['ReturnsAccepted'] = isset($detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsAccepted'}) ? $detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsAccepted'} : 'N/A';
        $detailJSON['Row']['ReturnPolicy']['ReturnsWithin'] = isset($detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsWithin'}) ? $detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsWithin'} : 'N/A';
        $detailJSON['Row']['ItemSpecifics'] = isset($detailObj->{'Item'}->{'ItemSpecifics'}) ? $detailObj->{'Item'}->{'ItemSpecifics'} : array();
    }
    $detailJSON = json_encode($detailJSON);
    var_dump($detailJSON);

}

if (isset($_POST["keywords"])) {
    $searchObj;
    $itemJSON;
    $form = $_POST;
    searchRequest($form);
    searchProcess();
    //echo($itemJSON);
}
/*$itemId = '273189058712';
$detailObj;
detailRequest($itemId);
//var_dump($detailObj);
detailProcess();*/
?>

<div class="mainbox">
    <div class="title">
        <p class="title">Product Search</p>
    </div>
    <div class="divideLine"></div>
    <form name="myform" method="POST" id="form" onsubmit="receiveJSON()">
        <b>Keyword</b>
        <input class="keywordInput" type="text" name="keywords" maxlength="255" size="100" value="USC" required/>
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
<iframe id="foo" name="foo" style="display: none">

</iframe>
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
        itemJSON = <?php echo $itemJSON;?>;
        document.getElementById("iframe").innerHTML = generateSearchHTML(itemJSON);
    }

    function receiveJSON1() {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.overrideMimeType("application/json");
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                jsonObj = this.responseText;
                document.getElementById("iframe").innerHTML = generateHTML(jsonObj);
            }
        }
        xmlhttp.open("GET", "productSearch.php", false);
        xmlhttp.send();
    }

    function drawDetail() {
        jsonObj = <?php echo $itemJSON;?>;
        document.getElementById("iframe").innerHTML = generateHTML(jsonObj);
    }

    function generateSearchHTML(jsonObj) {
        root = jsonObj.DocumentElement;
        search_text = "<html><head><title></title></head><body style='font-family:Times New Roman'>";
        search_text += "<table  >";
        search_text += "<tbody>";
        search_text += "<tr>";
        // output the headers
        var searchHeader = jsonObj.Header;
        for (i = 0; i < searchHeader.length; i++) {
            search_text += "<th>" + searchHeader[i] + "</th>";
        }
        search_text += "</tr>";
        // output out the values
        var search_items = jsonObj.Item;
        for (i = 0; i < search_items.length; i++) //do for all films (one per row)
        {
            search_item = search_items[i]; //get properties of a film (an object)
            search_text += "<tr>";      //start a new row of the output table
            search_item_keys = Object.keys(search_item);
            for (j = 0; j < search_item_keys.length; j++) {
                key = search_item_keys[j];
                if (key == 'Price') {
                    search_text += "<td>"
                    if (search_item[key].Currency == "USD") {
                        search_text += '$';
                    }
                    search_text += search_item[key].Value + "</td>";
                } else if (search_item_keys[j] == "Photo") {//handle images separately
                    if (search_item[key] == "") {
                        search_text += "<td>" + "</td>";
                    } else {
                        search_text += "<td><img src='" + search_item[key] + "'></td>";
                    }
                } else if (search_item_keys[j] == "ItemId") {
                    continue;
                } else {
                    search_text += "<td>" + search_item[key] + "</td>";
                }

            }
            search_text += "</tr>";
        }
        search_text += "</tbody>";
        search_text += "</table>";
        search_text += "</body></html>";
        return search_text;
    }
</script>

</body>

</html>