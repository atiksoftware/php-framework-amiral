# PHP Framework ASW Amiral
PHP small and basic rest api framework

Its not MVC. Its CCV.

---

## Installation

### Using Composer

```sh
composer require atiksoftware/php-framework-amiral
```

---

## HOW TO USE
### FILE TREE
```
core
    controller (this folder containing your model/class files)
        Posts.php
    engine (this folder containing your middle function files)
        posts
          index.php
          list.php
          delete.php
          update.php
        users.php
index.php
```

### FILE EXAMPLES
*index.php*
```php
<?php 
 

/** Config  Paths */
define("DIR_ROOT", __DIR__);

define("DIR_CORE", DIR_ROOT . "/core");

define("DIR_SYSTEM", DIR_CORE . "/system");
define("DIR_ENGINE", DIR_CORE . "/engine");
define("DIR_CONTROLLER", DIR_CORE . "/controller");

/** Allow all request from all domains */
define("ALLOW_ORIGIN_ALL", true);

/** Config  Mongodb Database */
define("DB_HOSTNAME", "mongodb://127.0.0.1:27017");
define("DB_USERNAME", "robotorroot");
define("DB_PASSWORD", "rapunzel14A1");
define("DB_DATABASE", "app_firstbooking");


/** Its your composer autoloader file path. Yuou can change to your global folder if you want.
 * But its should contain Amiral framework.
 */
require_once DIR_CORE . '/packagist/vendor/autoload.php';

Atiksoftware\Amiral\Kernel::Run(); 
```

*core/controller/Posts.php*
```php
<?php 

    class Posts extends \Atiksoftware\Amiral\Engine
    { 
    }
```
this file your POSTS class, its auto connect to dv and collection.
You cna use like that in everywhere : $class->posts->Select();

*core/engine/posts/search.php*
Ex Req. URL: site.com/posts/search?search=deneme&limit=10&skip=8
```php
<?php 
	use \Atiksoftware\Amiral\Tools;
	use \Atiksoftware\Amiral\Router;
	use \Atiksoftware\Cover\Text;
 
	$skip = Tools::GetParam("skip") ?: 0;
	$limit = Tools::GetParam("limit") ?: 100;
	$search = Tools::GetParam("search") ?: "";

	$filter = [];
	if($search && $search != ""){ 
		$search = Text::fixChars(Text::toLower($search));
		$filter = [ '$and' => []];
		$parts = explode(" ",$search);
		foreach($parts as $part){
			$filter['$and'][] = [
				'text' => [
					'$regex' => new \MongoDB\BSON\Regex ( $part)
				]
			]; 
		}
		
	}

	$items = $class->posts->Select(
		$filter,
		["created_time" => -1],
		[],
		$limit,
		$skip
	);

	$return = array_values($items);
```
 
