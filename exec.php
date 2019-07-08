<html>
<head>
    <meta HTTP-EQUIV="Content-type" CONTENT="text/html; charset=UTF-8">
</head>
<body>

<?php

/* Environment */

ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

/* Functions */

function getStringBetween($string, $start, $end) {
    preg_match_all( '/' . preg_quote( $start, '/') . '(.*?)' . preg_quote( $end, '/') . '/', $string, $matches);
    return $matches[1];
}

function getInstagramPageJSON($url) {
    $html = file_get_contents($url);
    $data = getStringBetween($html, '<script type="text/javascript">window._sharedData = ', ';</script>')[0];
    $jsonObject = json_decode($data, true);
    return $jsonObject;
}

function formInstagramPostUrl($shortcode) {
    $url = "https://www.instagram.com/p/" . $shortcode . "/";
    return $url;
}

/* Algo */

$url = "https://www.instagram.com/supercarros.pt/";

// Mining profile feed - Get the Instagram's profile JSON content, create pointer to the 'posts' array key
$jsonObject = getInstagramPageJSON($url);
$postsObject = $jsonObject['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
//var_dump($postsObject);

// Mining single posts - Filling the data array
$data = array();
foreach ( $postsObject as $key => $value )
{
    // Get and save the shortcode
    $shortcode = $postsObject[$key]['node']['shortcode'];
    $data[$key]['shortcode'] = $shortcode;

    // Get and save the description
    $description = $postsObject[$key]['node']['edge_media_to_caption']['edges'][0]['node']['text'];
    $data[$key]['description'] = $description;

    // Generate URL to the single post page
    $postUrl = formInstagramPostUrl($shortcode);
    $data[$key]['url'] = $postUrl;

    // Get post content
    $data[$key]['images'] = array();

    $singlePostObject = getInstagramPageJSON($postUrl);

    if ( isset($singlePostObject['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']) ) {
        // Post has multiple images
        $singlePostImages = $singlePostObject['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']['edges'];

        foreach ($singlePostImages as $keyImg => $valueImg) {
            // Each image is available in multiple sizes
            $imageSizes = $singlePostImages[$keyImg]['node']['display_resources'];
            // Pushing all the imagesizes[src, config_width, config_height] to the data array
            array_push($data[$key]['images'], $imageSizes);
        }
    } else {
        // Post has only one image
        $singlePostImages = $singlePostObject['entry_data']['PostPage'][0]['graphql']['shortcode_media']['display_resources'];
        // Pushing all the imagesizes[src, config_width, config_height] to the data array
        array_push($data[$key]['images'], $singlePostImages);
    }
}

/* Output */

echo "<hr><h1>DATA ARRAY</h1>";

var_dump($data);

echo "<hr>";
?>

</body>
</html>

