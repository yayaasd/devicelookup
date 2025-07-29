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

class BaseClient {
    public $vlan;
    public $vlanName;
    public $clientIP;
    public $status;
    public $macAddress;
    public $hostname;

    public function __construct($vlan, $vlanName, $hostname, $clientIP, $macAddress, $status) {
        $this->vlan = $vlan;
        $this->vlanName = $vlanName;
        $this->hostname = $hostname;
        $this->clientIP = $clientIP;
        $this->macAddress = $macAddress;
        $this->status = $status;
    }
}

////////////////////////////////////////////////////////////////
// Ab hier Wifi Client
////////////////////////////////////////////////////////////////

/*
class WirelessClient extends BaseClient {
	public $apName;
	public $apIpAddress;
	public $authorizationPolicy;
    public $ssid;

	public function __construct($vlan, $vlanName, $hostname, $clientIP, $macAddress, $status, $apName, $apIpAddress, $ssid) {
        parent::__construct($this->vlan = $vlan, $this->vlanName = $vlanName, $this->hostname = $hostname, $this->clientIP = $clientIP, $this->macAddress = $macAddress, $this->status = $status);
        $this->apName = $apName;
        $this->apIpAddress = $apIpAddress;
        $this->ssid = $ssid;
    }
     
    public function toHtmlTable() {
        $html = '<table border="1">';
        $html .= '<tr><th>Field</th><th>Value</th></tr>';

        foreach ($this->toArray() as $field => $value) {
            $html .= '<tr>';
            $html .= '<td>' . $field . '</td>';
            $html .= '<td>' . $value . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
   
    }

    public function toHtmlTableWithLabels() {
        $html = '<table border="0">';
     //   $html .= '<tr><th>Field</th><th>Value</th></tr>';

            $html .= '<tr>';
            $html .= '<td width=180px>' . 'AP Name' . '</td>';
            $html .= '<td>' . $this->apName . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'AP IP Adresse' . '</td>';
            $html .= '<td>' . $this->apIpAddress . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'VLAN' . '</td>';
            $html .= '<td>' . $this->vlanName . ' (' . $this->vlan . ')' . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Hostname' . '</td>';
            $html .= '<td>' . $this->hostname . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Client IP Adresse' . '</td>';
            $html .= '<td>' . $this->clientIP . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Client MAC Adresse' . '</td>';
            $html .= '<td>' . $this->macAddress . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Aktueller Status' . '</td>';
            $html .= '<td>' . $this->status . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Verbundene SSID' . '</td>';
            $html .= '<td>' . $this->ssid . '</td>';
            $html .= '</tr>';

        $html .= '</table>';
        return $html;
    }

}*/

////////////////////////////////////////////////////////////////
// Ab hier WIRED Client
////////////////////////////////////////////////////////////////

class WiredClient extends BaseClient {
    public $switchName;
    public $switchPort;
    public $switchIpAddress;
    public $speed;
    public $interfaceDescription;

    public function __construct($vlan, $vlanName, $hostname, $clientIP, $macAddress, $status, $switchName, $switchPort, $switchIpAddress, $speed, $interfaceDescription) {
        parent::__construct($this->vlan = $vlan, $this->vlanName = $vlanName, $this->hostname = $hostname, $this->clientIP = $clientIP, $this->macAddress = $macAddress, $this->status = $status);
        $this->switchName = $switchName;
        $this->switchPort = $switchPort;
        $this->switchIpAddress = $switchIpAddress;
        $this->speed = $speed;
        $this->interfaceDescription = $interfaceDescription;
    }
     
    public function toHtmlTable() {
        $html = '<table border="1">';
        $html .= '<tr><th>Field</th><th>Value</th></tr>';
        foreach ($this->toArray() as $field => $value) {
            $html .= '<tr>';
            $html .= '<td>' . $field . '</td>';
            $html .= '<td>' . $value . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public function toHtmlTableWithLabels() {
        $html = '<table border="0">';
      //  $html .= '<tr><th>Field</th><th>Value</th></tr>';

            $html .= '<tr>';
            $html .= '<td width=180px>' . 'Switch Name' . '</td>';
            $html .= '<td>' . $this->switchName . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Switchport' . '</td>';
            $html .= '<td>' . $this->switchPort . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Switch IP' . '</td>';
            $html .= '<td>' . $this->switchIpAddress . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'VLAN' . '</td>';
            $html .= '<td>' . $this->vlanName . ' (' . $this->vlan . ')' . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Hostname' . '</td>';
            $html .= '<td>' . $this->hostname . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Client IP Adresse' . '</td>';
            $html .= '<td>' . $this->clientIP . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>' . 'Client MAC Adresse' . '</td>';
            $html .= '<td>' . $this->macAddress . '</td>';
            $html .= '</tr>';
        
        //
        // Werte gem. Techmeeting vom 07.10.2024 deaktiviert
        //
        //    $html .= '<tr>';
        //    $html .= '<td>' . 'Aktueller Status' . '</td>';
        //    $html .= '<td>' . $this->status . '</td>';
        //    $html .= '</tr>';
        //    $html .= '<tr>';
        //    $html .= '<td>' . 'Verbindung' . '</td>';
        //    $html .= '<td>' . $this->speed . '</td>';
        //    $html .= '</tr>';
            
            $html .= '<tr>';
            $html .= '<td>' . 'Interface Description' . '</td>';
            $html .= '<td>' . $this->interfaceDescription . '</td>';
            $html .= '</tr>';

        $html .= '</table>';
        return $html;
    }


    public function toArray() {
	return get_object_vars($this);
    }
}
?>
