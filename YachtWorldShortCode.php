<?

class YachtWorldShortCode{

    public function addCSS(){
        wp_enqueue_style( 'yw_fancybox_style', plugins_url( 'css/jquery.fancybox.min.css' , __FILE__ ) );
        wp_enqueue_script( 'yw_fancybox_script', plugins_url( 'javascript/jquery.fancybox.min.js' , __FILE__ ), array('jquery') );

        wp_enqueue_style( 'yw_style', plugins_url( 'css/yachtworld.css' , __FILE__ )  );
        wp_enqueue_script( 'yw_script', plugins_url( 'javascript/yachtworld.js' , __FILE__ ), array('jquery') );

       /* wp_enqueue_style( 'yw_fancybox_style', plugins_url( 'fancybox/jquery.fancybox-1.3.4.css' , __FILE__ )  );
        wp_enqueue_script( 'yw_fancybox_mousewheel', plugins_url( 'fancybox/jquery.mousewheel-3.0.4.pack.js' , __FILE__ ), array('jquery') );
        wp_enqueue_script( 'yw_fancybox_pack', plugins_url( 'fancybox/jquery.fancybox-1.3.4.pack.js' , __FILE__ ), array('jquery') );
        wp_enqueue_script( 'yw_fancybox_script', plugins_url( 'fancybox/jquery.fancybox-1.3.4.js' , __FILE__ ), array('jquery') );*/
    }

    public function getSingleYacht()
    {
        $this->addCSS();
        $yw_public = new YachtWorldPublic();
        $boatId = $_GET["boatID"];
        $json = $yw_public->GET_SingleBoat_JSON_output($boatId);

        if($_GET["json"]==true){
            print "<pre>";
            print_r($json);
            print "</pre>";
        }else{
            return $yw_public->GetSingleBoatHtml($json, false);
        }
    }

    public function getSingleBrokerYacht()
    {
        $this->addCSS();
      
        $yw_public = new YachtWorldPublic();
        $boatId = $_GET["boatID"];
        $json = $yw_public->GET_SingleBoatBroker_JSON_output($boatId);

        if(isset($_GET["json"]) && $_GET["json"]==true){
            print "<pre>";
            print_r($json);
            print "</pre>";
        }else{
            return $yw_public->GetSingleBoatHtml($json, true);
        }
    }


    public function yachtWorld_shortcode($atts = []){

        $this->addCSS();
        $type= $atts["type"];
        $detailsPage = $atts["detail"];
        $output_type = $atts["output"];
        $description_length = $atts["description-length"];
        $status= $atts["status"];
        $buttonCount = 10;

        if($atts["button-count"] - 0 >0){
            $buttonCount = $atts["button-count"];
        }

        $yw_public = new YachtWorldPublic();
        if($description_length!="") {
            $yw_public->descriptionLength = $description_length;
        }

        if($output_type == "json"){
            $_output = $yw_public->GET_JSON_output($type, $status);
            print "<pre>";
            print_r($_output);
            print "</pre>";
        }else{
            $_output = $yw_public->GET_HTML_output($type, $detailsPage, $status, $buttonCount);
            return $_output;
        }
    }

    public function yachtWorld_broker_shortcode($attr = []){

        $atts = array('type' => 'Powser', 
                    'detail' => '/yacht-broker-details/',
                    'descriptionlength' => 450,
                    'output' => '',
                    'status' => '',
                    'button-count' => 0
                );

        $atts = array_merge($atts, $attr);

        $this->addCSS();
        $type= $atts["type"];
        $detailsPage = $atts["detail"];
        $output_type = $atts["output"];
        $yw_public = new YachtWorldPublic();
        $yw_public->startDate = $atts["start-date"];
        $status= $atts["status"];
        $buttonCount = 10;

        if($atts["button-count"] - 0 >0){
            $buttonCount = $atts["button-count"];
        }

        $description_length = $atts["description-length"];

        if($description_length!="") {
            $yw_public->descriptionLength = $description_length;
        }


        if($output_type == "json"){
            $_output = $yw_public->GET_BROKER_JSON_output($type, $status);
            print "<pre>";
            print_r($_output);
            print "</pre>";
        }else{
            $_output = $yw_public->GET_BROKER_HTML_output($type, $detailsPage, $status, $buttonCount);
            return $_output;
        }
    }

    public function yachtworld_search($atts = []){

        $this->addCSS();
        $yw_public = new YachtWorldPublic();
        return $yw_public->GetSearch($atts["action"]);
    }

    public function __construct(){

        add_shortcode('yw', array($this,'yachtWorld_shortcode'));
        add_shortcode('yw-search', array($this,'yachtWorld_search'));
        add_shortcode('getSingleYacht',array($this,'getSingleYacht'));
        add_shortcode('yw-broker', array($this,'yachtWorld_broker_shortcode'));
        add_shortcode('getSingleBrokerYacht',array($this,'getSingleBrokerYacht'));


    }

}