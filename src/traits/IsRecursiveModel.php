<?php
namespace Yuptogun\LaravelModel\Traits;

/**
 * apply it to models that has both `id` and `parent_id` that references `id` of itself
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
trait IsRecursiveModel
{
    /**
     * parent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(self::class, $this->cte_key_child, $this->cte_key_parent);
    }

    /**
     * all parents of parent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function allParents()
    {
        return $this->parent()->with('allParents');
    }

    /**
     * children
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, $this->cte_key_parent, $this->cte_key_child);
    }

    /**
     * all children of children
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * top parent
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGrandparents($query)
    {
        return $query->where($this->cte_key_parent, '=', null);
    }

    /**
     * siblings
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param integer $cte_key_parent default: parent CTE value of current model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSiblings($query, $cte_key_parent = null)
    {
        return $query->where($this->cte_key_parent, '=', $cte_key_parent ?: $this->{$this->cte_key_parent});
    }

    /**
     * does this model has children?
     *
     * @return bool
     */
    public function getHasChildrenAttribute()
    {
        return $this->children()->exists();
    }

    /**
     * does this model has parent?
     *
     * @return boolean
     */
    public function getHasParentsAttribute()
    {
        return $this->parent()->exists();
    }

    /**
     * the level
     *
     * @return integer
     */
    public function getDepthAttribute()
    {
        return $this->has_parents ? $this->parent()->first()->depth + 1 : 1;
    }
}