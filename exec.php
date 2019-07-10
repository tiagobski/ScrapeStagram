<?php

/* ==================================================================
 * Environment
 * ================================================================== */

require_once('config.php');
require_once('functions.php');
require_once('controller/accounts.controller.php');
require_once('controller/posts.controller.php');
require_once('controller/locations.controller.php');
require_once('controller/images.controller.php');
$accountsController = AccountsController::getInstance();
$postsController = PostsController::getInstance();
$locationsController = LocationsController::getInstance();
$imagesController = ImagesController::getInstance();

/* ==================================================================
 * Pre-execution checks
 * ================================================================== */

if ( isset($_GET['username']) && !is_null($_GET['username']) )
{
    // Web browser launch.
    $username = $_GET['username'];
}
else if ( isset($argc) && isset($argv[1]) )
{
    // Command line launch.
    // argc = argument count.
    // argv = argument values. $argv[0] = script name.
    $username = $argv[1];
}
else
{
    die('Username unset.');
}

/* ==================================================================
 * Algorithm
 * ================================================================== */

$data = Scrape($username);

if ( !$data )
    die('Failed obtaining profile Json data.' . PHP_EOL);

// Check if account exists, or add it
$account_id = null;
$account = $accountsController->get('external_id', $data['profile']['id']);

if ( is_null($account) )
{
    $account_id = $accountsController->insert($username, $data['profile']['id'], $data['profile']['external_url'], $data['profile']['biography'], $data['profile']['profile_pic'], $data['profile']['count_followers'], $data['profile']['count_following']);
    echo 'Inserted account with id \'' . $account_id . '\'.' . PHP_EOL;
} else
{
    $account_id = $account[0]['id_accounts'];
    echo 'Account already exists, id \'' . $account_id . '\'.' . PHP_EOL;
}

// Check if posts exist, or add them
foreach ( $data['posts'] as $key => $value )
{
    // Break cycle if post already exists
    $post_exists_check = $postsController->get( $value['id'] );
    if ( !is_null($post_exists_check) )
    {
        echo 'Break: Profile is up to date.' . PHP_EOL;
        break;
    }

    // Location
    $location_id = 1;   // 1 is the PK for the 'Undefined' location
    if ( isset($value['location']) )
    {
        $location_id = $locationsController->get($value['location']['id']);

        // Location doesnt exist, add it
        if ( is_null($location_id) )
        {
            $location_id = $locationsController->insert($value['location']['id'], $value['location']['slug'], $value['location']['name']);
            
            // Output location info
            if ($location_id != 0) {
                echo 'Inserted location id \'' . $location_id . '\' (' . $value['location']['name'] . ')' . PHP_EOL;
            } else {
                echo 'Failed inserting location.' . PHP_EOL;
                var_dump($value['location']);
            }
        }
    }

    // Adjust 'post' content for db insertion
    $external_url = FormInstagramPostUrl( $value['shortcode'] );
    $value['description'] = mb_convert_encoding($value['description'], 'UTF-8', 'Windows-1252');

    // Insert post
    $post_id = $postsController->insert($account_id, $location_id, $value['id'], $external_url, $value['shortcode'], $value['description']);
    
    // Output post info
    if ($post_id != 0) {
        echo 'Inserted post with id \'' . $post_id . '\'.' . PHP_EOL;
    } else {
        echo 'Failed inserting post.' . PHP_EOL;
    }

    // Insert images
    foreach ( $value['images'] as $key_images => $value_images )
    {
        foreach ( $value_images as $key_single_image => $value_single_image )
        {
            // Break if image is already in db
            $image_exists_check = $imagesController->get( $value_single_image['src'] );
            if ( !is_null($image_exists_check) )
            {
                echo 'Image with URL \'' . $value_single_image['src'] . '\' already exists in database.' . PHP_EOL;
                break;
            }

            // If not exists, insert Image
            $image_id = 0;
            $image_id = $imagesController->insert($value_single_image['src'], $value_single_image['config_width'], $value_single_image['config_height']);
        
            // Break if image insertion fails
            if ($image_id != 0) {
                echo 'Inserted image with id \'' . $image_id . '\'.' . PHP_EOL;
            } else {
                echo 'Failed inserting image with URL \'' . $value_single_image['src'] . '\'.' . PHP_EOL;
                var_dump($value_single_image);
            }

            // Associate 'post' with 'image'
            if ($post_id != 0 && $image_id != 0)
            {
                // TODO: Currently have no way to error catch assoc_op, since table does not have a PK.
                $assoc_op = $imagesController->insertPostImageRelation($post_id, $image_id);
                echo 'Inserted relation between post id \'' . $post_id . '\' with image id \'' . $image_id . '\'.' . PHP_EOL;
            }
        }
    }
}