DORM is a dumb little ORM library for Kohana.

DORM is built on the Datastore library and, at its core, is
database-agnostic.

# Defining a model

	class Model_Person extends DORM_Model {

		public static function initialize(DORM_Meta $meta)
		{
			$meta
				->fields(array(
					'_id' => new DORM_Field_Primary(),
					'name' => new DORM_Field_String(),
					'age' => new DORM_Field_Number(),
				))
		}
	}

## Fields

Models have one or more fields defined by using the `field` method on
the model's *meta object*. The method requires two parameters: the name
and the type of the field. An optional third parameter, an options
array, can be passed to define additional parameters, such as the
field's validation rules.

### Accessing & Setting

The value of a model's field can be accessed by using the model's `get`
method, and set with the model's `set` method. Alternatively, field
values can also be accessed and set as properties of the model object.

Internally, the model will use the field's `get` and `set` methods to
filter the value of the field when accessing and setting.

### Persistence

The values of fields are persisted to the datastore layer when the
model is saved. The model will use the field's `save` method to prepare
the value to be saved. Similarly, when loading a model's fields' values
from the datastore, the field's `load` method is used.


# Query Objects

There are four types of query objects (that correspond to the four types
of Datastore query objects): create, retrieve, update, delete. While
Datastore query objects operate on the datastore directly, DORM query
objects operate on models.

Query objects are created by using the factories provided by the DORM
class:

	DORM::create('person')
	DORM::retrieve('person')
	DORM::update('person')
	DORM::delete('person')

The return values of each query object's `->execute()` method is similar
to the corresponding Datastore query objects'. Create returns the
primary id of the created model; retrieve returns a collection of models
matching the query (or a single model instance if a limit of 1 was
used); update and delete return the number of models the query affected.

## Extending

# Retrieving

## Get a single model

All of the below expressions are equivelant (to the last) and return a
single Model_Person instance.

	new Model_Person($id);

	DORM::retrieve('person', $id);

	DORM::retrieve('person')
		->where('id', '=', $id)
		->limit(1)
		->execute();

## Get a collection of models

	DORM::retrieve('person')
		->where('age', '=', 20)
		->execute();


# Inserting and Updating

## Validation

The state of the model is validated when the model is saved. This is
done through the model's ``validate`` method. Each changed value is
validated according to the field's validation rules.

If a validation error exists, a ``Validation_Exception`` is thrown
containing the error(s).

### 
