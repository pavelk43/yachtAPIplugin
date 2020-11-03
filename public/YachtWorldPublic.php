    <?php

    class YachtWorldPublic
    {

        private $plugin_name = "my-setting-yacht";
        private $id_number;
        private $id_number_2;
        private $page_size;
        public $descriptionLength=250;
        public $startDate;


        public function GetPageSize(){
            $options = get_option($this->plugin_name);
            $this->page_size = $options['page_size'];
            return $this->page_size;
        }


        public function GET_BROKER_JSON_output($type, $status){


            $this->GetPageSize();
            $options = get_option($this->plugin_name);
            $this->id_number = $options['id_number'];
            $this->id_number_2 = $options['id_number_2'];
            $this->page_size = $options['page_size'];
            
            $modified = $this->startDate;

            $pageSize = $this->page_size;

            if(isset($_GET["ps"]) && $_GET["ps"] !=""){
                $this->page_size = $_GET["ps"]-0;
                $pageSize = $_GET["ps"]-0;
            }

            $currentPage = 0;
            if( isset( $_GET["boatPage"] ) ){
                $currentPage = $_GET["boatPage"]-0;   
            }

            $offset ="";
            $query = "";
           

           // $query .="&status=Active";

            $query .="&status=" . $status;

            if(isset($_GET["keyword"]) && $_GET["keyword"]!=""){
                $query .= "&AdvancedKeywordSearch=" . $_GET["keyword"];
            }
            if($currentPage>0){
                $offset="&offset=". (($currentPage-1)*$pageSize);
            }

            if(isset($_GET["type"]) && $_GET["type"]!=""){
                $query .= '&category=' . $_GET["type"];
            }elseif($type !=""){
                $query .= '&category=' . $type;
            }



            if(isset($_GET["hmid"]) &&$_GET["hmid"]!=""){
                $query .= '&hull=' . $_GET["hmid"];
            }
            if(isset($_GET["enid"]) && $_GET["enid"]!=""){
                $query .= '&engines=' . $_GET["enid"];
            }
            if(isset($_GET["ftid"]) && $_GET["ftid"]!=""){
                $query .= '&fuel=' . $_GET["ftid"];
            }
            if(isset($_GET["cond"]) && $_GET["cond"]!=""){
                $query .= '&condition=' . $_GET["cond"];
            }
            if(isset($_GET["rid"]) && $_GET["rid"]!=""){
                $query .= '&country=' . $_GET["rid"];
            }


            if(isset($_GET["sort"]) && $_GET["sort"]!=""){
                $sort = '&sort=' . $_GET["sort"];

                if(isset($_GET["direction"]) && $_GET["direction"]!=""){
                    $sort  .= "|". $_GET["direction"];
                }
            }else{
                 $sort="&sort=Price|desc";
            }


            if(isset($_GET["makestring"]) && $_GET["makestring"]!=""){
                $query.="&makestring=" . $_GET["makestring"];
            }


            if(isset($_GET["msint"]) && $_GET["msint"]!=""){

                $newDate = (new DateTime());
                $dateAdjust = date_add($newDate, date_interval_create_from_date_string('-'. $_GET["msint"] .' days'));
                $searchDate = date_format($dateAdjust, 'Y-m-d');
                $query .= '&modified=' . $searchDate;
            }



            //modified

            if(isset($_GET["price-min"])){
                if($_GET["price-min"] < 500000){
                    $price_min = 500000;    
                }else{
                    $price_min = $_GET["price-min"];
                }
            }

            if(isset($_GET["price-max"])){
                if($_GET["price-max"] > 75000000){
                    $price_max = 75000000;    
                }else{
                    $price_max = $_GET["price-max"];
                }
            }

            if(isset($_GET["price-min"]) && isset($_GET["price-max"]) && $_GET["price-min"] != "" && $_GET["price-max"] ){
                $query.="&price=" . $price_min . ":" . $price_max."|USD";
            }elseif(isset($_GET["price-min"]) && $_GET["price-min"] != ""){
                $query.="&price=" . $price_min.":75000000|USD";
            }elseif(isset($_GET["price-max"]) && $_GET["price-max"] != ""){
                $query.="&price=500000:" .  $price_max."|USD";
            }

            if(isset($_GET["length-min"])){
                if($_GET["length-min"] < 45){
                    $length_min = 45;    
                }else{
                    $length_min = $_GET["length-min"];
                }
            }

            if(isset($_GET["length-max"])){
                if($_GET["length-max"] > 100){
                    $length_max = 100;    
                }else{
                    $length_max = $_GET["length-max"];
                }
            }

            if(isset($_GET["length-min"]) && isset($_GET["length-max"])  && $_GET["length-min"] != "" && $_GET["length-max"]){
                $query.="&length=" . $length_min . ":" . $length_max. "|feet";
            }elseif( isset($_GET["length-min"]) && $_GET["length-min"] != ""){
                $query.="&length=" . $length_min. ":100|feet";
            }elseif(isset($_GET["length-max"]) && $_GET["length-max"] != ""){
                $query.="&length=45:" .  $length_max. "|feet";
            }

            if(isset($_GET["year-min"])){
                if($_GET["year-min"] < 2010){
                    $year_min = 2010;    
                }else{
                    $year_min = $_GET["year-min"];
                }
            }

            if(isset($_GET["year-max"])){
                if($_GET["year-max"] > date("Y") ){
                    $year_max = date("Y") -1;    
                }else{
                    $year_max = $_GET["year-max"];
                }
            }

            if(isset($_GET["year-min"]) && isset($_GET["year-max"]) &&  $_GET["year-min"] != "" && $_GET["year-max"]){
                $query.="&year=" . $year_min . ":" . $year_max;
            }elseif(isset($_GET["year-min"]) && $_GET["year-min"] != ""){
                $query.="&year=" . $year_min.":".(date("Y")-1);
            }elseif( isset($_GET["year-max"]) && $_GET["year-max"] != ""){
                $query.="&year=2010:" .  $year_max;
            }


            if($modified!=""){
                $query .= '&modified=' . $modified;
            }
            //https://services.boats.com/0a7cb34ea52a/pls/boats/search?fields=MakeString,ModelYear,price,YachtWorldID,BoatLocation,PartyID,DocumentID&AdvancedKeywordSearch=New

            $JSON_Query = 'https://services.boats.com/' . $this->id_number_2 . '/pls/boats/search?fields='
                .'NominalLength,AdditionalDetailDescription,NumberOfEngines,TotalEnginePowerQuantity,DesignerName,BuilderName,'
                . 'BoatHullMaterialCode,Engines,SalesRep,Office,GeneralBoatDescription,BoatCategoryCode,SalesStatus,IMTTimeStamp,'
                . 'NormNominalLength,MakeString,ModelYear,price,YachtWorldID,Type,BoatLocation,PartyID,DocumentID,BoatName,'
                . 'LengthOverall,Model,NormPrice,Images,BeamMeasure,BeamMeasure,MaxDraft,BridgeClearanceMeasure,HasBoatHullID,CruisingSpeedMeasure,RangeMeasure,MaximumSpeedMeasure,FuelTankCapacityMeasure,WaterTankCapacityMeasure,HoldingTankCapacityMeasure,ExternalLink'
                . str_replace(" ","+", $query) . $sort  . '&rows=' . $this->page_size . $offset;

            //print $JSON_Query;


            $request = wp_remote_get($JSON_Query);
            $json = wp_remote_retrieve_body( $request );        
            $obj = json_decode($json);
            
            if($obj != NULL){            
                return $obj->data;    
            }
            
            return; 
        }

        public function GET_JSON_output($type, $status)
        {
            $this->GetPageSize();
            $options = get_option($this->plugin_name);
            $this->id_number = $options['id_number'];
            $this->id_number_2 = $options['id_number_2'];
            $this->page_size = $options['page_size'];

            $pageSize = $this->page_size;

            if($_GET["ps"]!=""){
                $this->page_size = $_GET["ps"]-0;
                $pageSize = $_GET["ps"]-0;
            }

            $currentPage = $_GET["boatPage"]-0;

            $offset ="";
            $query = "";
            $sort="&sort=Price|desc";


            if($_GET["keyword"]!=""){
                $query .= "&AdvancedKeywordSearch=" . $_GET["keyword"];
            }
            if($currentPage>0){
                $offset="&offset=". (($currentPage-1)*$pageSize);
            }

            $query .="&SalesStatus=" . $status;

    //print_r($status . "****");
          //  $query .= "&OptionActiveIndicator=on";

            if($_GET["type"]!=""){
                $query .= '&category=' . $_GET["type"];
            }elseif($type !=""){
                $query .= '&category=' . $type;
            }



            if($_GET["hmid"]!=""){
                $query .= '&hull=' . $_GET["hmid"];
            }
            if($_GET["enid"]!=""){
                $query .= '&engines=' . $_GET["enid"];
            }
            if($_GET["ftid"]!=""){
                $query .= '&fuel=' . $_GET["ftid"];
            }
            if($_GET["cond"]!=""){
                $query .= '&condition=' . $_GET["cond"];
            }
            if($_GET["rid"]!=""){
                $query .= '&country=' . $_GET["rid"];
            }

            if($_GET["msint"]!=""){

                $newDate = (new DateTime());
                $dateAdjust = date_add($newDate, date_interval_create_from_date_string('-'. $_GET["msint"] .' days'));
                $searchDate = date_format($dateAdjust, 'Y-m-d');
                $query .= '&modified=' . $searchDate;
            }

            //modified



            if($_GET["price-min"] != "" && $_GET["price-max"]!=""){
                $query.="&price=" . $_GET["price-min"] . ":" . $_GET["price-max"];
            }elseif($_GET["price-min"] != ""){
                $query.="&price=" . $_GET["price-min"];
            }elseif($_GET["price-max"] != ""){
                $query.="&price=" .  $_GET["price-max"];
            }

            if($_GET["length-min"] != "" && $_GET["length-max"]!=""){
                $query.="&length=" . $_GET["length-min"] . ":" . $_GET["length-max"] . "|feet";
            }elseif($_GET["length-min"] != ""){
                $query.="&length=" . $_GET["length-min"]. "|feet";
            }elseif($_GET["length-max"] != ""){
                $query.="&length=" .  $_GET["length-max"]. "|feet";
            }

            if($_GET["year-min"] != "" && $_GET["year-max"]!=""){
                $query.="&year=" . $_GET["year-min"] . ":" . $_GET["year-max"];
            }elseif($_GET["year-min"] != ""){
                $query.="&year=" . $_GET["year-min"];
            }elseif($_GET["year-max"] != ""){
                $query.="&year=" .  $_GET["year-max"];
            }


              if($_GET["sort"]!=""){
                $sort = '&sort=' . $_GET["sort"];

                if($_GET["direction"]!=""){
                    $sort  .= "|". $_GET["direction"];
                }
            }else{
                 $sort="&sort=Price|desc";
            }


            if($_GET["make"]!=""){
                $query.="&make=" . $_GET["make"];
            }




            $JSON_Query = 'http://api.boats.com/inventory/search?key=' . $this->id_number . str_replace(" ","+", $query) . $sort  . '&rows=' . $this->page_size . $offset;

               print_r($JSON_Query);

            $request = wp_remote_get($JSON_Query);
            $json = wp_remote_retrieve_body( $request );
            $obj = json_decode($json);
            return $obj;
        }

       public function GET_SingleBoat_JSON_output($boatId)
        {
            $options = get_option($this->plugin_name);
            $this->id_number = $options['id_number'];
            $request = wp_remote_get('http://api.boats.com/inventory/' . $boatId .'?key=' . $this->id_number);
            $json = wp_remote_retrieve_body( $request );

            $obj = json_decode($json);
            return $obj;
        }


        public function GET_SingleBoatBroker_JSON_output($boatId)
        {
            $options = get_option($this->plugin_name);
            $this->id_number_2 = $options['id_number_2'];


            $JSON_Query = 'https://services.boats.com/' . $this->id_number_2 . '/pls/boats/search?fields='
                .'NominalLength,AdditionalDetailDescription,NumberOfEngines,TotalEnginePowerQuantity,DesignerName,BuilderName,'
                . 'BoatHullMaterialCode,Engines,SalesRep,Office,GeneralBoatDescription,BoatCategoryCode,SalesStatus,IMTTimeStamp,'
                . 'NormNominalLength,MakeString,ModelYear,price,IsAvailableForPls,SaleClassCode,DriveTypeCode,YachtWorldID,BoatLocation,PartyID,DocumentID,BoatName,LengthOverall,Model,NormPrice,Images,BeamMeasure,BeamMeasure,MaxDraft,BridgeClearanceMeasure,HasBoatHullID,CruisingSpeedMeasure,RangeMeasure,MaximumSpeedMeasure,FuelTankCapacityMeasure,WaterTankCapacityMeasure,HoldingTankCapacityMeasure,ExternalLink';

            $JSON_Query .= "&DocumentID=" . $boatId;

           // $json = wp_remote_get($JSON_Query);

            $request = wp_remote_get($JSON_Query);
            $json = wp_remote_retrieve_body( $request );

            $obj = json_decode($json);
            if($obj != NUll ){
                return $obj->data;    
            }

            return;        
            
        }


        public function GetSingleBoatHtml($json, $isBroker){

            setlocale(LC_MONETARY, 'en_US.utf8');
            $_output = "";
            if($json != NULL ){
                foreach($json->results as $boat){

                    $image = $boat->Images[0];

                    $secureImg = str_replace("http://", "https://", $image->Uri);

                    $_output .= "<div class='listing-details'>"
                        .  "<div class='left-cell'><div class='listing-detail-header'>";

                    $_output .= "<h2 class='listing-title'>" . $boat->ModelYear." ". $boat->MakeString . " " . $boat->Model . "</h2>
                            <ul class='listing-meta'>
                                <li class='ft'>". $boat->LengthOverall ."</li>
                                <li class='cat'>". $boat->BoatCategoryCode ."</li>
                                <li class='loc'>". $boat->BoatLocation->BoatCityName . ", ". $boat->BoatLocation->BoatCountryID ."</li>
                            </ul>
                            </div></div>" ;

                    $_output .= "<div class='right-cell'><div class='salesprice'><h3>YACHT PRICE : </h3><h1>" . ($boat->NormPrice != "" && $boat->NormPrice != 0? "$"
                            . str_replace(".00","", str_replace("USD ","", $boat->NormPrice)): "") . "</h1></div></div>";


                    $_output.= "<div class='left-cell'>"
                        . "<div class='listing-img'><img src='". $secureImg . "'/></div>";

                    $_output .= "<div class='yacht-description-wrap'><ul class='yacht-description-nav'>";

                    $_output .= "<li><a class='yw-tablinks active' onclick='openListing(event)' tab-desc='overview'>Overview</a></li>
                                <li><a class='yw-tablinks' onclick='openListing(event)'  tab-desc='specifications'>Specifications</a></li>
                                <li><a class='yw-tablinks' onclick='openListing(event)' tab-desc='gallery'>Gallery</a></li>
                                <li><a class='yw-tablinks' onclick='openListing(event)'  tab-desc='descriptions'>Descriptions</a></li>
                                <li><a class='yw-tablinks' onclick='openListing(event)' tab-desc='links'>Links</a></li>";

                    $_output .= "</ul><div class='yacht-description'><h3 class='title'>Overview</h3>
                                <div id='overview' class='yw-tabcontent'>";
                        /*Overview Content*/
                        foreach($boat-> GeneralBoatDescription as $g){
                            $_output.= "<div style='padding-top:14px;'>" . str_replace("center","left", strip_tags(preg_replace('/\s\s+/', '<br/>', $g),"<p><br><br/>")) . "</div>";
                        }

                    $_output .= "</div><h3 class='title'>Specifications</h3><div id='specifications' class='yw-tabcontent'>";
                    /*Description Content*/
                        $_output .= "<h2>BASIC INFORMATION</h2>
                                    <div class='specifications-row'>
                                        <div class='devider'>
                                            <div class='left-side'>
                                                <dl><dt>Manufacturer: </dt><dd>".$boat->MakeString."</dd></dl>
                                                <dl><dt>Model: </dt><dd>".$boat->Model."</dd></dl>
                                                <dl><dt>Year: </dt><dd>".$boat->ModelYear."</dd></dl>
                                                <dl><dt>Category: </dt><dd>".$boat->BoatCategoryCode."</dd></dl>
                                                <dl><dt>Condition: </dt><dd>".$boat->SaleClassCode ."</dd></dl>
                                                <dl><dt>Location: </dt><dd>".$boat->BoatLocation->BoatCityName.", ".$boat->BoatLocation->BoatStateCode."</dd></dl>
                                                <dl><dt>Available for sale in U.S. waters: </dt><dd>";
                                    $_output .= $boat->IsAvailableForPls?"Yes":"No";
                                    $_output .= "</dd></dl>
                                            </div>    
                                            <div class='right-side'>
                                                <dl><dt>Vessel Name: </dt><dd>".$boat->BoatName."</dd></dl>
                                                <dl><dt>Boat Type: </dt><dd>".$boat->DriveTypeCode."</dd></dl>
                                                <dl><dt>Hull Material: </dt><dd>".$boat->BoatHullMaterialCode."</dd></dl>
                                                <dl><dt>Hull Type: </dt><dd></dd></dl>
                                                <dl><dt>Hull Color: </dt><dd></dd></dl>
                                                <dl><dt>HIN: </dt><dd>";
                                    $_output.= $boat->HasBoatHullID?"Yes":"No";
                                    $_output.= "</dd></dl>
                                                <dl><dt>Designer: </dt><dd>".$boat->DesignerName."</dd></dl>
                                                <dl><dt>Flag of Registry: </dt><dd></dd></dl>
                                            </div>
                                        </div>
                                    </div>";
                        $_output .= "<h2>DIMENSIONS & WEIGHT</h2>
                                    <div class='specifications-row'>
                                        <div class='devider'>
                                            <div class='left-side'>
                                                <dl><dt>Length: </dt><dd>".$boat->NominalLength . $this->ft_to_meter($boat->NominalLength)."</dd></dl>
                                                <dl><dt>LOA: </dt><dd>".$boat->LengthOverall . $this->ft_to_meter($boat->LengthOverall)."</dd></dl>
                                                <dl><dt>Beam: </dt><dd>".$boat->BeamMeasure . $this->ft_to_meter($boat->BeamMeasure)."</dd></dl>
                                            </div>    
                                            <div class='right-side'>
                                                <dl><dt>Draft - max: </dt><dd>".$boat->MaxDraft. $this->ft_to_meter($boat->MaxDraft)."</dd></dl>
                                                <dl><dt>Bridge Clearance: </dt><dd>".$boat->BridgeClearanceMeasure . $this->ft_to_meter($boat->BridgeClearanceMeasure)."</dd></dl>
                                                <dl><dt>DryWeight: </dt><dd>".$boat->BoatHullMaterialCode."</dd></dl>
                                            </div>
                                        </div>
                                    </div>";    

                        $_output .= "<h2>ENGINE</h2>
                                    <div class='specifications-row'>
                                        <div class='devider'>
                                            <div class='left-side'>
                                                <dl><dt>Make: </dt><dd>".$boat->Engines[0]->Make."</dd></dl>
                                                <dl><dt>Model: </dt><dd>".$boat->Engines[0]->Model ."</dd></dl>
                                                <dl><dt>Engine(s): </dt><dd>".$boat->NumberOfEngines ."</dd></dl>
                                                <dl><dt>Hours: </dt><dd>"; 
                                                if(isset($boat->Engines[0]->Hours)){
                                                    $_output .= $boat->Engines[0]->Hours;
                                                }
                                    $_output.="</dd></dl>                                            
                                                <dl><dt>Cruise Speed: </dt><dd>".$boat->CruisingSpeedMeasure ."</dd></dl>
                                                <dl><dt>Range: </dt><dd>".$boat->RangeMeasure ."</dd></dl>
                                            </div>    
                                            <div class='right-side'>
                                                <dl><dt>Engine Type: </dt><dd>".$boat->Engines[0]->Type."</dd></dl>
                                                <dl><dt>Drive Type: </dt><dd>".$boat->DriveTypeCode."</dd></dl>
                                                <dl><dt>Fuel Type: </dt><dd>".$boat->Engines[0]->Fuel."</dd></dl>
                                                <dl><dt>Horsepower: </dt><dd>".$boat->Engines[0]->EnginePower."</dd></dl>
                                                <dl><dt>Max Speed: </dt><dd>".$boat->MaximumSpeedMeasure."</dd></dl>
                                            </div>
                                        </div>
                                    </div>"; 
                        $_output .= "<h2>TANK CAPACITIES</h2>
                                    <div class='specifications-row'>
                                        <div class='devider'>
                                            <div class='left-side'>
                                                <dl><dt>Fuel Tank: </dt><dd>".$boat->FuelTankCapacityMeasure."</dd></dl>
                                                <dl><dt>Fresh Water Tank: </dt><dd>".$boat->WaterTankCapacityMeasure ."</dd></dl>
                                            </div>    
                                            <div class='right-side'>
                                                <dl><dt>Holding Tank: </dt><dd>";
                                            if(isset($boat->HoldingTankCapacityMeasure)){
                                                $_output .= $boat->HoldingTankCapacityMeasure;    
                                            }  
                                $_output .="</dd></dl>
                                            </div>
                                        </div>
                                    </div>";  
                        /*$_output .= "<h2>ACCOMMODATIONS</h2>
                                    <div class='specifications-row'>
                                        <div class='devider'>
                                            <div class='left-side'>
                                                <dl><dt>Total Cabins: </dt><dd>"."</dd></dl>
                                                <dl><dt>Total Berths: </dt><dd>".$boat->WaterTankCapacityMeasure ."</dd></dl>
                                                <dl><dt>Total Sleeps: </dt><dd>"."</dd></dl>
                                                <dl><dt>Total Heads: </dt><dd>"."</dd></dl>
                                                <dl><dt>Captains Cabin: </dt><dd>"."</dd></dl>
                                            </div>    
                                            <div class='right-side'>
                                                <dl><dt>Crew Cabins: </dt><dd>"."</dd></dl>
                                                <dl><dt>Crew Berths: </dt><dd>"."</dd></dl>
                                                <dl><dt>Crew Sleeps: </dt><dd>"."</dd></dl>
                                                <dl><dt>Crew Heads:: </dt><dd>"."</dd></dl>
                                            </div>
                                        </div>
                                    </div>";   */     
                    $_output .= "</div><h3 class='title'>Gallery</h3><div id='gallery' class='yw-tabcontent'>";

                    /*Gallery Content*/            
                    $_output .= "<div class='image-collection'>";
                                foreach($boat-> Images as $i){

                                    $secureImg = str_replace("http://", "https://", $i->Uri);
                                    $_output.= "<div class='boat-images'><a href='" . $secureImg . "' data-fancybox='gallery' data-caption='" . $i->Caption . "'><img src='"
                                        . str_replace("_XLARGE.jpg","_LARGE.jpg", $secureImg) . "'/></a></div>";
                                }
                                $_output.= "</div>";
                    $_output .= "</div><h3 class='title'>Description</h3>
                                <div id='descriptions' class='yw-tabcontent'>";
                            foreach($boat-> AdditionalDetailDescription as $d){
                                $_output.= "<div style='padding-top:14px;'>" . str_replace("center","left", strip_tags(preg_replace('/\s\s+/', '<br/>', $d),"<p><br><br/>")) . "</div>";
                            }
                    $_output .= "</div><h3 class='title'>Link</h3><div id='links' class='yw-tabcontent'>
                                    <h2>EXTERNAL LINKS</h2>
                                    <div class='external_link_content'>";
                                if(isset($boat->ExternalLink)){
                                    $_output .= "<a href='".$boat->ExternalLink[0]->Uri."' target='_blank'>" .$boat->ExternalLink[0]->Text."</a>";

                                }
                    $_output .=  "<div>
                                </div>";
                                               
                   /* if(!$isBroker) {
                        $_output .= "<div class='listing-title'><strong>Sales Rep: " . $boat->SalesRep->Name . "</strong></div>"
                        . "<div>Email: <a href='mailto:" . $boat->Office->Email . "'>" . $boat->Office->Email . "</a></div>"
                        . "<div>Phone: " . $boat->Office->Phone . "</div>";
                    }*/

                    $_output.= "</div></div></div></div></div>";

                    $_output.= "<div class='right-cell' style='float:right;'>
                                    <section class='section broker-wrap'>            
                                    <h3>Interested In This Yacht :</h3>                                
                                            <div class='broker clearfixmain'>                                            
                                                <div class='brokermeta clearfixmain'>
                                                    <h4>Contact Yacht Hunter</h4>            
                                                </div>
                                            </div>
                                    <div class='ph'><a class='tel' href='tel:305-709-2644'><span>305-709-2644</span></a></div>
                                    <div class='cb'><a href='/contact-broker/?id=1&amp;yid=11717&amp;pg=0' class='contactbroker button contact yw-contact' data-subject='".$boat->ModelYear . " ". $boat->MakeString . " " . $boat->Model."' data-fancybox-type='iframe'><span>Email Us</span></a></div>
                                    </section>";
                    $_output.= "</div></div>";

                }   
            }        

            return $_output;

        }

        public function GET_BROKER_HTML_output($type, $detailPage, $status, $buttonCount)
        {
            setlocale(LC_MONETARY, 'en_US.utf8');

            $json = $this->GET_BROKER_JSON_output($type, $status);
            $_output = "";

            $_pageSize = $this->GetPageSize();

            if(isset($_GET["ps"]) && $_GET["ps"]!=""){
                $_pageSize = $_GET["ps"]-0;
            }

            $_numResults = 0;
            if($json != Null){
                $_numResults = $json->numResults;
            }

            $_current_page = 1;
            $query="";

            $path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            if(isset($_GET["boatPage"]) && $_GET["boatPage"]!=""){
                $_current_page = $_GET["boatPage"];
            }

            $_maxPages = ceil($_numResults/$_pageSize);

            //   print_r("<br/>****" . $_numResults . " - Page Size:" . $_pageSize . "<br/>");

            $total = 0;
            if($json != null && $json->results) {
                $total = count($json->results);
                foreach ($json->results as $boat) {


                    $boatDescription = strip_tags($boat->GeneralBoatDescription[0]);
                    $out = strlen($boatDescription) > $this->descriptionLength ? substr($boatDescription,0,$this->descriptionLength)."... <a href='".$detailPage."?boatID=" . $boat->DocumentID . "'>more &raquo;</a>" : $boatDescription;

                    $image = $boat->Images[0];
                    $secureImg = str_replace("http://", "https://", $image->Uri);
                    
                    $_output .= "<div class='boat-item'>"  
                        . "<div class='listing-details'>"                     
                        . "<div class='listing-title'>" . $boat->ModelYear . " ". $boat->MakeString. " " . $boat->Model . "</div>"
                        . "<div class='listing-img'><a href='".$detailPage."?boatID=" . $boat->DocumentID . "'><img src='" . $secureImg . "'/></a></div>"
                        . "<div class='listing-location'><span>".$boat->BoatLocation->BoatCityName.", ".$boat->BoatLocation->BoatStateCode.", ".$boat->BoatLocation->BoatCountryID."</span></div>"
                        
                         . "<div class='listing-price'>". ($boat->NormPrice != "" && $boat->NormPrice != 0? "$"
                            . str_replace(".00","", str_replace("USD ","",number_format($boat->NormPrice))): "")  . "</div>"
                        /* . "<div class='listing-make'>" . ($boat->MakeString? "<span class='make'>" .$boat->MakeString."</span>":"<span></span>")."</div>"*/
                        . "<div class='listing-buttons'><a href='". $detailPage."?boatID=" . $boat->DocumentID ."' class='details button'>Details</a><a data-type='image' data-subject='".$boat->ModelYear . " ". $boat->MakeString . " " . $boat->Model."' data-caption='Caption' class='yw-contact contact button'>Get True Price</a>"
                        . "</div></div></div>";
                }
            }else{

                $_output .= "<div class='yw-no-results'>No results returned.</div>";
            }
            
            $_output = "<div class='boat-items'>".$_output."</div>";


            if(isset($_GET["keyword"]) && $_GET["keyword"]!=""){
                $query .= "&keyword=" . $_GET["keyword"];
            }

            parse_str($_SERVER['QUERY_STRING'], $q);

            $queryString = "";
            $sortQueryString="";
            $concat = "&";
            foreach($q  as $key => $value){

                if($key != "boatPage" && $value != ""){
                    $queryString .= $concat . $key . "=" . $value;
                   
                    if($key != "sort"){
                        $sortQueryString.= $concat . $key . "=" . $value;
                    }

                    $concat = "&";
                }
               
            }

            $direction = "desc";
            $sort = "Price";

            if(isset($_GET["sort"]) && $_GET["sort"] != ""){

                $sortParts = explode("|", $_GET["sort"]);
                $sort = $sortParts[0];
                $direction = $sortParts[1];
            }


            $yw_list_heading = "<div class='yw_detail_heading'>".
                /*<div class='total_result'> ". $total ."RESULT(S) </div>*/
                "<div class='yw-sort'> <span>Sort By | </span>"
            ."<a href='". $path_only . "?boatPage=1". $sortQueryString . "&sort=Price|" . $direction . "' class='". ($sort=="Price"?"sortActive":"") . "'>Price</a>"
            ."<a href='" . $path_only . "?boatPage=1". $sortQueryString . "&sort=ModelYear|" . $direction . "' class='". ($sort=="ModelYear"?"sortActive":"") . "'>Year</a>"
            ."<a href='" . $path_only . "?boatPage=1". $sortQueryString . "&sort=Length|" . $direction . "' class='". ($sort=="Length"?"sortActive":"") . "'>Length</a>"
           . "<div class='sort-direction'>"
            ."<a href='". $path_only . "?boatPage=1". $sortQueryString . "&sort=" . $sort . "|desc' class='asc ". ($direction=="asc"?"sortActive":"") . "'></a>"
                    ."<a href='". $path_only . "?boatPage=1". $sortQueryString . "&sort=" . $sort . "|asc' class='desc ". ($direction=="desc"?"sortActive":"") . "'></a>"
            ."</div></div></div>";       


            $_pageControl = "<div class='page-holder'>";
            $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=1". $queryString  . "'>&laquo;</a>";

            if($_current_page>1){
                $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . ($_current_page-1) . $queryString  . "'>&lsaquo;</a>";
            }else{
                $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=1" . $queryString  . "'>&lsaquo;</a>";
            }

            $seed = 1;

            if($_current_page > ($buttonCount/2)){
                $seed = $_current_page - floor(($buttonCount-1)/2);
            }
    //$_maxPages


            if($_current_page + ($buttonCount/2) > $_maxPages){

                $seed = $_maxPages-($buttonCount-1);
            }

            if($seed<1){
                $seed=1;
            }

            $endpage = $seed + ($buttonCount-1);

            if($endpage>$_maxPages){
                $endpage = $_maxPages;
            }

            for($i=$seed;$i<=$endpage;$i++){
                $_pageControl .= "<a class='page".($_current_page-0 == $i? " active":"") . "' href='" . $path_only . "?boatPage=".$i. $queryString  . "'>" . $i . "</a>";
            }



            if($_current_page < $_maxPages){
                $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . ($_current_page + 1) . $queryString  . "'>&rsaquo;</a>";
            }else{
                $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . $_maxPages . $queryString  . "'>&rsaquo;</a>";
            }
            $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . $_maxPages . $queryString  . "'>&raquo;</a>";


            $_pageControl .= "</div>";

            $search_box = $this->GetSearch('/browse-yachts/');
            $mobile_search_button = "<h3 class='ad-search'><span>Advanced search</span></h3>";

            $_output = "<div class='yw-lists-content'>". $yw_list_heading. $mobile_search_button .$search_box. "<div class='yw-list-content'>". $_output . $_pageControl."</div></div>";

            return $_output;

        }



       public function GET_HTML_output($type, $detailPage, $status, $buttonCount)
        {
            setlocale(LC_MONETARY, 'en_US.utf8');

            $json = $this->GET_JSON_output($type, $status);
            $_output = "";

            $_pageSize = $this->GetPageSize();

            if($_GET["ps"]!=""){
                $_pageSize = $_GET["ps"]-0;
            }


            $_numResults = $json->numResults;
            $_current_page = 1;
            $query="";

            $path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            if($_GET["boatPage"]!=""){
                $_current_page = $_GET["boatPage"];
            }

            $_maxPages = ceil($_numResults/$_pageSize);

            //   print_r("<br/>****" . $_numResults . " - Page Size:" . $_pageSize . "<br/>");


            if($json->results) {

                foreach ($json->results as $boat) {


                    $boatDescription = strip_tags($boat->GeneralBoatDescription[0]);
                    $out = strlen($boatDescription) > $this->descriptionLength ? substr($boatDescription,0,$this->descriptionLength)."... <a href='".$detailPage."?boatID=" . $boat->DocumentID . "'>more &raquo;</a>" : $boatDescription;

                    $image = $boat->Images[0];
                    $secureImg = str_replace("http://", "https://", $image->Uri);

                    $_output .= "<div class='boat-item'>"

                        . "<div class='listing-img'><a href='".$detailPage."?boatID=" . $boat->DocumentID . "'><img src='" . $secureImg . "'/></div>"
                        . "<div class='listing-details'>"
                        . "<div class='listing-title'><h2><a href='" . $detailPage . "?boatID=" . $boat->DocumentID . "'>" . $boat->BoatName . ($boat->BoatName && $boat->NormPrice != "" && $boat->NormPrice!="" ? " - ":"") . ($boat->NormPrice != "" && $boat->NormPrice != 0? "$"
                            . str_replace(".00","", str_replace("USD ","",money_format('%i', $boat->NormPrice))): "")  . "</a></h2></div>"
                        . "<div class='listing-model'>" . $boat->ModelYear . " " . $boat->Model . "</div>"
                        . ($boat->YachtWorldID !=""?"<div class='listing-yachtworldID'>YachtWorld ID: " . $boat->YachtWorldID . "</div>":"")

                        . "<div class='listing-description'>" . $out  . "</div>"
                        . "</div></div>";
                }
            }else{

                $_output .= "<div class='yw-no-results'>No results returned.</div>";
            }
            $_pageControl = "<div class='page-holder'>";


            if($_GET["keyword"]!=""){
                $query .= "&keyword=" . $_GET["keyword"];
            }

            parse_str($_SERVER['QUERY_STRING'], $q);

            // $queryString = "";
            // $concat = "&";
            // foreach($q  as $key => $value){

            //     if($key != "boatPage" && $value != ""){
            //         $queryString .= $concat . $key . "=" . $value;
            //         $concat = "&";
            //     }
            //     // print_r($queryString);
            // }


            $queryString = "";
            $sortQueryString="";
            $concat = "&";
            foreach($q  as $key => $value){

                if($key != "boatPage" && $value != ""){
                    $queryString .= $concat . $key . "=" . $value;
                   
                    if($key != "sort"){
                        $sortQueryString.= $concat . $key . "=" . $value;
                    }

                    $concat = "&";
                }
               
            }

            $direction = "desc";
            $sort = "Price";

            if($_GET["sort"] != ""){

                $sortParts = explode("|", $_GET["sort"]);
                $sort = $sortParts[0];
                $direction = $sortParts[1];
            }


            $_output = "<div class='sort'>"
            ."<a href='". $path_only . "?boatPage=1". $sortQueryString . "&sort=Price|" . $direction . "' class='". ($sort=="Price"?"sortActive":"") . "'>Price</a> | "
            ."<a href='" . $path_only . "?boatPage=1". $sortQueryString . "&sort=ModelYear|" . $direction . "' class='". ($sort=="ModelYear"?"sortActive":"") . "'>Manufacture Year</a> | "
            ."<a href='" . $path_only . "?boatPage=1". $sortQueryString . "&sort=Length|" . $direction . "' class='". ($sort=="Length"?"sortActive":"") . "'>Length</a>"
            . "</div>" 
            . "<div class='sort-direction'>"
            ."<a href='". $path_only . "?boatPage=1". $sortQueryString . "&sort=" . $sort . "|asc' class='". ($direction=="asc"?"sortActive":"") . "'>ascending</a> | "
                    ."<a href='". $path_only . "?boatPage=1". $sortQueryString . "&sort=" . $sort . "|desc' class='". ($direction=="desc"?"sortActive":"") . "'>descending</a>"
            ."</div>"
            . $_output;




            // print_r($queryString);


            $_pageControl = "<div class='page-holder'>";
            $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=1". $queryString  . "'>&laquo;</a>";

            if($_current_page>1){
                $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . ($_current_page-1) . $queryString  . "'>&lsaquo;</a>";
            }else{
                $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=1" . $queryString  . "'>&lsaquo;</a>";
            }

            $seed = 1;

            if($_current_page > ($buttonCount/2)){
                $seed = $_current_page - floor(($buttonCount-1)/2);
            }


           if($_current_page + ($buttonCount/2) > $_maxPages){

               $seed = $_maxPages-($buttonCount-1);
           }

           if($seed<1){
               $seed=1;
           }

           $endpage = $seed + ($buttonCount-1);

           if($endpage>$_maxPages){
               $endpage = $_maxPages;
           }

           for($i=$seed;$i<=$endpage;$i++){
               $_pageControl .= "<a class='page".($_current_page-0 == $i? " active":"") . "' href='" . $path_only . "?boatPage=".$i. $queryString  . "'>" . $i . "</a>";
           }



           if($_current_page < $_maxPages){
               $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . ($_current_page + 1) . $queryString  . "'>&rsaquo;</a>";
           }else{
               $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . $_maxPages . $queryString  . "'>&rsaquo;</a>";
           }
           $_pageControl .= "<a class='page' href='" . $path_only . "?boatPage=" . $_maxPages . $queryString  . "'>&raquo;</a>";


           $_pageControl .= "</div>";
           $_output = $_pageControl . $_output . $_pageControl;


           return $_output;
        }





        public function GetSearch($action)
        {
            $_output = "<div class='yw-search-box yw-mobile-fade'><h4>Search By</h4>"
                . "<form id='yw-search-form' class='yw-search-form' method='Get' action='" . $action . "'>"
                . "<div class='yw-search-label'><span>Keyword:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon searchkey'></span><input type='text' name='keyword' value='" . (isset($_GET["keyword"])?$_GET["keyword"]:"" ) . "' placeholder='Keyword' /></div>"
                 . "<div class='yw-search-label'><span>Manufacturer:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon makefield'></span><input type='text' name='makestring' value='" . (isset($_GET["MakeString"])?$_GET["MakeString"]:"" ) . "' placeholder='Manufacturer' /></div>"
                . "<div class='yw-split-50 input-left'>"
                . "<div class='yw-search-label'><span>Length Min:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon lengthfield'></span><input type='text' name='length-min' value='" . (isset($_GET["length-min"])?$_GET["length-min"]:"" ). "' placeholder='Min' /></div>"
                . "</div>"
                . "<div class='yw-split-50 input-right'>"
                . "<div class='yw-search-label'><span>Length Max:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon lengthfield'></span><input type='text' name='length-max' value='" . (isset($_GET["length-max"])?$_GET["length-max"]:"" ) . "' placeholder='Max' /></div>"
                . "</div>"
                . "<div class='yw-split-50 input-left'>"
                . "<div class='yw-search-label'><span>Year Min:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon yearfield'></span><input type='text' name='year-min' value='" . (isset($_GET["year-min"])?$_GET["year-min"]:"" ) . "' placeholder='Min' /></div>"
                . "</div>"
                . "<div class='yw-split-50 input-right'>"
                . "<div class='yw-search-label'><span>Year Max:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon yearfield'></span><input type='text' name='year-max' value='" . (isset($_GET["year-max"])?$_GET["year-max"]:"" ). "' placeholder='Max' /></div>"
                . "</div>"
                . "<div class='yw-split-50 input-left'>"
                . "<div class='yw-search-label'><span>Price Min:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon pricefield'></span><input type='text' name='price-min' value='" . (isset($_GET["price-min"])?$_GET["price-min"]:"" ). "' placeholder='Min' /></div>"
                . "</div>"
                . "<div class='yw-split-50 input-right'>"
                . "<div class='yw-search-label'><span>Price Max:</span></div>"
                . "<div class='yw-search-input'><span class='input-icon pricefield'></span><input type='text' name='price-max' value='" . (isset($_GET["price-max"])?$_GET["price-max"]:"" ) . "' placeholder='Max' /></div>"
                . "</div>"
                /*. "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Boat Type:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"type\" class=\"short_select\">
    								<option value=\"\">All Boat Types</option>
    								<option value=\"\"> ----------------</option>
    								<option value=\"Power\"" . (isset($_GET["type"])&&$_GET["type"] == "Power"?" selected":"") . ">Power</option>
                                    <option value=\"Sail\"" . (isset($_GET["type"])&&$_GET["type"] == "Sail"?" selected":"") . ">Sail</option>
    								</select>"
                . "</div>"
                . "</div>"
                . "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Hull Material:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"hmid\" class=\"short_select\" >
    								<option value=\"\">Any</option>
    								<option value=\"Aluminum\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Aluminum"?" selected":"") . ">Aluminum</option>
    								<option value=\"Carbon Fiber\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Carbon Fiber"?" selected":"") . ">Carbon Fiber</option>
    								<option value=\"Composite\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Composite"?" selected":"") . ">Composite</option>
    								<option value=\"Ferro-Cement\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Ferro-Cement"?" selected":"") . ">Ferro-Cement</option>
    								<option value=\"Fiberglass\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Fiberglass"?" selected":"") . ">Fiberglass</option>
    								<option value=\"Hypalon\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Hypalon"?" selected":"") . ">Hypalon</option>
    								<option value=\"PVC\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "PVC"?" selected":"") . ">PVC</option>
    								<option value=\"Roplene\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Roplene"?" selected":"") . ">Roplene®</option>
    								<option value=\"Steel\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Steel"?" selected":"") . ">Steel</option>
    								<option value=\"Wood\"" . (isset($_GET["hmid"])&&$_GET["hmid"] == "Wood"?" selected":"") . ">Wood</option>
    								</select>"
                . "</div>"
                . "</div>"
                . "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Number of Engines:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"enid\" class=\"short_select\" >
                                    <option value=\"\">Any</option>
                                    <option value=\"100\"" . (isset($_GET["enid"])&&$_GET["enid"] == "100"?" selected":"") . ">1</option>
                                    <option value=\"101\"" . (isset($_GET["enid"])&&$_GET["enid"] == "101"?" selected":"") . ">2</option>
                                    <option value=\"103\"" . (isset($_GET["enid"])&&$_GET["enid"] == "103"?" selected":"") . ">None</option>
                                    <option value=\"102\"" . (isset($_GET["enid"])&&$_GET["enid"] == "102"?" selected":"") . ">Other</option>
                                    </select>"
                . "</div>"
                . "</div>"
                . "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Fuel Type:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"ftid\" class=\"short_select\" >
    								<option value=\"\">Any</option>
    								<option value=\"101\"" . (isset($_GET["ftid"])&&$_GET["ftid"] == "101"?" selected":"") . ">Diesel</option>
    								<option value=\"100\"" . (isset($_GET["ftid"])&&$_GET["ftid"] == "100"?" selected":"") . ">Gas</option>
    								<option value=\"102\"" . (isset($_GET["ftid"])&&$_GET["ftid"] == "102"?" selected":"") . ">Other</option>
    								</select>"
                . "</div>"
                . "</div>"
                . "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Condition:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"cond\" class=\"short_select\" >
    								<option value=\"\">Any</option>
    								<option value=\"true\"" . (isset($_GET["cond"])&&$_GET["cond"] == "true"?" selected":"") . ">New</option>
    								<option value=\"false\"" . (isset($_GET["cond"])&&_GET["cond"] == "false"?" selected":"") . ">Used</option>
    								</select>"
                . "</div>"
                . "</div>"
                . "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Worldwide Region:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"rid\" class=\"short_select\" >
                                    <option value=\"\">Any</option>
                                    <option value=\"US\"" . (isset($_GET["rid"])&&$_GET["rid"] == "US"?" selected":"") . ">United States</option>
                                    <option value=\"CA\"" . (isset($_GET["rid"])&&$_GET["rid"] == "CA"?" selected":"") . ">Canada</option>
                                    </select>"
                . "</div>"
                . "</div>"
                . "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Boats Per Page:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"ps\" class=\"short_select\" >
                                    <option value=\"20\"" . (isset($_GET["ps"])&&$_GET["ps"] == "20"?" selected":"") . ">20 boats/page</option>
                                    <option value=\"30\"" . (isset($_GET["ps"])&&$_GET["ps"] == "30"?" selected":"") . ">30 boats/page</option>
                                    <option value=\"40\"" . (isset($_GET["ps"])&&$_GET["ps"] == "40"?" selected":"") . ">40 boats/page</option>
                                    <option value=\"50\"" . (isset($_GET["ps"])&&$_GET["ps"] == "50"?" selected":"") . ">50 boats/page</option>
                                    <option value=\"75\"" . (isset($_GET["ps"])&&$_GET["ps"] == "75"?" selected":"") . ">75 boats/page</option>
                                    <option value=\"100\"" . (isset($_GET["ps"])&&$_GET["ps"] == "100"?" selected":"") . ">100 boats/page</option>
                                    </select>"
                . "</div>"
                . "</div>"
                . "<div class='yw-split-50'>"
                . "<div class='yw-search-label'><span>Added Within:</span></div>"
                . "<div class='yw-search-input'>"
                . "<select name=\"msint\" class=\"short_select\" >
    								<option value=\"\">Any time</option>
    								<option value=\"1\"" . (isset($_GET["msint"])&&$_GET["msint"] == "1"?" selected":"") . ">the last day</option>
    								<option value=\"3\"" . (isset($_GET["msint"])&&$_GET["msint"] == "3"?" selected":"") . ">the last 3 days</option>
    								<option value=\"7\"" . (isset($_GET["msint"])&&$_GET["msint"] == "7"?" selected":"") . ">the last 7 days</option>
    								<option value=\"14\"" . (isset($_GET["msint"])&&$_GET["msint"] == "14"?" selected":"") . ">the last 14 days</option>
    								<option value=\"30\"" . (isset($_GET["msint"])&&$_GET["msint"] == "30"?" selected":"") . ">the last 30 days</option>
    								<option value=\"60\"" . (isset($_GET["msint"])&&$_GET["msint"] == "60"?" selected":"") . ">the last 60 days</option>
    								</select>"
                . "</div>"
                . "</div>"*/

                . "<div class='yw-search-button'><button type='submit' id='yw-bttn-search' class='button yw-bttn'>Search</button> <button type='reset' id='yw-bttn-reset' class='button yw-bttn' onClick='location=\"" . $action . "\";'>Reset</button></div>"
                . "</form>"
                . "</div>";

            return $_output;
        }

        public function ft_to_meter($str){
            preg_match_all('!\d+!', $str, $ft);
            $meter = 0;
            if(count($ft[0])>0){
                if(count($ft[0])>1){
                    $meter = round($ft[0][0]*0.3048 + $ft[0][1]*0.0254, 2);
                }else{
                    $meter = round($ft[0][0]*0.3048, 2) ;
                }   
            }

            if( $meter==0 ){
                return;
            }else{
                return " - " . strval($meter) . " meter";    
            }        
        }

        public function yw_contract_form(){

        }


    }

