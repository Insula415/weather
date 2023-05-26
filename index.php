<?php get_header(); ?>

<div id="content">
    <?php
    // set london as default
    $city = isset($_GET['city']) ? $_GET['city'] : 'London';
    $lat = isset($_GET['lat']) ? $_GET['lat'] : null;
    $lon = isset($_GET['lon']) ? $_GET['lon'] : null;

    if ($lat && $lon) {
        if (!is_numeric($lat) || !is_numeric($lon)) {
            echo '<p>Invalid coordinates. Please enter valid numbers.</p>';
        } else {
            $location_name = get_location_name($lat, $lon);
            $weather_data = get_weather_data(null, array('lat' => $lat, 'lon' => $lon)); // Pass coordinates as an array
            display_weather_data($weather_data, $location_name, array('lat' => $lat, 'lon' => $lon)); // Pass location name and coordinates
        }
    } else {
        if (!preg_match("/^[a-zA-Z ]{1,50}$/", $city)) {
            echo '<p>Invalid city name. Please enter a valid city name (letters and spaces only, max 50 characters).</p>';
        } else {
            $weather_data = get_weather_data($city);
            display_weather_data($weather_data, $city);
        }
    }
    ?>

    <!-- user interface -->
    <br>
    <button id="cityBtn" class="active" onclick="toggleInput('city')">City</button>
    <button id="coordsBtn" onclick="toggleInput('coords')">Coordinates</button>
    <br>
    <br>

    <form id="cityForm" action="" method="get">
        <label for="city">Enter a City:</label><br>
        <input type="text" id="city" name="city" required><br>
        <input type="submit" value="Get Weather">
    </form>

    <form id="coordsForm" action="" method="get" style="display: none;">
        <label for="lat">Enter Latitude:</label><br>
        <input type="text" id="lat" name="lat" required><br>
        <label for="lon">Enter Longitude:</label><br>
        <input type="text" id="lon" name="lon" required><br>
        <input type="submit" value="Get Weather">
    </form>
</div>
<!-- end user interface -->

<script>
    function toggleInput(type) {
        var buttons = document.getElementsByTagName('button');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('active');
        }

        document.getElementById(type + 'Btn').classList.add('active');

        var cityForm = document.getElementById('cityForm');
        var coordsForm = document.getElementById('coordsForm');

        if (type === 'city') {
            cityForm.style.display = 'block';
            coordsForm.style.display = 'none';
        } else {
            cityForm.style.display = 'none';
            coordsForm.style.display = 'block';
        }
    }

    function kelvinToCelsius(kelvin) {
        return (kelvin - 273.15).toFixed(2);
    }

    var tempElements = document.querySelectorAll('.temperature');
    tempElements.forEach(function (element) {
        element.addEventListener('click', function () {
            var kelvinValue = parseFloat(this.dataset.kelvin);
            var celsiusValue = kelvinToCelsius(kelvinValue);
            this.innerHTML = kelvinValue + ' Kelvin (' + celsiusValue + ' Celsius)';
        });
    });
</script>

<?php get_footer(); ?>
