Phostgre
===========
Phostgre is a small group of classes that provides the connection and CRUD operations between a PHP script and a  PostgreSQL database.

It contains an Engine class, which is meant to be the bridge between PHP and the PostgreSQL Database System.

-----------

It's very simple to use! Follow these instructions and feel free to customize them as you do, if you know what you're doing.

* Configure your database settings in "load.php".
	
		define("PgSQL_HOSTNAME", "<your database host address>");
		define("PgSQL_USERNAME", "<your postgresql username>");
		define("PgSQL_PASSWORD", "<your postgresql password>");
		define("PgSQL_DATABASE", "<your database name>");
		define("PgSQL_PORT"    , "<your postgresql database port, which is usually 5432");

* Create a new folder named "phostgre" in your project's root directory, and place the "lib" folder and "load.php" in it.

* Call "load.php" once from your script.

		 require_once("phostgre/load.php");
	
* Enjoy the Engine class!


		$engine = new Engine(); // Create an instance
		$engine->setQuery("SELECT * FROM employees WHERE salary > 1000;"); // Set a query.
		$results = $engine->loadMultiple(); // Load results as a 2d array, suitable for a foreach loop.
		if($engine->hasErrors()) print $engine->complain(); // Check for errors.
		$engine->addEmployee("Mehmet Seckin", 5000, "2013-01-01"); // Call your stored procedures right through the Engine.
		$employeeAdded = $engine->loadBoolean(); // Load a boolean result.
