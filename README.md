# 🍎🥕 Fruits and Vegetables

## 🎯 Goal
We want to build a service which will take a `request.json` and:
* Process the file and create two separate collections for `Fruits` and `Vegetables`
* Each collection has methods like `add()`, `remove()`, `list()`;
* Units have to be stored as grams;
* Store the collections in a storage engine of your choice. (e.g. Database, In-memory)
* Provide an API endpoint to query the collections. As a bonus, this endpoint can accept filters to be applied to the returning collection.
* Provide another API endpoint to add new items to the collections (i.e., your storage engine).
* As a bonus you might:
  * consider giving an option to decide which units are returned (kilograms/grams);
  * how to implement `search()` method collections;
  * use latest version of Symfony's to embed your logic 

### ✔️ How can I check if my code is working?
You have two ways of moving on:
* You call the Service from PHPUnit test like it's done in dummy test (just run `bin/phpunit` from the console)

or

* You create a Controller which will be calling the service with a json payload

## 💡 Hints before you start working on it
* Keep KISS, DRY, YAGNI, SOLID principles in mind
* Timebox your work - we expect that you would spend between 3 and 4 hours.
* Your code should be tested

## When you are finished
* Please upload your code to a public git repository (i.e. GitHub, Gitlab)

## 🐳 Docker image
Optional. Just here if you want to run it isolated.

### 📥 Pulling image
```bash
docker pull tturkowski/fruits-and-vegetables
```

### 🧱 Building image
```bash
docker build -t tturkowski/fruits-and-vegetables -f docker/Dockerfile .
```

### 🏃‍♂️ Running container
```bash
docker run -it -w/app -v$(pwd):/app tturkowski/fruits-and-vegetables sh 
```

### 🛂 Running tests
```bash
docker run -it -w/app -v$(pwd):/app tturkowski/fruits-and-vegetables bin/phpunit
```

### ⌨️ Run development server
```bash
docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables php -S 0.0.0.0:8080 -t /app/public
# Open http://127.0.0.1:8080 in your browser
```

--- ---

### Learning
* [Doctrine](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/index.html)
* [Symfony](https://symfony.com/doc/current/index.html)

Looks like Doctrine is the most popular ORM for PHP. I'm not sure if it's the best choice for this project, but it's a good starting point.

Database Entities can be created with the following command:
```bash
php bin/console make:entity Fruit
```

The command will ask you for the name of the entity, the fields, and the relations. You can also use the `--regenerate` option to overwrite the existing entity.

Generate migration:
```bash
php bin/console make:migration
```
Run the migration:
```bash
php bin/console doctrine:migrations:migrate
```
When you run this command, Doctrine will execute the migration to create the fruits and vegetables tables with columns inherited from the Food class (id, name, and quantity).

- Comparing Doctrine with Eloquent. I think Eloquent use more magic when storing the data and with Doctrine you have to explicitly use the EntityManager.

- APIs are in the Controllers.
- For DTOs, there is the symfony library Symfony\Component\Validator\Constraints and to validate them Symfony\Component\Validator\Validator\ValidatorInterface can be used.


### Questions / Thoughts
* After looking into the data, request.json, I had questions about how to handle repetition of the same fruit/vegetable. For example, if I have a Apple with a quantity of 10 in the database, and I want to add another Apple with a quantity of 5, how do I handle this? Should I sum the quantities with the existing one or create a new one?
* Same as deleting, right now It's a hard delete. Maybe should be changed to soft delete, or reduce the quantity of the fruit/vegetable.
* I didn't store the units in the database. But maybe would be a good idea to store them to avoid confusion. To handle the case of the api returning the units, I created a trait that is being used by the Food Entity as a non-persistent property.

## Considerations / TODOs
1. Handling Repeated Fruits/Vegetables
 *  Add logic to handle repeated items in the collection services. Decide whether to sum the quantities or keep them as distinct items.
2. Soft Deletes
 *  Using soft deletes can help maintain historical data. Implement soft deletes by adding a deletedAt timestamp column and modifying the remove method to set this timestamp instead of deleting the record.
3. Storing Units in Database
 *  Consider storing units in the database for clarity and consistency. Update your entities to include a unit column.
4. Tests
 *  Write more. Right now, there are only a few tests and only testing the success cases.
 *  There is a deprecation notice when running the tests.
 *  Apis add tests are filling the database with data.
5. API
 *  Investigate how to add a global prefix /api/ to all the routes. Maybe it's inside the config folder.

## How long it took
* Took me about 2 hours fighting with php versions. Ended up with php 8.1.
* Investigating, learning and writing the code took me around 6 hours.

## How to run
```bash
composer install
```
It's using SQLite as database. So you need to create the DATABASE_URL variable in your .env same as in the .env.test file.
```
DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
```
Then run the migrations:
```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
That should be enough to run the application.
```bash
symfony server:start
```
To load the request.json file, assuming that the server is running on http://127.0.0.1:8000, you can use the following command:
```bash
curl -X POST -H "Content-Type: application/json" -d @request.json http://127.0.0.1:8000/process
```
Or call the endpoint /process and send the request.json file as a file in the body.

To run the tests:
```bash
bin/phpunit
```