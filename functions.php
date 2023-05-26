<?php
global $wpdb;

// inserts location data into db
function insert_location_data($location) {
    global $wpdb;
    $table_name = 'locationdat';

    $data = array(
        'Location' => $location,
        'Time' => current_time('mysql')
    );

    $wpdb->insert($table_name, $data);

    echo '<script>console.log("Data inserted successfully.");</script>'; // debug
}

// gets location name based on lat and long
function get_location_name($lat, $lon) {
    $api_key = 'C815e7e6f6adf63781437395939c7e9d';
    $api_url = 'https://api.openweathermap.org/geo/1.0/reverse?lat=' . urlencode($lat) . '&lon=' . urlencode($lon) . '&limit=1&appid=' . $api_key;

    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        echo '<p>Failed to retrieve location data. Please try again later.</p>';
        return false;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        echo '<p>Failed to retrieve location data. Please check the coordinates and try again.</p>';
        return false;
    }

    $location_data = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($location_data) || !isset($location_data[0]['name'])) {
        echo '<p>No location found for the provided coordinates.</p>';
        return false;
    }

    insert_location_data($location_data[0]['name']); // insert location data into db

    // return the location name
    return $location_data[0]['name'];
}

// retrieves weather data based on the city name or coords.
function get_weather_data($city = null, $coords = null) {
    $api_key = 'C815e7e6f6adf63781437395939c7e9d';

    if ($city !== null) {
        $api_url = 'https://api.openweathermap.org/data/2.5/weather?q=' . urlencode($city) . '&appid=' . $api_key;
    } elseif ($coords !== null) {
        $api_url = 'https://api.openweathermap.org/data/2.5/weather?lat=' . urlencode($coords['lat']) . '&lon=' . urlencode($coords['lon']) . '&appid=' . $api_key;
    } else {
        return false;
    }

    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        return false;
    }

    return json_decode(wp_remote_retrieve_body($response), true);
}

// displays weather data on the page
function display_weather_data($data, $location_name = null, $coords = null) {
    if ($location_name) {
        echo '<h2>Weather for ' . esc_html($location_name);
        if ($coords) {
            echo ' (' . esc_html($coords['lat']) . ', ' . esc_html($coords['lon']) . ')';
        }
        echo '</h2>';
    }

    if (isset($data['main'])) {
        $temp_kelvin = $data['main']['temp'];
        $feels_like_kelvin = $data['main']['feels_like'];
        $temp_celsius = round($temp_kelvin - 273.15, 2);
        $feels_like_celsius = round($feels_like_kelvin - 273.15, 2);

        echo '<p class="temperature" data-kelvin="' . esc_attr($temp_kelvin) . '">Temperature: ' . esc_html($temp_kelvin) . ' Kelvin</p>';
        echo '<p class="temperature" data-kelvin="' . esc_attr($feels_like_kelvin) . '">Feels Like: ' . esc_html($feels_like_kelvin) . ' Kelvin</p>';
        echo '<p>Humidity: ' . esc_html($data['main']['humidity']) . '%</p>';
        echo '<p>Wind Speed: ' . esc_html($data['wind']['speed']) . ' m/s</p>';
    }

    if (isset($data['daily'])) {
        echo '<h2>Forecast for the next 7 days</h2>';
        foreach ($data['daily'] as $day) {
            echo '<p>Day ' . esc_html($day['dt']) . ':</p>';
            echo '<p>Temperature: ' . esc_html($day['temp']['day']) . ' Kelvin</p>';
            echo '<p>Feels Like: ' . esc_html($day['feels_like']['day']) . ' Kelvin</p>';
            echo '<p>Humidity: ' . esc_html($day['humidity']) . '%</p>';
            echo '<p>Wind Speed: ' . esc_html($day['wind_speed']) . ' m/s</p>';
        }
    }

    if (!isset($data['main']) && !isset($data['daily'])) {
        echo '<p>Sorry, we couldn\'t fetch the weather data. Please check the name of the city or the coordinates and try again.</p>';
    }
}

function weather_theme_scripts() {
    wp_enqueue_style('style-name', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'weather_theme_scripts');
?>
