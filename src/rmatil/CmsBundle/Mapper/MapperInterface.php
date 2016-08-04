<?php


namespace rmatil\CmsBundle\Mapper;


use rmatil\CmsBundle\Exception\MapperException;

interface MapperInterface {

    /**
     * Converts the given entities to the appropriate
     * data transfer objects
     *
     * @param array $entities The entities to transform
     *
     * @return array The resulting DTOs
     *
     * @throws MapperException If an inappropriate entity was passed to the mapper
     */
    function entitiesToDtos(array $entities) : array;

    /**
     * Converts the given DTOs to the appropriate Entities
     *
     * @param array $dtos The DTOs to converts
     *
     * @return array The resulting entities
     *
     * @throws MapperException If an inappropriate entity was passed to the mapper
     */
    function dtosToEntities(array $dtos) : array;

    /**
     * Converts the given entity into the appropriate DTO
     *
     * @param $entity object The entity to transform
     *
     * @return object The resulting DTO
     *
     * @throws MapperException If an inappropriate entity was passed to the mapper
     */
    function entityToDto($entity);

    /**
     * Converts the given DTO to the appropriate entity
     *
     * @param $dto object The DTO to transform
     *
     * @return object The resulting entity
     *
     * @throws MapperException If an inappropriate entity was passed to the mapper
     */
    function dtoToEntity($dto);
}
