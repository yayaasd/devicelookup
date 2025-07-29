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
// 
// ----------------------------
// YIS 2023-12-07 First Version
// YIS 2023-12-21 added Clientname & Printername lookup
// YIS 2024-10-01 Prime disabled for Wifi in the API call
// ----------------------------
//


// debug enable
//ini_set('display_startup_errors', 1);
//ini_set('display_errors', 1);
//error_reporting(-1);


	require_once "checkMAC.php";
	require_once "checkIP.php";
	require_once "clientDetailsPrime.php";
	$config = include('config.php');
	$host = $config["host"];
	$username = $config['username'];
	$password = $config['password'];
	$domainsuffix = $config['domainsuffix'];
	
	$DeviceID = $_POST["DeviceID"];


		if (Net_MAC::check($DeviceID)) {
		    $mac = Net_MAC::format($DeviceID,'-',true);
		    $URL = "https://{$host}/webacs/api/v4/data/ClientDetails.json?.full=true&macAddress=$mac&connectionType=WIRED";
		    echo "<br>Ergebnis für $mac<br><br><br>";
		}	

		elseif (Net_CheckIP::check_ip($DeviceID)) {
       // Syntax of the IP is ok
		    $URL = "https://{$host}/webacs/api/v4/data/ClientDetails.json?.full=true&ipAddress=$DeviceID&connectionType=WIRED";
		    echo "<br>Ergebnis für $DeviceID<br><br><br>";
		}

//===========================================
// Syntax for device names like NBZRH12345 / ZHPCOM-xxxxxxxx
//
// ADJUST THIS AS YOU NEED
// DEVICE SYNTAX OF YOUR COMPANY
//
		elseif (preg_match('/^(?:[a-zA-Z]{2,9}[0-9]{2,9}|zhpcom\-[A-Za-z0-9]{8})$/i', $DeviceID)) {
			$deviceFQDN = $DeviceID.".".$domainsuffix;
		    $URL = "https://{$host}/webacs/api/v4/data/ClientDetails.json?.full=true&hostname=$deviceFQDN&connectionType=WIRED";
		    echo "<br>Ergebnis für $deviceFQDN<br><br><br>";		
		}
		else{
			echo "Eingabe war ungültig";
			exit;
		}


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	//wenn CURLOPT_RETURNTRANSFER auf 1 gesetzt ist, gibt die Funktion curl_exec das Resultat zurueck, also hier den JSON String, anstatt true oder false
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13");
	curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	$result = curl_exec($ch);
	curl_close($ch);

	$decodedResult = json_decode($result);
	$prettyJson = json_encode($decodedResult, JSON_PRETTY_PRINT);

	//alles ausgeben
	//echo '<pre>' . $prettyJson . '</pre>';
	
	//nur ausgewählte Felder ausgeben
	$decodedResponse = json_decode($prettyJson, true); // true for associative array
	$requestedClient = null;
	$emptyResponse = null;
	$emptyResponse = $decodedResponse['queryResponse']['@count'];




	if ($emptyResponse === 0)
	{
		print "Gerät nicht im Prime gefunden";
		exit;
	}
	
	else{
		$connectionTypeResponse = $decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['connectionType'];
	}

	/* 
	//
	// PRIME DISABLED FOR WIFI LOOKUP
	//
	if ($connectionTypeResponse === "LIGHTWEIGHTWIRELESS") {
		$requestedClient = new WirelessClient(
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['vlan'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['vlanName'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['hostname'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['ipAddress']['address'],
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['macAddress']['octets'], 
        	$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['status'],
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['apName'],
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['apIpAddress']['address'],
			$decodedResponse['queryResponse']['entity'][0]['clientDetailsDTO']['ssid']
		);
	}

	//elseif ($connectionTypeResponse === "WIRED") {
	*/


	if ($connectionTypeResponse === "WIRED") {
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
	}

	else {
		print "Gerät nicht gefunden";
		exit;
	}



    // Output the selected values
    //echo $requestedClient->toHtmlTable();
    echo $requestedClient->toHtmlTableWithLabels();
	
?>
