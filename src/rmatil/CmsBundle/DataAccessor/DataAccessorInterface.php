<?php


namespace rmatil\CmsBundle\DataAccessor;

use rmatil\CmsBundle\Exception\EntityInvalidException;
use rmatil\CmsBundle\Exception\EntityNotInsertedException;
use rmatil\CmsBundle\Exception\EntityNotUpdatedException;
use rmatil\CmsBundle\Exception\EntityNotFoundException;

/**
 * An interface specifying the access to
 * entities stored in repositories
 */
interface DataAccessorInterface {

    /**
     * Return all objects of the type currently
     * stored in the database
     *
     * @return array[object]
     */
    public function getAll();

    /**
     * Returns the object identified by the given id.
     *
     * @param $id int The id of the object
     *
     * @return object
     *
     * @throws EntityNotFoundException If the entity is not found
     */
    public function getById($id);

    /**
     * Updates the given object and stores it in the database
     *
     * @param $object object The object to update (incl. its dependencies)
     *
     * @return object The updated object
     *
     * @throws EntityNotUpdatedException If given object couldn't be updated
     * @throws EntityInvalidException If the object identifier is missing
     * @throws EntityNotFoundException If the entity to update is not found in the database
     */
    public function update($object);

    /**
     * Inserts the given object into the database
     *
     * @param $object object The object to insert
     *
     * @return object The inserted object
     *
     * @throws EntityNotInsertedException If the given entity could not have been inserted
     */
    public function insert($object);


    /**
     * Delete the entity with the given id from the database
     *
     * @param $id int The id of the entity
     */
    public function delete($id);
}
