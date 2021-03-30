<?php
namespace Yuptogun\LaravelModel\Interfaces;

/**
 * apply it to any Laravel model that uses `IsRecursiveModel` trait
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
interface ValueRotatingModel
{
    /**
     * get definition of the applicable fields and the options
     *
     * @return array[]
     */
    public function getRotatingFieldsAttribute();
}