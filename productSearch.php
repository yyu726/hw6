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
        #divSearch {
            margin:auto;
            width:1400px;
            margin-top:100px;
        }
        #divDetail {
            margin: auto;
            margin-top:100px;
            width: 800px;
        }
        #sellerButton {
            width:300px;
            height:300px;
            background-color:blue;
            margin:auto;
        }
        #divSeller{
            margin:auto;
            width:1600px;
        }
        #iframeSeller {
            width:1600px;
            height : 300px;
            outline: dotted;
            overflow-x: hidden;
        }
        #similarButton {
            width:300px;
            height:300px;
            background-color:red;
            margin:auto;
        }

        #divSimilar {
            margin:auto;
            margin: auto;
            margin-top:100px;
            width: 1000px;
            overflow:auto;
            border: 2px solid rgb(200, 200, 200);
        }

        #searchTable {
            width: 1400px;
            border-collapse: collapse;
        }

        #detailTable {
            border-collapse: collapse;
        }

        table, th, td {
            border: 2px solid rgb(200, 200, 200);
            font-size: 20px;
        }

        #divSimilar td {
            border:none;
            padding-left:20px;
            padding-right:20px;
        }
        #similarTable {
            border:none;
        }
        img {
            width: 100px;
        }

        .detailRefrence {
            cursor: pointer;

        }

        .detailRefrence:hover {
            color:grey;

        }

    </style>
</head>

<body onload="getLocation();loadPage()">
<script>
    function getLocation() {
        var s = document.createElement("script");
        s.src = "http://ip-api.com/json/?callback=setLocation";
        document.body.appendChild(s);
        setLocation(s);
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
function searchRequest()
{
    global $form;
    $OPERATION_NAME = 'findItemsAdvanced';
    $SERVICE_VERSION = '1.0.0';
    $SECURITY_APPNAME = 'YangYu-CSCI571-PRD-7a6d8bb94-950c1bc5';
    $RESPONSE_DATA_FORMAT = 'JSON';
    $paginationInput_entriesPerPage = '20';
    $keywords = $form["keywords"];
    $categoryArray = array('All' => NULL, 'Art' => '550', 'Baby' => '2984', 'Books' => '267', 'CSA' => '11450',
        'CTN' => '58058', 'HB' => '26395', 'Music' => '11233', 'VGC' => '1249');
    $categoryId = $categoryArray[$form["category"]];
    $buyerPostalCode = null;
    if (isset($form["nearbySearch"])) {
        if (isset($form["hereRadio"])) {
            $buyerPostalCode = $form["hereRadio"];
        }
        if (isset($form["zipInput"])) {
            $buyerPostalCode = $form["zipInput"];
        }
    }
    $MaxDistance = isset($form["nearbySearch"]) ? $form["distance"] : NULL;
    $LocalPickupOnly = isset($form["shipping1"]) ? "true" : NULL;
    $FreeShippingOnly = isset($form["shipping2"]) ? "true" : NULL;
    $Condition = null;
    if (isset($form["condition1"])) {
        $Condition[] = $form["condition1"];
    }
    if (isset($form["condition2"])) {
        $Condition[] = $form["condition2"];
    }
    if (isset($form["condition3"])) {
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
        $searchURL = $searchURL . "&itemFilter($filterCount).name=MaxDistance";
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
    //echo $searchText;
    //var_dump($searchObj);
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
    $itemJSON = null;
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
    //echo $detailURL;
    $detailText = file_get_contents($detailURL);
    //var_dump($detailText);
    global $detailObj;
    $detailObj = json_decode($detailText);
}

function detailProcess() {
    global $detailObj;
    $detail = null;
    if ($detailObj->{'Ack'} == 'Failure'){
        $detail = array();
        $seller = '';
    }
    if ($detailObj->{'Ack'} == 'Success') {
        $seller['Description'] = $detailObj->{'Item'}->{'Description'};
        $detail[0]['Photo'] = isset($detailObj->{'Item'}->{'PictureURL'}[0]) ? $detailObj->{'Item'}->{'PictureURL'}[0] : 'N/A';
        $detail[0]['Title'] = isset($detailObj->{'Item'}->{'Title'}) ? $detailObj->{'Item'}->{'Title'} : 'N/A';
        $detail[0]['Subtitle'] = isset($detailObj->{'Item'}->{'Subtitle'}) ? $detailObj->{'Item'}->{'Subtitle'} : 'N/A';
        $detail_currency = isset($detailObj->{'Item'}->{'CurrentPrice'}->{'CurrencyID'}) ? $detailObj->{'Item'}->{'CurrentPrice'}->{'CurrencyID'} : 'N/A';
        $detail_value = isset($detailObj->{'Item'}->{'CurrentPrice'}->{'Value'}) ? $detailObj->{'Item'}->{'CurrentPrice'}->{'Value'} : 'N/A';
        if ($detail_currency == 'N/A' && $detail_value == 'N/A') {
            $detail[0]['Price'] = 'N/A';
        } else {
            $detail[0]['Price'] = $detail_currency . $detail_value;
        }
        $detail[0]['Location'] = isset($detailObj->{'Item'}->{'Location'}) ? $detailObj->{'Item'}->{'Location'} : 'N/A';
        $detail[0]['PostalCode'] = isset($detailObj->{'Item'}->{'PostalCode'}) ? $detailObj->{'Item'}->{'PostalCode'} : 'N/A';
        $detail[0]['Seller'] = isset($detailObj->{'Item'}->{'Seller'}->{'UserID'}) ? $detailObj->{'Item'}->{'Seller'}->{'UserID'} : 'N/A';
        $detail_returnaccpted = isset($detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsAccepted'}) ? $detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsAccepted'} : 'N/A';
        $detail_returnwithin = isset($detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsWithin'}) ? ' Within ' . $detailObj->{'Item'}->{'ReturnPolicy'}->{'ReturnsWithin'} : 'N/A';
        if ($detail_returnaccpted == 'N/A' && $detail_returnwithin == 'N/A') {
            $detail[0]['ReturnPolicy'] = 'N/A';
        } else {
            $detail[0]['ReturnPolicy'] = $detail_returnaccpted . $detail_returnwithin;
        }
        $detail[0]['ItemSpecifics'] = isset($detailObj->{'Item'}->{'ItemSpecifics'}) ? $detailObj->{'Item'}->{'ItemSpecifics'} : array();
    }
    global $sellerJSON;
    global $detailJSON;
    $sellerJSON = json_encode($seller);
    $detailJSON = json_encode($detail);
    //var_dump($detailJSON);


}

function similarRequest($itemId) {
    $OPERATION_NAME = 'getSimilarItems';
    $SERVICE_NAME = 'MerchandisingService';
    $SERVICE_VERSION = '1.1.0';
    $CONSUMER_ID = 'YangYu-CSCI571-PRD-7a6d8bb94-950c1bc5';
    $RESPONSE_DATA_FORMAT = 'JSON';
    $maxResults = 8;
    $similarURL = 'http://svcs.ebay.com/MerchandisingService?';
    $similarURL = $similarURL . 'OPERATION-NAME=' . $OPERATION_NAME;
    $similarURL = $similarURL . '&SERVICE-NAME=' . $SERVICE_NAME;
    $similarURL = $similarURL . '&SERVICE-VERSION=' . $SERVICE_VERSION;
    $similarURL = $similarURL . '&CONSUMER-ID=' . $CONSUMER_ID;
    $similarURL = $similarURL . '&RESPONSE-DATA-FORMAT=' . $RESPONSE_DATA_FORMAT;
    $similarURL = $similarURL . '&REST-PAYLOAD' ;
    $similarURL = $similarURL . '&itemId=' . $itemId;
    $similarURL = $similarURL . '&maxResults=' . $maxResults;
    //echo $similarURL;
    $similarText = file_get_contents($similarURL);
    //var_dump($similarText);
    global $similarObj;
    $similarObj = json_decode($similarText);
}

function similarProcess() {
    global $similarObj;

    $similar = null;
    if ($similarObj->{'getSimilarItemsResponse'}->{'ack'} == 'Failure'){
        $similar = array();
    }
    if ($similarObj->{'getSimilarItemsResponse'}->{'ack'} == 'Success') {
        $similar = array();

        $similarItem = $similarObj->{'getSimilarItemsResponse'}->{'itemRecommendations'}->{'item'};
        for ($i = 0; $i < count($similarItem); $i++) {
            $similar[$i]['ItemID'] = isset($similarItem[$i]->{'itemId'}) ? $similarItem[$i]->{'itemId'} : 'N/A';
            $similar[$i]['Title'] = isset($similarItem[$i]->{'title'}) ? $similarItem[$i]->{'title'} : 'N/A';
            $similar[$i]['Photo'] = isset($similarItem[$i]->{'imageURL'}) ? $similarItem[$i]->{'imageURL'} : 'N/A';
            $similar[$i]['Price'] = isset($similarItem[$i]->{'buyItNowPrice'}->{'__value__'}) ? 'USD' . $similarItem[$i]->{'buyItNowPrice'}->{'__value__'} : 'N/A';
        }
        global $similarJSON;
        $similarJSON = json_encode($similar);
        //var_dump($similarJSON);
    }
}

function initialForm() {
    global $form;
    $form["keywords"] = null;
    $form["category"] = null;
    $form["condition1"] = null;
    $form["condition2"] = null;
    $form["condition3"] = null;
    $form["shipping1"] = null;
    $form["shipping2"] = null;
    $form["nearbySearch"] = null;
    $form["zipInput"] = null;
}
$form;
initialForm();

$searchObj = 'null';
$itemJSON = 'null';
if (isset($_POST["keywords"])) {
    $form = $_POST;
    searchRequest();
    searchProcess();
    //echo($itemJSON);
}
$itemId = null;
if (isset($_POST["itemId"]) && $_POST["itemId"] != '') {
    $itemId = $_POST["itemId"];
} else {
    $itemId = '233153942491';
}
$detailObj = 'null';
$detailJSON = 'null';
$sellerJSON = 'null';
$similarObj = 'null';
$similarJSON = 'null';
detailRequest($itemId);
//var_dump($detailObj);
detailProcess();
similarRequest($itemId);
similarProcess();
var_dump($_POST);
?>

<div class="mainbox">
    <div class="title">
        <p class="title">Product Search</p>
    </div>
    <div class="divideLine"></div>
    <form id="form" name="myform" method="POST">
        <b>Keyword</b>
        <input id="keywordInput" class="keywordInput" type="text" name="keywords" maxlength="255" size="100" value="iPhone" required/>
        <input id="itemId" class="itemId" name="itemId" type="text" maxlength="255" size="100" style="display:none" value="" >
        <br/>
        <b>Category</b>
        <select class="categoryInput" name="category">
            <option id="All" value="All" selected=false>All Categories</option>
            <option id="Art" value="Art" selected=false>Art</option>
            <option id="Baby" value="Baby" selected=false>Baby</option>
            <option id="Books" value="Books" selected=false>Books</option>
            <option id="CSA" value="CSA" selected=false>Clothing, Shoes & Accessories</option>
            <option id="CTN" value="CTN" selected=false>Computer/Tablets & Networking</option>
            <option id="HB" value="HB" selected=false>Health & Beauty</option>
            <option id="Music" value="Music" selected=false>Music</option>
            <option id="VGC" value="VGC" selected="false">Video Games & Consoles</option>
        </select>
        <br/>
        <b>Condition</b>
        <input id="condition1" class="checkbox" type="checkbox" name="condition1" value="New">New
        <input id="condition2" class="checkbox" type="checkbox" name="condition2" value="Used">Used
        <input id="condition3" class="checkbox" type="checkbox" name="condition3" value="Unspecified">Unspecified
        <br/>
        <b>Shipping Options</b>
        <input id="shipping1" class="shipping" type="checkbox" name="shipping1" value="LocalPickup">Local Pickup
        <input id="shipping2" class="shipping" type="checkbox" name="shipping2" value="FreeShipping">Free Shipping
        <br/>
        <input id="nearbySearch" class="nearbySearch" type="checkbox" name="nearbySearch" value="enabled"
               onchange="enableSearch(this)"><b>Enable Nearby Search</b>
        <input id="milesInput" type="text" name="distance" maxlength="255" size="100" placeholder="10" value="10"
               disabled required>
        <b id="milesLabel">miles from</b>
        <input id="hereRadio" type="radio" name="hereRadio" value="Here" checked disabled onclick="clickHere()">
        <id id="hereLabel">Here</id>
        <br>
        <input id="zipRadio" type="radio" name="zipRadio" value="" onclick="clickZip()" disabled>
        <input id="zipInput" type="text" name="zipInput" maxlength="5" placeholder="zipcode" disabled required>
        <br/>
        <input id="submitButton" type="submit" value="Search" disabled>
        <input type="reset" value="Clear">
    </form>

</div>
<div id="divSearch" name="divSearch"></div>
<div id="divDetail" name="divDetail"></div>
<div id="sellerButton" name="sellerButton">
    <p></p>
    <img>
</div>
<div id="divSeller" name="divSeller">
    <iframe id="iframeSeller" name="iframeSeller" ></iframe>
</div>

<div id="similarButton" name="similarButton"></div>
<div id="divSimilar" name="divSimilar"></div>



<script type="text/javascript">
    function loadForm () {
        document.getElementById("All").selected=false;
        document.getElementById("Art").selected=false;
        document.getElementById("Baby").selected=false;
        document.getElementById("Books").selected=false;
        document.getElementById("CSA").selected=false;
        document.getElementById("CTN").selected=false;
        document.getElementById("HB").selected=false;
        document.getElementById("Music").selected=false;
        document.getElementById("VGC").selected=false;
        if (<?php if (isset($form["category"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("<?php if (isset($form["category"])) {echo $form["category"];} else {echo "";}?>").selected=true;
        } else {
            document.getElementById("All").selected=true;
        }
        if (<?php if (isset($form["keywords"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("keywordInput").setAttribute("value", "<?php if (isset($form["keywords"])) {echo $form["keywords"];} else {echo "";}?>") ;
        }
        if (<?php if (isset($form["condition1"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("condition1").setAttribute("checked", "true");
        }
        if (<?php if (isset($form["condition2"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("condition2").setAttribute("checked", "true");
        }
        if (<?php if (isset($form["condition3"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("condition3").setAttribute("checked", "true");
        }
        if (<?php if (isset($form["shipping1"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("shipping1").setAttribute("checked", "true");
        }
        if (<?php if (isset($form["shipping2"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("shipping2").setAttribute("checked", "true");
        }
        if (<?php if (isset($form["nearbySearch"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("nearbySearch").setAttribute("checked", "true");
            enableSearch(document.getElementById("nearbySearch"));
        }
        if (<?php if (isset($form["distance"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("milesInput").setAttribute("value", "<?php if (isset($form["distance"])) {echo $form["distance"];} else {echo "";}?>") ;
        }
        if (<?php if (isset($form["hereRadio"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("hereRadio").setAttribute("checked", "true");
            document.getElementById("zipRadio").checked = false;
        }
        if (<?php if (isset($form["zipRadio"])) {echo "true";} else {echo "false";} ?>) {
            document.getElementById("zipRadio").setAttribute("checked", "true");
            document.getElementById("zipInput").removeAttribute("disabled");
            document.getElementById("hereRadio").checked = false;
        }

     if (<?php if (isset($form["zipInput"])) {echo "true";} else {echo "false";} ?>) {
           document.getElementById("zipInput").setAttribute("value", <?php if (isset($form["zipInput"])) {echo $form["zipInput"];} else {echo "";}?>);
       }
    }
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

            document.getElementById("milesInput").setAttribute("value", "10");
            document.getElementById("hereRadio").checked=true;
            document.getElementById("zipRadio").checked=false;
            document.getElementById("zipInput").setAttribute("value", "");


        }
    }

    function clickHere() {
            document.getElementById("zipInput").setAttribute("disabled", "disabled");
            document.getElementById("hereRadio").setAttribute("checked", "true");
            document.getElementById("zipRadio").checked = false;
    }

    function clickZip() {
            document.getElementById("zipInput").removeAttribute("disabled");
            document.getElementById("zipRadio").setAttribute("checked", "true");
            document.getElementById("hereRadio").checked = false;
    }

    function clickDetail(string) {
        document.getElementById("itemId").value=string;
        document.getElementById("form").submit();
    }
    function loadPage() {
        loadForm();
        drawSearch();
        drawDetail();
    }

    function drawSearch() {
        itemJSON = <?php echo $itemJSON;?>;
        if (itemJSON != null) {
            document.getElementById("divSearch").innerHTML = generateSearchHTML(itemJSON);
        }
    }


    function drawDetail() {
        detailJSON = <?php echo $detailJSON;?>;
        if (detailJSON != null) {
            detailJSON = <?php echo $detailJSON;?>;
            sellerJSON = <?php echo $sellerJSON;?>.Description;
            similarJSON = <?php echo $similarJSON;?>;
            document.getElementById("iframeSeller").srcdoc = sellerJSON;
            document.getElementById("divDetail").innerHTML = generateDetailHTML(detailJSON);
            document.getElementById("divSimilar").innerHTML = generateSimilarHTML(similarJSON);
        }

    }



    function generateSearchHTML(jsonObj) {

        itemId_search = [];

        root = jsonObj.DocumentElement;
        search_text = "<html><head><title></title></head><body style='font-family:Times New Roman'>";
        search_text += "<table id='searchTable' >";
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
                    if (search_item[key] == "N/A") {
                        search_text += "<td>" + "</td>";
                    } else {
                        search_text += "<td><img src='" + search_item[key] + "'></td>";
                    }
                } else if (search_item_keys[j] == "ItemId") {
                    continue;
                } else if (search_item_keys[j] == "Name") {
                    search_text += "<td><p class=detailRefrence onclick=clickDetail(" + search_item["ItemId"] + ")>" + search_item["Name"] + "</p></td>";
                }
                else {
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

    function generateDetailHTML(jsonObj) {



        root = jsonObj.DocumentElement;
        detail_text = "<html><head><title></title></head><body style='font-family:Times New Roman'>";
        detail_text = "<H1 style='text-align:center;margin:auto'>Item Details</H1>";
        detail_text += "<table id='detailTable'>";
        detail_text += "<tbody>";
        // output out the values
        for (i = 0; i < jsonObj.length; i++) //do for all films (one per row)
        {
            detail = jsonObj[i]; //get properties of a film (an object)
                 //start a new row of the output table
            detail_keys = Object.keys(detail);
            for (j = 0; j < detail_keys.length; j++) {
                key = detail_keys[j];
                if (key == 'ItemSpecifics') {
                    for (k = 0; k < detail.ItemSpecifics.NameValueList.length; k++) {
                        detail_text += "<tr>";
                        detail_text += "<td><b>" + detail.ItemSpecifics.NameValueList[k].Name + "</b></td>";
                        detail_text += "<td>" + detail.ItemSpecifics.NameValueList[k].Value + "</td>";
                        detail_text += "</tr>";
                    }
                } else if (key == 'Photo') {
                    if (detail[key] == "N/A") {
                        detail_text += "<tr>";
                        detail_text += "<td><b>" + key + "</b></td>";
                        detail_text += "<td>" + "</td>";
                        detail_text += "</tr>";
                    } else {
                        detail_text += "<tr>";
                        detail_text += "<td><b>" + key + "</b></td>";
                        detail_text += "<td><img src='" + detail[key] + "' style='width:200px'></td>";
                        detail_text += "</tr>";
                    }
                } else if (key == 'Location') {
                    if (detail['Location'] != 'N/A' && detail['PostalCode'] != 'N/A') {
                        detail_text += "<tr>";
                        detail_text += "<td><b>" + "Location" + "</b></td>";
                        detail_text += "<td>" + detail['Location'] +", " + detail['PostalCode'] + "</td>";
                        detail_text += "</tr>";
                    }
                    if (detail['Location'] != 'N/A' && detail['PostalCode'] == 'N/A') {
                        detail_text += "<tr>";
                        detail_text += "<td><b>" + "Location" + "</b></td>";
                        detail_text += "<td>" + detail['Location'] + "</td>";
                        detail_text += "</tr>";
                    }
                } else {
                    if (detail[key] != 'N/A') {
                        detail_text += "<tr>";
                        detail_text += "<td><b>" + key + "</b></td>";
                        detail_text += "<td>" + detail[key] + "</td>";
                        detail_text += "</tr>";
                    }
                }
            }
        }
        detail_text += "</tbody>";
        detail_text += "</table>";
        detail_text += "</body></html>";
        return detail_text;
    }

    function generateSimilarHTML(jsonObj) {
        root = jsonObj.DocumentElement;
        similar_text = "<html><head><title></title></head><body style='font-family:Times New Roman'>";
        similar_text += "<table id='similarTable'>";
        similar_text += "<tbody>";
        // output out the values
        similar_text += "<tr>";
        for (i = 0; i < jsonObj.length; i++) //do for all films (one per row)
        {
            similar = jsonObj[i]; //get properties of a film (an object)
            //start a new row of the output table
            similar_text += "<td>";

            if (similar['Photo'] != 'N/A') {
                similar_text += "<img src='" + similar['Photo'] + "' style='width:200px' class=detailRefrence onclick=clickDetail(" + similar["ItemID"] + ")><br>";
            }
            if (similar['Title'] != 'N/A') {
                similar_text += "<p class=detailRefrence onclick=clickDetail(" + similar["ItemID"] + ")>" + similar['Title'] + "</p>";
            }
            similar_text += "</td>";

        }
        similar_text += "</tr>";
        similar_text += "<tr>";
        for (i = 0; i < jsonObj.length; i++) //do for all films (one per row)
        {
            similar = jsonObj[i]; //get properties of a film (an object)
            //start a new row of the output table
            similar_text += "<td>";
            similar_text += similar['Price'];
            similar_text += "</td>";

        }
        similar_text += "</tr>";
        similar_text += "</tbody>";
        similar_text += "</table>";
        similar_text += "</body></html>";
        return similar_text;
    }
</script>

</body>

</html>