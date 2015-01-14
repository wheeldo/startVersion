<?php


interface complexData {
	
	/**
	 * stores the data in the database
	 */
	public function store();
	/**
	 * returns the data in the object
	 */
	public function get();
	/**
	 * updates data in database
	 * @param $data new data
	*/
	public function update($data);
	/**
	 * Deletes data from database
	*/
	public function delete();
	/**
	 * loads data from database
	 */
	public function load();
	/**
	 * creates a string from the object
	 */
	public function __toString();
}

?>