<?php

/* Environment */
require_once('config.php');
require_once('functions.php');
//require_once('controller/database.controller.php');
require_once('controller/accounts.controller.php');
require_once('controller/posts.controller.php');
require_once('controller/locations.controller.php');
//$dbController = DatabaseController::getInstance();
$accountsController = AccountsController::getInstance();
$postsController = PostsController::getInstance();
$locationsController = LocationsController::getInstance();

/* Pre-execution checks */
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

/* Algo */
$data = Scrape($username);

// Check if account exists, or add it
$account_id = null;
$account = $accountsController->get('external_id', $data['profile']['id']);

if ( is_null($account) )
{
    echo 'Adding account.' . PHP_EOL;
    $account_id = $accountsController->insert($username, $data['profile']['id'], $data['profile']['external_url'], $data['profile']['biography'], $data['profile']['profile_pic'], $data['profile']['count_followers'], $data['profile']['count_following']);
} else
{
    echo 'Account already exists.' . PHP_EOL;
    $account_id = $account[0]['id_accounts'];
}

// Check if posts exist, or add them

foreach ( $data['posts'] as $key => $value )
{
    // Break cycle if post already exists
    $post_exists_check = $postsController->get( $value['id'] );
    if ( !is_null($post_exists_check) )
    {
        echo 'Break cycle: Profile is up to date. Most recent post with id \'' . $value['id'] . '\' already exists.' . PHP_EOL;
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
            echo 'Adding location id \'' . $location_id . '\' (' . $value['location']['name'] . ')' . PHP_EOL;
        }
    }

    // Adjust content for db insertion
    $external_url = FormInstagramPostUrl( $value['shortcode'] );
    $value['description'] = mb_convert_encoding($value['description'], 'UTF-8', 'Windows-1252');

    // Insert post
    $post_id = $postsController->insert($account_id, $location_id, $value['id'], $external_url, $value['shortcode'], $value['description']);
    echo 'Inserted post with id \'' . $post_id . '\'' . PHP_EOL;
}


/* Output */
//var_dump($data);