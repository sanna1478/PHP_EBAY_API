<!DOCTYPE html>
<html>
    <head>
        <style>
            form {
                border-width: 3px;
                border-style: solid;
                border-color: #C0C0C0;
                background-color: #fff7f8;
                margin: 0 auto;
                width: 600px
            }
            h1.title_header {
                margin: 0;
                font-family: serif;
                font-weight: 200;
                border-bottom-width: 2px;
                border-bottom-style: solid;
                border-bottom-color: #C0C0C0;
                width: 97%;
            }

            div.form_input_container {
                padding-left: 20px;
                padding-top: 10px;
            }

            div.error_output_container{
                text-align: center;
                background-color: #F0F0F0;
                border-style: solid;
                border-color: #DCDCDC;
                border-width: 2px;
                width: 700px;
            }

            div.form_button_container {
                padding-left: 20px;
                padding-top: 10px;
                padding-bottom: 20px;
                text-align: center;
            }

            p.error_message{
                margin: 0 auto;
            }

        </style>
    </head>
    <body onload = "getZip()">
        <?php
            $ebay_req_made = false;
            $keywordErr = $milesErr = $zip_codeErr = "";
            $keyword = $miles = $zip_code = $zip_code_here = "";
            $nearby_set = false;
            $zip_code_valid = false;
            $keyword_valid = false;
            $miles_valid = false;
            $all_form_data_valid = false;

            if($_SERVER["REQUEST_METHOD"] == "POST"){
                // Checking if a keyword entry is not empty
                if(empty($_POST["keyword"])){
                    $keywordErr = "Keyword required";
                } else {
                    $keyword = $_POST["keyword"];
                    $keyword_valid = true;
                }

                // Checking all the inputs if "Enable Nearby Search" checkbox is checked
                if(isset($_POST["near"])){
                    $nearby_set = true;
                    // Ensure miles has valid data
                    if(empty($_POST["miles"])){
                        $milesErr = "Miles required";
                    } else {
                        $miles = $_POST["miles"];
                        // REGEX the input for miles
                        $regex_miles = "/^[0-9]{1,6}$/";
                        if(preg_match($regex_miles,$miles)){
                            $miles_valid = true;
                        }
                    }
                    // Check that zip code is valid if zip code button is pressed
                    if(isset($_POST["zip"])){
                        if(empty($_POST["zip_code"])){
                            $zip_codeErr = "Require zip code";
                        } else {
                            $zip_code = $_POST["zip_code"];
                            // REGEX the input for zip_code
                            $regex_zip_code = "/^[0-9]{5,5}$/";
                            if(preg_match($regex_zip_code,$zip_code)){
                                $zip_code_valid = true;
                            }
                        }
                    }
                    if(isset($_POST['here'])){
                        $zip_code_here = $_POST['here'];
                        $zip_code_valid = true;
                    }
                }
            }

            if(($zip_code_valid && $miles_valid && $keyword_valid)){
                $all_form_data_valid = true;
            }
            if($keyword_valid && !$nearby_set){
                $all_form_data_valid = true;
            }
         ?>


        <div>
            <form action="" method="POST">
                <div align="center" id="title">
                    <h1 align="center" class="title_header"><i>Product Search</i></h1>
                </div>
                <div class="form_input_container">
                    <b>Keyword</b>
                    <input required type="text" name="keyword" id="keyword_input"
                        value="<?php echo $keyword;?>">
                </div>
                <div class="form_input_container">
                    <b>Category</b>
                    <select name="category" id="cat_selection">
                        <option value="0" <?php if($_POST['category']=="0")
                        echo 'selected="selected"';?>>All Categories</option>
                        <option value="550" <?php if($_POST['category']=="550")
                        echo 'selected="selected"';?>>Art</option>
                        <option value="2984" <?php if($_POST['category']=="2984")
                        echo 'selected="selected"';?>>Baby</option>
                        <option value="267" <?php if($_POST['category']=="267")
                        echo 'selected="selected"';?>>Books</option>
                        <option value="11450" <?php if($_POST['category']=="11450")
                        echo 'selected="selected"';?>>Clothing</option>
                        <option value="58058" <?php if($_POST['category']=="58058")
                        echo 'selected="selected"';?>> Shoes & Accessories</option>
                        <option value="26395" <?php if($_POST['category']=="26395")
                        echo 'selected="selected"';?>>Computer/Tablets & Netoworking</option>
                        <option value="11233" <?php if($_POST['category']=="11233")
                        echo 'selected="selected"';?>>Health & Beauty</option>
                        <option value="1249" <?php if($_POST['category']=="1249")
                        echo 'selected="selected"';?>>Music and Video Games & Consoles</option>
                    </select>
                </div>
                <div class="form_input_container">
                    <b style="padding-right: 20px">Condition</b>
                    <input type="checkbox" <?php if(isset($_POST['new'])) echo "checked='checked'";?> name="new" id="new_checkbox">
                        <label style="padding-right: 20px">
                            New
                        </label>
                    <input type="checkbox" <?php if(isset($_POST['used'])) echo "checked='checked'";?> name="used" id="used_checkbox">
                        <label style="padding-right: 20px">
                            Used
                        </label>
                    <input type="checkbox" <?php if(isset($_POST['unknown'])) echo "checked='checked'";?> name="unknown" id="unknown_checkbox">
                        <label style="padding-right: 20px">
                            Unspecified
                        </label>
                </div>
                <div class="form_input_container">
                    <b style="padding-right: 45px">Shipping Options</b>

                    <input type="checkbox" <?php if(isset($_POST['pickup'])) echo "checked='checked'";?> name="pickup" id="pickup_checkbox">
                        <label style="padding-right: 45px">
                            Local Pickup
                        </label>

                    <input type="checkbox" <?php if(isset($_POST['free_ship'])) echo "checked='checked'";?> name="free_ship" id="free_ship_checkbox">
                        <label>
                            Free Shipping
                        </label>
                </div>
                <div class="form_input_container">
                    <table style="display: inline">
                        <tr>
                            <td rowspan="2" valign="top">
                                <input type="checkbox" name="near" id="near_me" onclick="enable_inputs()"
                                    <?php if(isset($_POST['near'])) echo "checked='checked'";?>>
                                <label style="padding-right: 20px">
                                    <b>Enable Nearby Search</b>
                                </label>

                                <input required type="text" name="miles" size="6" id="dist"
                                <?php if(!isset($_POST['near'])) echo "disabled value='10'"?>
                                <?php if(isset($_POST['miles'])) echo "value=$miles";?>>
                                <?php if(isset($_POST['near'])){?>
                                    <label style="padding-left: 5px;opacity:1.0" id="miles_label">
                                <?php } else { ?>
                                    <label style="padding-left: 5px;opacity:0.5" id="miles_label">
                                <?php } ?>
                                    <b>miles from</b>
                                </label>
                            </td>
                            <td>
                                <input type="radio" name="here" id="here_radio"
                                    onclick="toggle_here_radio()"
                                    <?php if(!isset($_POST['near'])) echo "disabled checked='checked'"; ?>
                                    <?php if(isset($_POST['here'])) echo "checked='checked'" ?>>

                                    <?php if(isset($_POST['near'])) {?>
                                        <label style="opacity:1.0" id="here_label">
                                    <?php } else {?>
                                        <label style="opacity:0.5" id="here_label">
                                    <?php }?>
                                        Here
                                    </label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="radio" name="zip" id="zip_radio"
                                    onclick="toggle_zip_radio()"
                                    <?php if(empty($_POST['near'])) echo "disabled";?>
                                    <?php if(isset($_POST['zip'])) echo "checked='checked'";?>>

                                <input required type="text" name="zip_code"
                                    placeholder="zip code" id="zip_text"
                                    <?php if(empty($_POST['near'])) echo "disabled";?>
                                    <?php if(empty($_POST['zip'])) echo "disabled";?>
                                    value = <?php echo $zip_code ?>>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="form_button_container">
                    <input type="submit" name="submit" value="Search"/>
                    <input type="button" name="clear_form" value="Clear"
                        onclick="clear_data(this.form)"/>
                </div>
            </form>
        </div>


        <?php //Output of the REGEX CHECK
            if(!$zip_code_valid && $nearby_set){
                echo "<div align='center' style='padding-top:30px' id='zip_err'>
                        <div class='error_output_container'>
                            <p class='error_message'>Zipcode is invalid</p>
                        </div>
                    </div>";
            } else {
                echo "";
            }

            if(!$miles_valid && $nearby_set){
                echo "<div align='center' style='padding-top:30px' id='miles_err'>
                        <div class='error_output_container'>
                            <p class='error_message'>miles value is invalid</p>
                        </div>
                    </div>";
            } else {
                echo "";
            }
        ?>

        <?php
            if($all_form_data_valid){
                $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';
                $version = '1.0.0';
                $appid = 'Shreyash-productS-PRD-355b66fda-57ed6986';
                $query = $keyword;
                $safequery = urlencode($query);

                //Construct the findItemsKeywords HTTP GET call
                $apicall = "$endpoint?";
                $apicall .= "OPERATION-NAME=findItemsAdvanced";
                $apicall .= "&SERVICE-VERSION=$version";
                $apicall .= "&SECURITY-APPNAME=$appid";
                $apicall .= "&keywords=$safequery";
                $apicall .= "&paginationInput.entriesPerPage=20";
                $apicall .= "&RESPONSE-DATA-FORMAT=JSON";
                $apicall .= "&REST-PAYLOAD";
                if($nearby_set){
                    if(isset($_POST['here'])){
                        $apicall .= "&buyerPostalCode=$zip_code_here";
                    }
                    if(isset($_POST['zip'])){
                        $apicall .= "&buyerPostalCode=$zip_code";
                    }
                }
                if(!$_POST['category'] == 0){
                    $cat_id = $_POST['category'];
                    $apicall .= "&categoryId=$cat_id";
                }
                $index_counter = 0;
                if($nearby_set){
                    $apicall .= "&itemFilter($index_counter).name=MaxDistance";
                    $apicall .= "&itemFilter($index_counter).value=$miles";
                    $index_counter+=1;
                }
                if(isset($_POST['pickup'])){
                    $apicall .= "&itemFilter($index_counter).name=LocalPickupOnly";
                    $apicall .= "&itemFilter($index_counter).value=true";
                    $index_counter+=1;
                }
                if(isset($_POST['free_ship'])){
                    $apicall .= "&itemFilter($index_counter).name=FreeShippingOnly";
                    $apicall .= "&itemFilter($index_counter).value=true";
                }
                if(isset($_POST['new'])||isset($_POST['used'])||isset($_POST['unknown'])){
                    $apicall .= "&itemFilter($index_counter).name=Condition";
                    $value_index = 0;
                    if(isset($_POST['new'])){
                        $apicall .= "&itemFilter($index_counter).value($value_index)=New";
                        $value_index += 1;
                    }
                    if(isset($_POST['used'])){
                        $apicall .= "&itemFilter($index_counter).value($value_index)=Used";
                        $value_index += 1;
                    }
                    if(isset($_POST['unknown'])){
                        $apicall .= "&itemFilter($index_counter).value($value_index)=Unspecified";
                        $value_index += 1;
                    }
                }
                // Make a request to eBay api
                $resp = file_get_contents($apicall);
                // Flag so we know when to run the parser in javascript tag
                $ebay_req_made = true;

            }
         ?>


        <script type="text/javascript">
            var ebay_req_made = "<?php echo $ebay_req_made;?>";
            if(ebay_req_made){
                parseResult();
            }

            function clear_data(data){
                var keyword_input = document.getElementById("keyword_input");
                var dist_text = document.getElementById("dist");
                var zip_text = document.getElementById("zip_text");
                var here_label = document.getElementById("here_label");
                var miles_label = document.getElementById("miles_label");
                var found_mssg = document.getElementById("noResult");
                var zip_err_mssg = document.getElementById("zip_err");
                var miles_err_mssg = document.getElementById("miles_err");
                var data_table = document.getElementById("dataTable");

                miles_label.style.opacity = "0.5";
                here_label.style.opacity = "0.5";
                keyword_input.value = "";
                dist_text.value = "10";
                dist_text.disabled = true;
                zip_text.disabled = true;
                zip_text.value = "";
                zip_text.placeholder = "zip code";

                if(zip_err_mssg){
                    zip_err_mssg.parentNode.removeChild(zip_err_mssg);
                }
                if(miles_err_mssg){
                    miles_err_mssg.parentNode.removeChild(miles_err_mssg);
                }
                if(found_mssg){
                    found_mssg.parentNode.removeChild(found_mssg);
                }
                if(data_table){
                    data_table.parentNode.removeChild(data_table);
                }
                var form_elements = data.elements;
                for(idx=0; idx<form_elements.length; idx++){
                    field_type = form_elements[idx].type.toLowerCase();
                    switch(field_type){
                        case "checkbox":
                            form_elements[idx].checked = false;
                            break;
                        case "radio":
                            if(form_elements[idx].name == "here"){
                                form_elements[idx].checked = true;
                                form_elements[idx].disabled = true;
                            }
                            if(form_elements[idx].name == "zip"){
                                form_elements[idx].checked = false;
                                form_elements[idx].disabled = true;
                            }
                            break;
                        case "select-one":
                        case "select-muli":
                            form_elements[idx].selectedIndex = 0;
                            break;
                        default:
                            break;
                    }
                }
            }
            function enable_inputs(){
                nearby_checkbox = document.getElementById("near_me");
                miles_textbox = document.getElementById("dist");
                zip_radio = document.getElementById("zip_radio");
                here_radio = document.getElementById("here_radio");
                zip_text = document.getElementById("zip_text");
                here_label = document.getElementById("here_label");
                miles_label = document.getElementById("miles_label");

                if(nearby_checkbox.checked){
                    miles_textbox.disabled = false;
                    zip_radio.disabled = false;
                    here_radio.disabled = false;
                    miles_label.style.opacity = "1.0";
                    here_label.style.opacity = "1.0";
                }
                if(!nearby_checkbox.checked){
                    miles_textbox.disabled = true;
                    zip_radio.disabled = true;
                    zip_text.disabled = true;
                    here_radio.disabled = true;
                    miles_label.style.opacity = "0.5";
                    here_label.style.opacity = "0.5";
                    zip_text.value = "";
                    zip_text.placeholder = "zip code";
                    miles_textbox.value = "10";
                    here_radio.checked = true;
                    zip_radio.checked = false;
                }
            }
            function toggle_zip_radio(){
                zip_radio = document.getElementById("zip_radio");
                zip_text = document.getElementById("zip_text");
                here_radio = document.getElementById("here_radio");
                if(zip_radio.checked){
                    here_radio.checked = false;
                    zip_text.disabled = false;
                }
            }
            function toggle_here_radio(){
                zip_radio = document.getElementById("zip_radio");
                zip_text = document.getElementById("zip_text");
                here_radio = document.getElementById("here_radio");
                if(here_radio.checked){
                    zip_radio.checked = false;
                    zip_text.disabled = true;
                }
            }
            function getZip(){
                // Disable search button
                search = document.getElementsByName("submit");
                search.disabled = true;
                var url = 'http://ip-api.com/json/?fields=zip';
                xmlhttp = new XMLHttpRequest();
                xmlhttp.open("GET",url,false);
                xmlhttp.send();
                zip_jsonObj = JSON.parse(xmlhttp.responseText);
                here_zip_code = zip_jsonObj.zip;
                document.getElementById("here_radio").value = here_zip_code;
                // Re-enable search button
                search.disabled = false;
            }
            function parseResult(){
                req_json_data = JSON.parse(<?php echo json_encode($resp);?>);
                findItemsAdvResp = req_json_data.findItemsAdvancedResponse;
                if(findItemsAdvResp[0].ack[0] == "Success"){
                    searchResult = findItemsAdvResp[0].searchResult[0];
                    // TODO: Check if any valid results
                    if(searchResult.hasOwnProperty("item")){
                        itemList = searchResult.item;
                        generateTable(itemList);
                    } else {
                        notFoundMssg();
                    }
                } else {
                    //TODO: Failure message and reason for failure
                }
            }
            function generateTable(itemList){
                var numItem = itemList.length;
                tableHeader = ['Index','Photo','Name','Price','Zip code','Condition','Shipping Options']

                // Create dynamic table
                var table = document.createElement('table');
                table.setAttribute('id', 'dataTable');
                table.style.margin = "30px auto";
                table.style.border = "2px solid #C0C0C0";
                table.style.borderCollapse = "collapse";
                table.style.width = "80%";
                var tr = table.insertRow(-1);
                tr.style.border = "2px solid #C0C0C0";
                tr.style.borderCollapse = "collapse";

                for(var header=0; header<tableHeader.length; header++){
                    var th = document.createElement('th');
                    th.style.border = "2px solid #C0C0C0";
                    th.style.borderCollapse = "collapse";
                    th.innerHTML = tableHeader[header];
                    tr.appendChild(th);
                }

                for(var itemNum=0; itemNum<itemList.length;itemNum++){
                    item = itemList[itemNum];
                    tr = table.insertRow(-1);
                    tr.style.border = "2px solid #C0C0C0";
                    tr.style.borderCollapse = "collapse";
                    for(var header = 0; header<tableHeader.length; header++){
                        var td = document.createElement('td');
                        td = tr.insertCell(-1);
                        if(tableHeader[header] == 'Index'){
                            td.innerHTML = itemNum+1;
                        }
                        if(tableHeader[header] == 'Photo'){
                            if(item.hasOwnProperty("galleryURL")){
                                td.style.width = "100px";
                                td.style.height = "100%";
                                td.style.paddingBottom = "0";
                                td.style.margin = "0"
                                td.innerHTML = "<img style='vertical-align:bottom' src="+item.galleryURL[0]+" width='100%' height='40%'"+">";
                            } else {
                                td.innerHTML = "N/A";
                            }
                        }
                        if(tableHeader[header] == 'Name'){
                            if(item.hasOwnProperty("title")){
                                td.innerHTML = item.title[0];
                            }
                            else {
                                td.innerHTML = "N/A";
                            }
                        }
                        if(tableHeader[header] == 'Price'){
                            if(item.hasOwnProperty("sellingStatus")){
                                sellingStat = item.sellingStatus[0];
                                if(sellingStat.hasOwnProperty("currentPrice")){
                                    currPrice = sellingStat.currentPrice[0];
                                    value = currPrice.__value__;
                                    td.innerHTML = "$"+value;
                                } else {
                                    td.innerHTML = "N/A";
                                }
                            } else {
                                td.innerHTML = "N/A";
                            }
                        }
                        if(tableHeader[header] == 'Zip code'){
                            if(item.hasOwnProperty("postalCode")){
                                td.innerHTML = item.postalCode[0];
                            } else {
                                td.innerHTML = "N/A";
                            }
                        }
                        if(tableHeader[header] == 'Condition'){
                            if(item.hasOwnProperty("condition")){
                                condition = item.condition[0]
                                if(condition.hasOwnProperty("conditionDisplayName")){
                                    cond_name = condition.conditionDisplayName[0];
                                    td.innerHTML = cond_name;
                                } else {
                                    td.innerHTML = "N/A";
                                }
                            } else {
                                td.innerHTML = "N/A";
                            }
                        }
                        if(tableHeader[header] == 'Shipping Options'){
                            if(item.hasOwnProperty("shippingInfo")){
                                shippingInfo = item.shippingInfo[0];
                                if(shippingInfo.hasOwnProperty("shippingServiceCost")){
                                    shippingCost = shippingInfo.shippingServiceCost[0];
                                    if(shippingCost.__value__ == "0.0"){
                                    td.innerHTML = "Free Shipping";
                                    } else {
                                        td.innerHTML = "$"+shippingCost.__value__;
                                    }
                                } else {
                                    td.innerHTML = "N/A";
                                }
                            } else {
                                td.innerHTML = "N/A";
                            }
                        }
                        td.style.border = "2px solid #C0C0C0";
                        td.style.borderCollapse = "collapse";
                    }
                }
                document.body.appendChild(table);
            }
            function notFoundMssg(){
                var main_div = document.createElement('div');
                var mssg_div = document.createElement('div');
                var mssg = document.createElement('p');

                main_div.setAttribute('id','noResult');
                main_div.setAttribute('align','center');
                main_div.style.paddingTop = "30px";
                mssg_div.className = "error_output_container";
                mssg.className = "error_message";

                mssg.innerHTML = "No Records has been found"
                mssg_div.appendChild(mssg);
                main_div.appendChild(mssg_div);
                document.body.appendChild(main_div);

            }
        </script>
    </body>
</html>
