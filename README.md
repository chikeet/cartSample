# Sample shopping cart service

A code sample. 

- **Minimum PHP version:** 7.1
- **Dependencies:** only for tests (`Nette\Tester`)

Contains:
- **Cart** - simple shopping cart backend with interfaces (`CartService`, `CartItem`, interfaces `IStorage` and `IProduct`)
- Example/default implementations of interfaces (`DefaultProduct`, `DefaultStorage`). Used as default product class (`DefaultProduct`) and in tests (`DefaultStorage`).
- **Utils** - static utility classes (`ArrayStructureChecker`, `Percents`)

How to run tests in CLI:

1. Run `composer install` in project root folder. 
2. `cd tests`
3. `php "../vendor/nette/tester/src/tester.php" -c "php.ini" "cases/unit/"`
