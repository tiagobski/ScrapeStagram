# ScrapeStagram

PHP tool to scrape the 12 most recent posts from single Instagram profiles. Each post and it's details, such as location, description, and image sizes, is stored in a MySQL database. Subsequent scrapes of the same profile will not duplicate information in the database, with only the newer posts being stored.

This information is scraped directly through the web portal, since a JSON string containing valuable information can be found on each profile page.

## Execution

1. Execute through a command line environment:

```
php exec.php <username-here>
```

2. Execute through a web-browser:

```
http://host:port/scrapestagram/exec.php?username=<username-here>
```

## Database EER Diagram

![](database\db-scrapestagram.png)

## '$data' array structure

Retrieved by the `scrape()` function.

* Note that the `$data['posts']` array is ordered decreasingly by time of posting, meaning that the most recent post occupies position '0'.

```
$data['profile']['id']
$data['profile']['name']
$data['profile']['biography']
$data['profile']['profile_pic']
$data['profile']['external_url']
$data['profile']['count_followers']
$data['profile']['count_following']

$data['posts'][slot]['id']
$data['posts'][slot]['shortcode']
$data['posts'][slot]['description']
$data['posts'][slot]['url']
$data['posts'][slot]['images']
```

## TODO

* Update accounts over time, even if they already exist biography, follower/following counts and other factors can change. Not necessary to keep a record.
* Error-catch for the `post->image` database association query. Since this table has no PK, we cannot verify if the `INSERT` operation failed through it's value. It always returns '0'.
* Encoding: Database column `posts.description_posts` does not like UTF-8 characters, emojis, etc. Some encoding is done in the code with `mb_convert_encoding()`, which apparently works for the profile's biography.