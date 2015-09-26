<?php


namespace rmatil\cms\DataAccessor;


interface DataAccessorInterface {

    public function getAll();

    public function getById($id);

    public function update($object);

    public function insert($object);

    public function delete($id);
}