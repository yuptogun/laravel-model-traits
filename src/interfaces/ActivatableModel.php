<?php
namespace Yuptogun\LaravelModel\Interfaces;

/**
 * apply it with `HasActivationAttributes` trait
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
interface ActivatableModel
{
    /**
     * field name that defines 'start at'
     *
     * @return string
     */
    public function getStartsAtFieldAttribute();

    /**
     * field name that defines 'end at'
     *
     * @return string
     */
    public function getEndsAtFieldAttribute();

    /**
     * the timestamp of infinity
     * 
     * default by trait: 10 years later (to prevent the overflow)
     *
     * @return integer
     */
    public function getInfinityAttribute();
}