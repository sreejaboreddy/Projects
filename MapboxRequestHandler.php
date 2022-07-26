<?php

 class MapboxRequestHandler{
    // declaring variables
    // $url is used to store the input url
    public $url;
    //  $p is used to store the path 
    public $p;
    // $ans is used to store the end path according to LocationIQ standards
    public $ans;
    // $pathPara is used to store the varialbes available in both mapbox and locationIQ optional parmeters
    public $pathPara = array();
    
    //declaring a constructor 
    function __construct($url) {
        $this->url = $url;
      }

    
    public function pathExtraction(){
            // varible $s is used to save the parameter extracted from the input url
            // stored in an associative array
            $s = parse_url($this->url);

            // print_r($s);


        //  finding path parameters      
        
        // $x is a temporary variable used to store the path parameters
        $x=explode('&' , $s['query']);

        // $requestPara variable is used to store the same parameters in both mapbox and locationIQ
        $requestPara = array(
                               'language' => 'accept-language',
                               'bbox'=> 'viewbox' , 
                               'country' => 'countrycodes',
                               'limit'=>'limit'
                            );
         
        // just a temporary varible used to extract key and value of each parameter
        $temp = array();
        
        //looping through all the parameters and its values
        for($i=0; $i<sizeof($x);$i++)
        {
            $temp = explode( '=', $x[$i] );

            //checking if the key is present in both requestPara or not
            if(  isset( $requestPara[$temp[0]] )  )
            {
                 // if the value is present then we are changing the key according to LocationIQ standards
                 $this->pathPara[$requestPara[$temp[0]]] = $temp[1];
            }
        }
            // $r variable is used to save which consists of the string that needs to be searched
            $r=$s['path'];
            //manipulating the string to get the key words which we use to search location
            $r=explode("/",$r);
            $r=trim($r[4],".json");
            // $r is being assinged to $p variable specific to this class
            $this->p=$r;
            // calling parameterExtaction to change the url according to locationIQ standards
            $this->parameterExtraction();
    }



    public function parameterExtraction(){
        //$parse is an associative array which is used to store all the values that are needed 
        $parse= array( 
            // key is the access token from unwired labs
            'key'=>"pk.587a83ef89129e3f27f12787564289c0",
            // q is the search string which we extracted from $path variable
            'q'=>$this->p , 
            // just specifiying the format as json 
            'format'=>'json'
        );

        if(sizeof($this->pathPara)>0){
            $parse = $parse + $this->pathPara;
        }

        // print_r($parse);

        // building a http query from the associative array which is stored in $parse
        $this->ans = http_build_query($parse);
        $this->ans="https://us1.locationiq.com/v1/search.php?".$this->ans;
        // printing the url generated
        print($this->ans);
        // response function is called to get the response from the above url generated
        $this->response();
    }

    public function response(){
        // curl initiation
        $c = curl_init();
        curl_setopt($c , CURLOPT_URL , $this->ans);
        curl_setopt($c , CURLOPT_RETURNTRANSFER , true);
        
        // curl execution
        $resp  =curl_exec($c);
 
        // checking if the result is error then we will just print the error 
        if($e = curl_error($c))
        {
            echo $e;
        }
        // else  we will print the response in json format
        else{
            // the true is used to make an array from an object
            $decode = json_decode($resp ,true);
            print_r($decode);
        }
        // colsing the curl connection 
        curl_close($c);
    }
}
//   creating an object for the MapboxRequestHandler class
//   $x=new MapboxRequestHandler('https://api.mapbox.com/geocoding/v5/mapbox.places/chester.json?proximity=-74.70850,40.78375&access_token=YOUR_MAPBOX_ACCESS_TOKEN');
//   $x->pathExtraction();
?>