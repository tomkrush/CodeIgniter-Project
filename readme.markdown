# Code Igniter Migrations & Active Record

To use this code download a copy of CodeIgniter and replace the application folder with the one in this repo. To run the migrations you must enter your MySQL database credentials into the database.php file.

## Running Migrations

Once you have connected CI to the database, migrations can be run by hitting the url /migrations. This is **not** for production projects. To create a migration goto the url /migrations/create/CreateProjectTable. CreateProjectTable can be replaced with any name you want for the migration. This does not generate the table code, you must do that yourself.

To edit migrations look at the applications/db in your CI project. There are examples in there.

## Active Record

This is an early loose port of ActiveRecord from Rails 2.x.

### Writing a model

	class Blogs_Model extends My_Model 
	{	
		function init()
		{
			$this->fields('rss_url', 'slug', 'name', 'description');
			$this->validates('name', array('presence' => TRUE, 'uniqueness' => TRUE));
			$this->validates('slug', array('presence' => TRUE, 'uniqueness' => TRUE));
			
			$this->before_save('force_slug');
		}
		
		function force_slug()
		{
			/* Force the slug into a format*/
		}
	}
	
The only requirements for a model is the fields must be defined.