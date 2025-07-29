<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
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

<html lang="de">

<head>
    <title>Device Lookup</title>
    <meta http-equiv="cache-control" content="no-cache /">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <link type="text/css" rel="stylesheet" href="./resources/style.css" />
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache /">

</head>

<body>
    <div id="header">
  <h1>Device Lookup</h1>

  <div id="header-right">
    <div id="header_logo">
      <img src="resources/your_logo.svg" alt="Logo">
    </div>
    <div id="changelog">CHANGELOG
      <div id="changelog-content"></div>
    </div>
  </div>
</div>

    <div id="main">
        <form id="macForm">
            <table>
                <tr>
                    <td>Device Lookup via MAC / IP / Hostname: </td>
                    <td><input id="DeviceID" name="DeviceID" size=25></td>
                    <td colspan="2"><button id="buttonID" type="button">Lookup</button></td>
                </tr>
            </table>
        </form>
    </div>


    <!-- Pop-up structure -->
    <div id="overlay"></div>

    <p>Aufgrund eines Systemwechsels werden Anfragen gegen zwei Systeme geprüft.<br><span style="color: blue">LAN Endpunkte kommen aus dem Prime Infrastructure</span> - <span style="color: red">Wifi Endpunkte aus dem Catalyst Center</span></p>

    <div class="container">
        <span id="spanResults">
            <div id="results"></div>
        </span>
        <span id="spanResultsCC">
            <div id="resultsCC"></div>
        </span>
    </div>


    <script>

        // Function to load the content of CHANGELOG.txt via AJAX
        function loadChangelog() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'CHANGELOG.txt?t=' + new Date().getTime(), true); // Append timestamp
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('changelog-content').innerHTML = xhr.responseText;
                }
            };
        xhr.send();
        }


        // Event listeners for mouseover and mouseout to show/hide changelog
        var changelog = document.getElementById('changelog');
        var changelogContent = document.getElementById('changelog-content');

        changelog.addEventListener('mouseover', function () {
            changelogContent.style.display = 'block';  // Show the content on mouseover
            loadChangelog();  // Load changelog content when mouseover occurs
        });

        changelog.addEventListener('mouseout', function () {
            changelogContent.style.display = 'none';  // Hide the content on mouseout
        });




            // Lookup device logic
            function lookupDevice() {
                var DeviceID = document.getElementById("DeviceID").value;           

                // Remove all blanks from the DeviceID
                DeviceID = DeviceID.replace(/\s+/g, '');            

                var button = document.getElementById("buttonID");


                // Disable the button and change its text
                button.disabled = true;
                button.innerText = "Please wait...";


                // Simulate lookup process with a 7-second delay
                setTimeout(function () {
                    button.disabled = false;
                    button.innerText = "Lookup";
                    console.log("Device lookup complete.");
                }, 7000); // Adjust timeout duration if needed

                
                // Request to PRIME INFRASTRUCTURE
                fetch('./php/getPrimeApiDevice.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'DeviceID=' + encodeURIComponent(DeviceID),
                })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("results").innerHTML = data;
                        applyBorders();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });

                // Request to CATALYSTCENTER
                fetch('./php/getCCApiDevice.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'DeviceID=' + encodeURIComponent(DeviceID),
                })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("resultsCC").innerHTML = data;
                        applyBorders();
                    });
            }

            // Add 'Enter' key event for input field
            var input = document.getElementById("DeviceID");
            input.addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("buttonID").click();
                }
            });

            // Add event listener for lookup button
            var button = document.getElementById("buttonID");
            if (button) {
                button.addEventListener("click", lookupDevice);
            } else {
                console.log("Button element not found.");
            }

            // Apply borders based on results
            function applyBorders() {
                const resultsDiv = document.getElementById('results');
                const resultsCCDiv = document.getElementById('resultsCC');

                const spanResults = document.getElementById('spanResults');
                const spanResultsCC = document.getElementById('spanResultsCC');

                if (resultsDiv.innerHTML.trim() !== "") {
                    spanResults.classList.add('border-blue');
                } else {
                    spanResults.classList.remove('border-blue');
                }
                if (resultsCCDiv.innerHTML.trim() !== "") {
                    spanResultsCC.classList.add('border-red');
                } else {
                    spanResultsCC.classList.remove('border-red');
                }
            };
    </script>

</body>

</html>
