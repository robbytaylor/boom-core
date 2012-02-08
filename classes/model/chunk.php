<?php
/**
* Model for the chunk table.
* The chunk table is only useful with one of the related tables (chunk_text for example for a text chunk).
* This class has a custom constructor which joins a chunk type table depending on the value of the type column.
*
* Table name: chunk
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	slotname		****	string		****	Name of the slot that the chunk belongs to.
****	active_vid		****	integer		****	The ID of the related chunk table.
****	type			****	string		****	The type of chunk (text, feature, etc.)
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk';
	
	/**
	* Custom constructor for the slots tables.
	* Uses the type column to determine which table to join (if a record was loaded).
	* Joins the required slot type table and loads the slot data with the chunk data.
	*/
	public function __construct( $id = null )
	{
		parent::__construct( $id );
		
		if ($type !== null && $this->loaded())
		{
			$slot = array(
	        	'model' => "chunk_" . $this->type,
	        	'foreign_key' => "active_vid",
			);

	        $this->_belongs_to['slot'] = $slot;
			$this->with( 'slot' );
		}
	}
}

?>