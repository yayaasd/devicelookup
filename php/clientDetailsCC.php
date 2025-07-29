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

// Ensure errors are displayed for debugging purposes
//  error_reporting(E_ALL);
//  ini_set('display_errors', 1);

// BaseClient class definition
class BaseClient
{
    public $vlan;
    public $vlanname;
    public $location;
    public $clientIP;
    public $status;
    public $macAddress;
    public $hostname;

    public function __construct($vlan, $vlanname, $location, $hostname, $clientIP, $macAddress, $status)
    {
        $this->vlan       = $vlan;
        $this->vlanname   = $vlanname;
        $this->location   = $location;
        $this->hostname   = $hostname;
        $this->clientIP   = $clientIP;
        $this->macAddress = $macAddress;
        $this->status     = $status;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}

////////////////////////////////////////////////////////////////
// WirelessClient class definition
////////////////////////////////////////////////////////////////

class WirelessClient extends BaseClient
{
    public $apName;
    public $apIpAddress;
    public $authorizationPolicy;
    public $ssid;
    public $authType;
    public $frequency;
    public $dot11Protocol;
    public $wlcName;

    public function __construct($vlan, $vlanname, $location, $hostname, $clientIP, $macAddress, $status, $apName, $apIpAddress, $ssid, $authType, $frequency, $dot11Protocol, $wlcName)
    {
        parent::__construct($vlan, $vlanname, $location, $hostname, $clientIP, $macAddress, $status);
        $this->apName        = $apName;
        $this->apIpAddress   = $apIpAddress;
        $this->ssid          = $ssid;
        $this->authType      = $authType;
        $this->frequency     = $frequency;
        $this->dot11Protocol = $dot11Protocol;
        $this->wlcName       = $wlcName;
    }

    public function toHtmlTable()
    {
        $html = '<table border="1">';
        $html .= '<tr><th>Field</th><th>Value</th></tr>';

        foreach ($this->toArray() as $field => $value) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($field) . '</td>';
            $html .= '<td>' . htmlspecialchars($value) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    public function toHtmlTableWithLabels()
    {
        $html = '<table border="0">';
        $html .= $this->generateRow('Location Name', $this->location);
        $html .= $this->generateRow('AP Name', $this->apName);
        $html .= $this->generateRow('AP IP Address', $this->apIpAddress);
        $html .= $this->generateRow('VLAN', $this->vlan . ' [' . $this->vlanname . ']');
        $html .= $this->generateRow('Client Hostname', $this->hostname);
        $html .= $this->generateRow('Client IP Address', $this->clientIP);
        $html .= $this->generateRow('Client MAC Address', $this->macAddress);
        $html .= $this->generateRow('Current Status', $this->status);
        $html .= '<tr><td colspan="2"><br><b>Wifi Details:</b></td></tr>';
        $html .= $this->generateRow('SSID', $this->ssid);
        $html .= $this->generateRow('Auth. Method', $this->authType);
        $html .= $this->generateRow('2.4Ghz / 5 Ghz', $this->frequency);
        $html .= $this->generateRow('802.11 Standard', $this->dot11Protocol);
        $html .= $this->generateRow('WLC', $this->wlcName);
        $html .= '</table>';
        return $html;
    }

    private function generateRow($label, $value)
    {
        return '<tr><td width="180px">' . htmlspecialchars($label) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
    }
}

////////////////////////////////////////////////////////////////
// WiredClient class definition
////////////////////////////////////////////////////////////////

class WiredClient extends BaseClient
{
    public $switchName;
    public $switchPort;
    public $switchIpAddress;
    public $speed;
    public $interfaceDescription;

    public function __construct($vlan, $vlanname, $location, $hostname, $clientIP, $macAddress, $status, $switchName, $switchPort, $switchIpAddress, $speed, $interfaceDescription)
    {
        parent::__construct($vlan, $vlanname, $location, $hostname, $clientIP, $macAddress, $status);
        $this->switchName           = $switchName;
        $this->switchPort           = $switchPort;
        $this->switchIpAddress      = $switchIpAddress;
        $this->speed                = $speed;
        $this->interfaceDescription = $interfaceDescription;
    }

    public function toHtmlTable()
    {
        $html = '<table border="0">';
        $html .= '<tr><th>Field</th><th>Value</th></tr>';
        foreach ($this->toArray() as $field => $value) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($field) . '</td>';
            $html .= '<td>' . htmlspecialchars($value) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public function toHtmlTableWithLabels()
    {
        $html = '<table border="1">';
        $html .= $this->generateRow('Location Name', $this->location);
        $html .= $this->generateRow('Switch Name', $this->switchName);
        $html .= $this->generateRow('Switch Port', $this->switchPort);
        $html .= $this->generateRow('Switch IP', $this->switchIpAddress);
        $html .= $this->generateRow('VLAN', $this->vlan . '/' . $this->vlanname);
        $html .= $this->generateRow('Hostname', $this->hostname);
        $html .= $this->generateRow('Client IP Address', $this->clientIP);
        $html .= $this->generateRow('Client MAC Address', $this->macAddress);
        $html .= $this->generateRow('Current Status', $this->status);
        $html .= $this->generateRow('Connection Speed', $this->speed);
        $html .= $this->generateRow('Interface Description', $this->interfaceDescription);
        $html .= '</table>';
        return $html;
    }

    private function generateRow($label, $value)
    {
        return '<tr><td width="180px">' . htmlspecialchars($label) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
    }
}
