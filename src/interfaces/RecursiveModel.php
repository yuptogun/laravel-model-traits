<?php
namespace Yuptogun\LaravelModel\Interfaces;

/**
 * apply it to any Laravel model that uses `IsRecursiveModel` trait
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
interface RecursiveModel
{
    /**
     * child CTE field
     *
     * @return string
     */
    public function getCteKeyChildAttribute();

    /**
     * parent CTE field
     *
     * @return string
     */
    public function getCteKeyParentAttribute();
}