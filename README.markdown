This is the PHP Trestle Client.  If you are developing in PHP, the Trestle Client
gives you access to the power of Trestle with minimal fuss:

Example usage:

```php
require_once 'Trestle.php';

$trestle = new Trestle('api_key','api_secret');

// Create a new user account
$_args = array('username' => 'newuser','password' => 'abc123','email' => 'newuser@example.com');
$_user = $trestle->UserCreate($_args);

var_dump($_user);
```
