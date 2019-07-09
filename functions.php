<?php

function GetStringBetween($string, $start, $end)
{
    preg_match_all( '/' . preg_quote( $start, '/') . '(.*?)' . preg_quote( $end, '/') . '/', $string, $matches);
    return $matches[1];
}

function FormInstagramProfileUrl($name)
{
    $url = "https://www.instagram.com/" . $name . "/";
    return $url;
}

function FormInstagramPostUrl($shortcode)
{
    $url = "https://www.instagram.com/p/" . $shortcode . "/";
    return $url;
}

function ConvertJsonToArray($json)
{
    try
    {
        $arr = json_decode($json, true);
    } catch (Exception $e)
    {
        error_log( $e->getMessage() );
        return false;
    } finally
    {
        if ( isset($arr) && is_array($arr) ) {
            return $arr;
        } else {
            return false;
        }
    }

}

function WriteToFile($file, $message)
{
    $message = $message . PHP_EOL;
    $op = file_put_contents($file, $message , FILE_APPEND | LOCK_EX);
}

function SaveLog($profile, $message)
{
    global $config;
    $fileName = $config['path_logs'] . time() . '-' . $profile . '.log';
    WriteToFile($fileName, $message);
}

function GetInstagramPageJson($url, $convert = true)
{
    try
    {
        $html = file_get_contents($url);
    }
    catch (Exception $e) {
        // Catch exception (like SSL connection forcibly closed)
        error_log( $e->getMessage() );
        return false;
    }
    finally {
        // Separate strings
        $data = GetStringBetween($html, '<script type="text/javascript">window._sharedData = ', ';</script>');

        // Error catch
        if ( isset($data[0]) )
            $data = $data[0];
        
        // JSON to Array conversion
        if ($convert)
            $data = ConvertJsonToArray($data);

        // Return
        return $data;
    }
}

function Scrape($username, $loggingEnabled = false)
{
    // Catch malformed username
    if ( is_null($username) || $username == '' || preg_match('/\s/', $username) )
    {
        error_log('Error: Username is malformed.');
        return false;
    }

    // Data structure
    $data = array(
        'profile' => [],
        'posts' => []
    );

    // Form profile URL
    $url = FormInstagramProfileUrl($username);

    // Get profile's Json content
    $jsonObject = GetInstagramPageJson($url, false);

    // Catch error
    if ( $jsonObject === false )
    {
        error_log('Error: Failed fetching json data.');
        return false;
    }

    // Convert Json to associative array
    $arrObject = ConvertJsonToArray($jsonObject);

    // Create pointer to the 'posts' array key
    $postsObject = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];  //var_dump($postsObject);

    // Profile information
    $data['profile']['id'] = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['id'];
    $data['profile']['name'] = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['full_name'];
    $data['profile']['biography'] = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['biography'];
    $data['profile']['profile_pic'] = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url_hd'];
    $data['profile']['external_url'] = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['external_url'];
    $data['profile']['count_followers'] = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['edge_followed_by']['count'];
    $data['profile']['count_following'] = $arrObject['entry_data']['ProfilePage'][0]['graphql']['user']['edge_follow']['count'];

    // Mining single posts
    foreach ( $postsObject as $key => $value )
    {
        // Get post details - id, shortcode, description
        $data['posts'][$key]['id'] = $postsObject[$key]['node']['id'];
        $data['posts'][$key]['shortcode'] = $postsObject[$key]['node']['shortcode'];
        $data['posts'][$key]['description'] = $postsObject[$key]['node']['edge_media_to_caption']['edges'][0]['node']['text'];
        // Post location (might be unset)
        if ( isset($postsObject[$key]['node']['location']) )
            $data['posts'][$key]['location'] = $postsObject[$key]['node']['location'];

        // Generate URL to the single post page
        $postUrl = FormInstagramPostUrl( $data['posts'][$key]['shortcode'] );
        $data['posts'][$key]['url'] = $postUrl;

        // Images
        $data['posts'][$key]['images'] = array();

        $singlePostObject = GetInstagramPageJson($postUrl);

        if ( isset($singlePostObject['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']) )
        {
            // Post has multiple images
            $singlePostImages = $singlePostObject['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']['edges'];

            foreach ($singlePostImages as $keyImg => $valueImg)
            {
                // Each image is available in multiple sizes
                $imageSizes = $singlePostImages[$keyImg]['node']['display_resources'];
                // Pushing all the imagesizes[src, config_width, config_height] to the data array
                array_push($data['posts'][$key]['images'], $imageSizes);
            }
        } else
        {
            // Post has only one image
            $singlePostImages = $singlePostObject['entry_data']['PostPage'][0]['graphql']['shortcode_media']['display_resources'];
            // Pushing all the imagesizes[src, config_width, config_height] to the data array
            array_push($data['posts'][$key]['images'], $singlePostImages);
        }
    }

    // Encoding
    $data['profile']['biography'] = mb_convert_encoding($data['profile']['biography'], 'UTF-8', 'Windows-1252');

    // Logging
    if ($loggingEnabled)
        SaveLog($username, $jsonObject);

    // Return
    return $data;
}