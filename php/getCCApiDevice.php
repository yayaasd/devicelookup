<!--
/* 
 * Copyright (c) 2025 Yasin Is
 *
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the 
 * "Software"), to deal in the Software without restriction, including 
 * without limitation the rights to use, copy, modify, merge, publish, 
 * distribute, sublicense, and/or sell copies of the Software, and to 
 * permit persons to whom the Software is furnished to do so, subject to 
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
 * DEALINGS IN THE SOFTWARE.
 *
 */
-->

 <?php
// Version History
// ----------------------------
// YIS 2023-12-07 First Version
// YIS 2023-12-21 added Clientname & Printername lookup
// YIS 2024-06-24 changed file to WIFI-only and adapted to catalyst-center
// YIS 2024-07-10 changed IP and Device-Name Lookup to loopback on MAC-Address
// ----------------------------
//

// Ensure errors are displayed for debugging purposes
//	error_reporting(E_ALL);
//	ini_set('display_errors', 1);
// DEBUGGING END

require_once "checkMAC.php";
require_once "checkIP.php";
require_once "clientDetailsCC.php";
$config   = include "configCC.php";
$host = $config["host"];
$username = $config["username"];
$password = $config["password"];
$domainsuffix = $config['domainsuffix'];

$DeviceID = $_POST["DeviceID"];

// ====================================================
// ====== Authentication Part =========================

$auth    = curl_init();
$authURL = "https://{$host}/dna/system/api/v1/auth/token";
curl_setopt($auth, CURLOPT_URL, $authURL);
curl_setopt($auth, CURLOPT_POST, 1);
curl_setopt($auth, CURLOPT_POSTFIELDS, "{}"); // Empty JSON body
curl_setopt($auth, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($auth, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Content-Type: application/json",
    "Authorization: Basic " . base64_encode("$username:$password"), // Basic Auth header with base64 encoded username:password
]);

// Execute cURL request and capture the response
$auth_response = curl_exec($auth);

// Check for errors
if (curl_errno($auth)) {
    echo "xx Error:" . curl_error($auth);
}

// Get HTTP status code
$http_status = curl_getinfo($auth, CURLINFO_HTTP_CODE);

// Close cURL session
curl_close($auth);

// Decode JSON response to get the token
$auth_data = json_decode($auth_response, true);

/*
// Check HTTP status code for successful response
if ($http_status !== 200) {
    echo 'HTTP status code: ' . $http_status . "<br>";
    echo 'Response: ' . $auth_response . "<br>";
    exit;
}

// Check for "Unauthorized" message in response
if (isset($auth_data['message']) && $auth_data['message'] === "Unauthorized") {
    echo 'Authorization failed: Unauthorized<br>';
    exit;
}

// Check if the token exists in the response
if (isset($auth_data['Token'])) {
    $token = $auth_data['Token']; // Adjust the key according to your API response structure
    echo 'Token received: ' . $token . "<br>";
}
*/
$token = $auth_data["Token"]; // Adjust the key according to your API response

// ====================================================

if (Net_MAC::check($DeviceID)) {
    $mac = Net_MAC::format($DeviceID, "-", true);
    $URL = "https://{$host}/dna/intent/api/v1/client-detail?macAddress=$mac";
    echo "<br>Ergebnis für $mac<br><br><br>";
}

// Check if Syntax of the IP is ok
elseif (Net_CheckIP::check_ip($DeviceID)) {
    //get the result of API-Call
    $curlGetIp = curl_init();
    $getIpUrl  = "https://{$host}/api/fedsearch/v1/applications/dna/autocomplete?request=$DeviceID&type=Interface,User,EndDevice,NetworkDevice";
    curl_setopt($curlGetIp, CURLOPT_URL, $getIpUrl);
    curl_setopt($curlGetIp, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlGetIp, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curlGetIp, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curlGetIp, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $curlGetIp,
        CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13"
    );
    curl_setopt($curlGetIp, CURLOPT_HTTPHEADER, [
        "x-auth-token: " . $token,
        "Accept: application/json",
        "Content-Type: application/json",
    ]);

    $responseIP = curl_exec($curlGetIp);
    // debug responseIP
    //echo "Ausgabe responseIP: $responseIP<br>";

    // Check if there was an error during the request
    if (curl_errno($curlGetIp)) {
        echo "cURL error: " . curl_error($curlGetIp);
        exit();
    }

    // Close cURL session
    curl_close($curlGetIp);

    // Decode the JSON response
    $response_array = json_decode($responseIP, true);

    // Check if the response is an array and has at least one element
    if (is_array($response_array) && count($response_array) > 0) {
        // Access the "label" field
        $labelFromIp = $response_array[0]["label"];

        // Output the value of "label" (for testing purposes)
        //echo "Ausgabe Label: $labelFromIp<br>";
    }

    $URL = "https://{$host}/dna/intent/api/v1/client-detail?macAddress=$labelFromIp";
    echo "<br>Ergebnis für $DeviceID<br><br><br>";
}



//===========================================
// Syntax for device names like NBZRH12345 / ZHPCOM-xxxxxxxx
//
// ADJUST THIS AS YOU NEED
// DEVICE SYNTAX OF YOUR COMPANY
//
elseif (preg_match('/^(?:[a-zA-Z]{2,9}[0-9]{2,9}|zhpcom\-[A-Za-z0-9]{8})$/i', $DeviceID)) {

    //get the result of API-Call
    $curlGetIp = curl_init();
    $getIpUrl  = "https://{$host}/api/fedsearch/v1/applications/dna/autocomplete?request=$DeviceID&type=Interface,EndDevice,NetworkDevice";
    curl_setopt($curlGetIp, CURLOPT_URL, $getIpUrl);
    curl_setopt($curlGetIp, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlGetIp, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curlGetIp, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curlGetIp, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(
        $curlGetIp,
        CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13"
    );
    curl_setopt($curlGetIp, CURLOPT_HTTPHEADER, [
        "x-auth-token: " . $token,
        "Accept: application/json",
        "Content-Type: application/json",
    ]);

    $responseIP = curl_exec($curlGetIp);
// debug responseIP
//echo "<br>DEBUG: Ausgabe responseIP: $responseIP<br>";

    // Check if there was an error during the request
    if (curl_errno($curlGetIp)) {
        echo "cURL error: " . curl_error($curlGetIp);
        exit();
    }

    // Close cURL session
    curl_close($curlGetIp);

    // Decode the JSON response
    $response_array = json_decode($responseIP, true);

    // Check if the response is an array and has at least one element
    // Catalyst-Center is answering sometimes in wrong order, so need to fix array index manually
    $labelFromName = null;
    if (is_array($response_array) && count($response_array) > 0) {
        // Check if 'label' exists in index 0
        if (isset($response_array[0]["label"])) {
            $labelFromName = $response_array[0]["label"];
        }
        // Check if 'label' exists in index 1
        elseif (isset($response_array[1]["label"])) {
            $labelFromName = $response_array[1]["label"];
        }
    }

    //Output the value of "label" (for testing purposes)
    //echo "<br>DEBUG: Ausgabe Label: $labelFromName<br>";

// Check for dns suffix - win11 devices do not have any dns suffix!
    if (!preg_match('/^ZHPCOM-/i', $DeviceID)) {
    // DeviceID beginnt NICHT mit "ZHPCOM-" (KEIN win11 device)
        $deviceFQDN = $DeviceID.".".$domainsuffix;
    } else {    // DeviceID beginnt mit "ZHPCOM-" (win11 device)
        $deviceFQDN = $DeviceID;
    }
    $URL        = "https://{$host}/dna/intent/api/v1/client-detail?macAddress=$labelFromName";
    echo "<br>Ergebnis für $deviceFQDN<br><br><br>";
} else {
    echo "Eingabe war ungültig";
    exit();
}

// Now use the token in subsequent API requests
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
//wenn CURLOPT_RETURNTRANSFER auf 1 gesetzt ist, gibt die Funktion curl_exec das Resultat zurueck, also hier den JSON String,
//anstatt true oder false
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt(
    $ch,
    CURLOPT_USERAGENT,
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13"
);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-auth-token: " . $token,
    "Accept: application/json",
    "Content-Type: application/json",
]);

$result = curl_exec($ch);
// Check for errors
if (curl_errno($ch)) {
    echo "Error:" . curl_error($ch);
}
curl_close($ch);

$decodedResult = json_decode($result);
$prettyJson    = json_encode($decodedResult, JSON_PRETTY_PRINT);

//alles ausgeben
//echo '<pre>' . $prettyJson . '</pre>';

//nur ausgewählte Felder ausgeben
$decodedResponse = json_decode($prettyJson, true); // true for associative array
$requestedClient = null;
$emptyResponse   = null;
$emptyResponse   = $decodedResponse["detail"]["@count"];

if ($emptyResponse === 0) {
    print "Gerät nicht im Catalyst Center gefunden";
    exit();
} else {
    $connectionTypeResponse = $decodedResponse["detail"]["hostType"];
}

//========================================================
// Get VLAN Name from another API call
//
//get the result of API-Call
$curlGetVlanName = curl_init();
$GetVlanName     = "https://{$host}/api/v2/network-device/5882c88c-4eda-49bf-a8c0-fcfd5012f717/vlan";
curl_setopt($curlGetVlanName, CURLOPT_URL, $GetVlanName);
curl_setopt($curlGetVlanName, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlGetVlanName, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curlGetVlanName, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curlGetVlanName, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt(
    $curlGetVlanName,
    CURLOPT_USERAGENT,
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13"
);
curl_setopt($curlGetVlanName, CURLOPT_HTTPHEADER, [
    "x-auth-token: " . $token,
    "Accept: application/json",
    "Content-Type: application/json",
]);

$GetVlanName = curl_exec($curlGetVlanName);
// Close cURL session
curl_close($curlGetVlanName);

// debug GetVlanName
// echo "<br>DEBUG: Ausgabe GetVlanName: $GetVlanName<br>";

// Set the vlanId you want to filter for
$targetVlanId = $decodedResponse["detail"]["vlanId"];
$vlanName     = null;

// Decode the JSON response to an associative array
$VlanNameData = json_decode($GetVlanName, true);

// Check if decoding was successful
if ($VlanNameData === null) {
    die("Failed to decode JSON. Error: " . json_last_error_msg());
}

// Loop through the data to find the vlanName with the matching vlanId
foreach ($VlanNameData['response'] as $vlan) {
    // Check if 'vlanId' key exists in this $vlan item
    if (isset($vlan['vlanId'])) {
        // Debugging output to check the value and type of $vlan['vlanId']
        //echo "Current VLAN ID: ";
        //var_dump($vlan['vlanId']); // Shows value and type of $vlan['vlanId']
        //echo "<br>";

                                                // Check if it matches the target VLAN ID
        if ($vlan['vlanId'] == $targetVlanId) { // use == for type-flexible comparison
            $vlanName = $vlan['vlanName'];
            break;
        }
    } else {
        //echo "vlanId not found in this entry<br>";
    }
}

// DEBUG: Check if vlanName was found and display it

//    if ($vlanName) {
//        echo "VLAN Name for vlanId {$targetVlanId}: " . $vlanName;
//    } else {
//        echo "No VLAN found with vlanId {$targetVlanId}.";
//        echo "Current VLAN ID: ";
//            var_dump($vlan['vlanId']);
//}
//}

// END Get VLAN Name ========================================================

// Check if VLAN Name is found, else set a default message
if (! $vlanName) {
    $vlanName = "VLAN Name not found";
}

if ($connectionTypeResponse === "WIRELESS") {
    $requestedClient = new WirelessClient(
        //$decodedResponse["detail"]["vlanId"], //vlan
        $targetVlanId,
        $vlanName,                                                  //vlanName
        $decodedResponse["detail"]["location"],                     //location
        $decodedResponse["detail"]["hostName"],                     //hostname
        $decodedResponse["detail"]["hostIpV4"],                     //clientIP
        $decodedResponse["detail"]["hostMac"],                      //macAddress
        $decodedResponse["detail"]["connectionStatus"],             //status
        $decodedResponse["detail"]["connectedDevice"][0]["name"],   //apName
        $decodedResponse["detail"]["connectedDevice"][0]["mgmtIp"], //apIpAddress
        $decodedResponse["detail"]["ssid"],                         //ssid
        $decodedResponse["detail"]["authType"],                     //authType
        $decodedResponse["detail"]["frequency"],                    //frequency
        $decodedResponse["detail"]["dot11Protocol"],                //dot11Protocol
        $decodedResponse["detail"]["wlcName"]                       //wlcName
    );
}
/*	elseif ($connectionTypeResponse === "WIRED") {
		$requestedClient = new WiredClient(
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['vlan'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['vlanName'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['hostname'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['ipAddress']['address'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['macAddress']['octets'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['status'],
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['deviceName'],
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['clientInterface'],
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['deviceIpAddress']['address'],
       		$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['speed'],
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['ifDescr']
		);
	}*/else {
    print "Gerät nicht gefunden";
    exit();
}

// Output the selected values
//echo $requestedClient->toHtmlTable();
echo $requestedClient->toHtmlTableWithLabels();
