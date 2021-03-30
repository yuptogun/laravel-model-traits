<?php
namespace Yuptogun\LaravelModel\Interfaces;

/**
 * apply it to any Laravel model that uses `HasSortableOrderAttribute` trait
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
interface SortableModel
{
    /**
     * get definition of the applicable field name
     *
     * @return string
     */
    public function getSortableOrderFieldAttribute();
}