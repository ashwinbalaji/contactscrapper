<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bnc_contacts";
//echo 'precessing';exit;
echo 'precessing please wait...';
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
require '/calais/opencalais.php';
$company_list = ["www.agcled.com", "bncnetwork", "venture"];
$apiKey = "mk7AAdW4spiFz9XzWzlqMyfsNjsSEOEY";
$oc = new OpenCalais($apiKey);
for ($index = 0; $index < count($company_list); $index++) {
    $file = $company_list[$index] . '.txt'; //"www.bncnetwork.net.txt"; //$_GET['url'];
//    echo $file;
    $fileContent = file_get_contents($file);
//        echo $fileContent;
//        echo '<br/>';
//
    $content = $fileContent;
    $entities = $oc->getEntities($content);
    $phone_details = $email_details = array();
    for ($entities_index = 0; $entities_index < count($entities); $entities_index ++) {
        if (strtolower($entities[$entities_index]->_type) == "phonenumber") {
            $phone_details[] = $entities[$entities_index];
        }
        if (strtolower($entities[$entities_index]->_type) == "emailaddress") {
            $email_details[] = $entities[$entities_index];
        }
    }

    $phone_number_one = isset($phone_details[0]->name) ? $phone_details[0]->name : '';
    $phone_number_two = isset($phone_details[1]->name) ? $phone_details[1]->name : '';
    $email_one = isset($email_details[0]->name) ? $email_details[0]->name : '';
    $email_two = isset($email_details[1]->name) ? $email_details[1]->name : '';
    $junk_data = '';
    for ($phone_details_index = 2; $phone_details_index < count($phone_details); $phone_details_index++) {
        if ($junk_data == '') {
            $junk_data .= $phone_details[$phone_details_index]->name;
        } else {
            $junk_data .= ', phone:' . $phone_details[$phone_details_index]->name;
        }
    }
    for ($email_details_index = 2; $email_details_index < count($email_details); $email_details_index++) {
        if ($junk_data == '') {
            $junk_data .= $email_details[$email_details_index]->name;
        } else {
            $junk_data .= ', email:' . $email_details[$email_details_index]->name;
        }
    }
    $sql = "INSERT INTO contacts (name, phoneone, phonetwo,emailone,emailtwo,junk_data,created_on)
                    VALUES ('$company_list[$index]', '$phone_number_one', '$phone_number_two','$email_one','$email_two','$junk_data',now())";
    echo $sql;
    echo '<br/>';
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


$conn->close();
?>
