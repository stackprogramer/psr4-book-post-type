# PSR-4 WordPress Plugin
WordPress Standard Plugin with Support [PSR-4](https://www.php-fig.org/psr/psr-4/) autoloading.

# PSR-4: Autoloader
This PSR describes a specification for [autoloading](http://php.net/autoload) classes from file
paths. It is fully interoperable, and can be used in addition to any other
autoloading specification, including [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md). This PSR also describes where
to place files that will be autoloaded according to the specification.

# Structure
In the below you can see the folder structure and classes:
  
```bash
includes
    ├── Folder
    │   └── Test.php
    └── Folder2
        └── Test2.php
```
The example of the `Test.php`
```
namespace Folder;

class Test
{
    /**
     * @return string
     */
    public static function getHelloWorld()
    {
        return 'Hello World!';
    }
}
```
And you can run above class in your code with below command:
```
echo Folder\Test::getHelloWorld();
```