
<?php

//echo 'precessing';
//exit;
require '/calais/opencalaismongo.php';
$company_list = ["www.zubairelectric.com", "bmtc.ae", "www.cinmarlight.com","www.agcled.com", "bncnetwork", "venture"];
$apiKey = "mk7AAdW4spiFz9XzWzlqMyfsNjsSEOEY";
$connect = new MongoClient('mongodb://localhost');
$collection = $connect->bnccontact->contact;

for ($index = 0; $index < count($company_list); $index++) {
    $oc = new OpenCalais($apiKey);
    $fileContent = $file = '';
    $content = '';
//    echo "casperjs lightdata.js " . $company_list[$index] . " contactus us uae" . "<br/>";
    exec("casperjs lightdata.js " . $company_list[$index] . " contactus us uae");
    $file = $company_list[$index] . '.txt'; //"www.bncnetwork.net.txt"; //$_GET['url'];
    try {
        $fileContent = @file_get_contents($file);
        if (!$fileContent) {
            throw new Exception('Failed to open uploaded file');
        } else {
            $content = $fileContent;
            $entities = array();
            $entities = $oc->getEntities($content);
            $info = $entities['entities'];
            $collection_data = array(
                "name" => $company_list[$index],
                "type" => "contact",
                "info" => $info
            );
            $collection->insert($collection_data);
        }
    } catch (Exception $hi) {
        $info = 'sorry file not available';
        $collection_data = array(
            "name" => $company_list[$index],
            "type" => "contact",
            "info" => $info
        );
        $collection->insert($collection_data);
    }
}
echo 'done';
?>
