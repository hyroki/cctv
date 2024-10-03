<?php
// URL where the JSON data is hosted
$jsonUrl = 'https://i-see.iconpln.co.id/backend/api/View/embedlink?url=dishub.kaltimprov.go.id';  // Replace with your actual JSON URL

// Fetch the JSON data from the URL
$jsonData = file_get_contents($jsonUrl);

if ($jsonData === false) {
    // Handle error if the JSON data cannot be fetched
    echo 'Error fetching camera data';
    exit;
}

// Decode the JSON data into a PHP array
$cameras = json_decode($jsonData, true);

if ($cameras === null || !isset($cameras['status']) || $cameras['status'] !== true) {
    // Handle error if the JSON data cannot be parsed or status is false
    echo 'Error parsing camera data or invalid data status';
    exit;
}

// Get the parameters from the URL (camId, cameraId, cameraName)
$camId = isset($_GET['camId']) ? $_GET['camId'] : null;
$cameraId = isset($_GET['cameraId']) ? $_GET['cameraId'] : null;
$cameraName = isset($_GET['cameraName']) ? $_GET['cameraName'] : null;

// Function to match the camera based on camId, cameraId, or cameraName
function findCamera($cameras, $camId, $cameraId, $cameraName) {
    foreach ($cameras['data'] as $site) {
        foreach ($site['areaTree'] as $area) {
            foreach ($area['cameraTree'] as $camera) {
                // Check if any of the provided parameters match
                if (
                    ($camId && $camera['camId'] === $camId) ||
                    ($cameraId && $camera['cameraId'] == $cameraId) ||
                    ($cameraName && $camera['cameraName'] === $cameraName)
                ) {
                    return $camera['streamingURL'];
                }
            }
        }
    }
    return null; // No match found
}

if ($camId || $cameraId || $cameraName) {
    // Search for the camera using any of the provided parameters
    $streamingURL = findCamera($cameras, $camId, $cameraId, $cameraName);

    if ($streamingURL) {
        // Redirect to the streamingURL if found
        header('Location: ' . $streamingURL);
        exit;
    } else {
        echo 'Camera not found';
    }
} else {
    echo 'No camId, cameraId, or cameraName provided';
}
?>
