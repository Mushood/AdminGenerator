## Admin Generator

This package is an admin generator that help you generate an admin interface based on the database structure. Its main goal is to quickly create the necessary files to create a CRUD for an entity. The generator is built using the laravel framework and vue JS framework. 

The following files are created:
- The model
- The validation request
- The request transformer
- The controller
- The index and create vue files
- The api routes
- The JS routes

Additionally, the package provides the following files:
- The admin and locale middleware
- A user model
- A page, block and seo structure for general pages
- An image handling structure
- Admin layout

The package is dependant on the following packages as well to quickly set up the project:
- Sluggable by cviebrock to generate slugs for models
- Translatable by dimsav to provide translation for models
- Laravel Socialite to allow login with FB, Google or Github
- Intervention image to handle images
- Laravel passport to manage tokens
- Laravel spatie permission to manage roles and permissions
- Laravel fractal to mask database structure to improve security
- Doctrine dbal to analyse db structure

## Installation

On a fresh laravel installation, you can install this package by running the following command: 

```
composer require sleekcube/admingenerator
```

Once installed, you need to setup laravel to use this package. Simply run the following command:
```
php artisan sleekcube:setup
```
Once done, run 
```
composer dump-autoload
```

After this, you need to hook up a database with your laravel application. 

Run the following commands:

```
php artisan config:cache;
php artisan storage:link;
php artisan migrate:fresh;
php artisan passport:install;
php artisan db:seed;
```

Once this is done, you should try to access the /admin route of your application. Ideally, you'd hook up the application with a v-host. Either way, be sure to change the APP_URL in your.env file.
As you can see in your UserSeeder, you have an admin with username: Admin@test.com and password: secret


## How to use the package
The package provides three commands for three types of database structure.

- An unrelated, standalone model
```
php artisan generate:simple [model]
```

- A related model
```
php artisan generate:belongto [model]
```

- A translated model
```
php artisan generate:translation [model]
```
NB: The model name needs to in singular form.

The package also provides a scheduler command. This allows you to order your migrations. 
First you need to publish the package config:
```
php artisan vendor:publish --provider="Sleekcube\AdminGenerator\AdminGeneratorServiceProvider" --tag="config"
```
In your config directory, you will find a sleekcube.php file. Here you can define an array as follows:

```
    'migrations' => [
        'tablename1' => 'simple', 
        'tablename2' => 'belongto',
        'tablename3' => 'translation',  
    ]
```
After the migration is done, to recompile your assets, you need to run 
```
npm run production
```

## Example
```
    // Migrations
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->timestamps();
        });
        
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('slug');
            $table->integer('tag_id')->unsigned();
            $table->foreign('tag_id')->references('id')->on('tags');
            $table->timestamps();
        });
        
        Schema::create('blogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('views')->default(false);
            $table->timestamps();
        });

        Schema::create('blog_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('blog_id')->unsigned();
            $table->string('locale')->index();

            $table->string('title');
            $table->text('description');
            $table->string('slug');

            $table->unique(['blog_id','locale']);
            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
        });
    }
```

```
    // in config/sleecube.php
    'migrations' => [
        'tag'       => 'simple', 
        'article'   => 'belongto',
        'blog'      => 'translation',  
    ]
```

This will create the following migrations

```
php artisan generate:simple tag;
php artisan generate:belongto article;
php artisan generate:translation blog;
```

Recompile your assets and checkout the following urls:

- /admin/tag
- /admin/article
- /admin/blog

***Hazza***

## Constraints
As a security feature, the id of the model is masked from the front. The mapping is always done using slugs which means that the model always need to have a string which will be used as the source for the slug generation.

## Future Developments
- Test for the package
- Generation of tests for the CRUD generated
- Linking translated models
- Cater for many-to-many relationships
- Cater for has-Many relations
- Handle more form types. Currently only, string, text and integer