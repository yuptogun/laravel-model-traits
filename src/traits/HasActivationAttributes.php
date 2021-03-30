<?php
namespace Yuptogun\LaravelModel\Traits;

/**
 * apply it when a model has 'start at' field and 'end at' field
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
trait HasActivationAttributes
{
    // ---- basic scopes ----

    /**
     * already activated
     * 
     * -------[ start ]-----[ NOW ]------> t
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeActivated($query)
    {
        return $query->where($this->starts_at_field, '<=', date('Y-m-d H:i:s'));
    }

    /**
     * not activated yet
     * 
     * --[ NOW ]-----------[ start ]-----> t
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeNotActivated($query)
    {
        return $query->where($this->starts_at_field, '>', date('Y-m-d H:i:s'));
    }

    /**
     * already deactivated
     * 
     * ----[ end ]----[ NOW ]------------> t
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeDeactivated($query)
    {
        return $query->where($this->ends_at_field, '<', date('Y-m-d H:i:s'));
    }

    /**
     * not deactivated yet
     * 
     * -------------------[ NOW ][ end ]-> t
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeNotDeactivated($query)
    {
        return $query->where($this->ends_at_field, '>=', date('Y-m-d H:i:s'));
    }

    /**
     * never expiring
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeNeverDeactivates($query)
    {
        return $query->where($this->ends_at_field, '>=', $this->infinity_datetime);
    }

    /**
     * active = activated + not deactivated
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeActive($query)
    {
        return $query->activated()->notDeactivated();
    }

    /**
     * mathematical reverse of active() scope
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where(function ($q) {
            $q->deactivated();
        })->orWhere(function ($q) {
            $q->notActivated();
        });
    }

    // ---- ordering scopes ----

    /**
     * order by "start at" asc
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSoonerToBeActivated($query)
    {
        return $query->orderBy($this->starts_at_field, 'asc');
    }

    /**
     * order by "start at" desc
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLaterToBeActivated($query)
    {
        return $query->orderBy($this->starts_at_field, 'desc');
    }

    /**
     * order by "end at" asc
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeSoonerToBeDeactivated($query)
    {
        return $query->orderBy($this->ends_at_field, 'asc');
    }

    /**
     * order by "end at" desc
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeLaterToBeDeactivated($query)
    {
        return $query->orderBy($this->ends_at_field, 'desc');
    }

    // ---- basic accessors ----

    /**
     * start at + end at
     *
     * @return array
     */
    public function getActivationPeriodAttribute()
    {
        return [$this->{$this->starts_at_field}, $this->{$this->ends_at_field}];
    }

    /**
     * is it already activated?
     *
     * @return boolean
     */
    public function getIsActivatedAttribute()
    {
        return strtotime($this->{$this->starts_at_field}) <= time();
    }

    /**
     * is it not activated yet?
     *
     * @return boolean
     */
    public function getIsNotActivatedAttribute()
    {
        return time() < strtotime($this->{$this->starts_at_field});
    }

    /**
     * is it already deactivated?
     *
     * @return boolean
     */
    public function getIsDeactivatedAttribute()
    {
        return strtotime($this->{$this->ends_at_field}) < time();
    }

    /**
     * is it not deactivated yet?
     *
     * @return boolean
     */
    public function getIsNotDeactivatedAttribute()
    {
        return time() <= strtotime($this->{$this->ends_at_field});
    }

    /**
     * is it *currently* active?
     *
     * @return boolean
     */
    public function getIsActiveAttribute()
    {
        return $this->is_activated && $this->is_not_deactivated;
    }

    /**
     * (logical reverse of is_active accessor)
     *
     * @return boolean
     */
    public function getIsInactiveAttribute()
    {
        return !$this->active;
    }

    /**
     * the (pseudo) infinity
     *
     * @return integer
     */
    public function getInfinityAttribute()
    {
        return strtotime('+10 years');
    }

    /**
     * the datetime string of (pseoudo) infinity
     *
     * @return string
     */
    public function getInfinityDatetimeAttribute()
    {
        return date('Y-m-d 23:59:59', $this->infinity);
    }

    /**
     * will it never cease?
     * 
     * @return boolean
     */
    public function getWillNeverBeDeactivatedAttribute()
    {
        return $this->infinity < strtotime($this->{$this->ends_at_field});
    }

    /**
     * will it last forever?
     *
     * @return boolean
     */
    public function getWillBeActivatedForeverAttribute()
    {
        return $this->will_never_be_deactivated;
    }

    // ---- mutators ----

    /**
     * `$this->starts_at = date('Y-m-d H:i:s');`
     *
     * @param string $datetime
     * @return void
     */
    public function setStartsAtAttribute($datetime)
    {
        $this->{$this->starts_at_field} = $datetime;
    }

    /**
     * `$this->ends_at = date('Y-m-d H:i:s');`
     *
     * @param string $datetime
     * @return void
     */
    public function setEndsAtAttribute($datetime)
    {
        $this->{$this->ends_at_field} = $datetime;
    }

    /**
     * `$this->activation = ['starts_at' => '2021-01-01 00:00:00', 'ends_at' => '2022-01-01 00:00:00'];`
     *
     * @param string[] $datetimes
     * @return void
     */
    public function setActivationAttribute($datetimes)
    {
        foreach (['starts_at', 'ends_at'] as $i => $attr) {
            if (isset($datetimes[$i])) {
                $this->{$attr} = $datetimes[$i];
            }
        }
    }

    /**
     * `$this->now_activating = true;`
     *
     * @param boolean $indeed
     * @return void
     */
    public function setNowActivatingAttribute($indeed = true)
    {
        if ($indeed) {
            $this->starts_at = date('Y-m-d H:i:s');
        }
    }

    /**
     * `$this->now_deactivating = true;`
     *
     * @param boolean $indeed
     * @return void
     */
    public function setNowDeactivatingAttribute($indeed = true)
    {
        if ($indeed) {
            $this->ends_at = date('Y-m-d H:i:s');
        }
    }

    /**
     * set to never expire
     * 
     * @param boolean $indeed
     * @return void
     */
    public function setNeverDeactivatesAttribute($indeed = true)
    {
        if ($indeed) {
            $this->ends_at = $this->infinity_datetime;
        }
    }

    /**
     * (alias of `setNeverDeactivatesAttribute`)
     * 
     * @param boolean $indeed
     * @return void
     */
    public function setNeverDeactivatingAttribute($indeed = true)
    {
        $this->never_deactivates = $indeed;
    }

    /**
     * (alias of `setNeverDeactivatesAttribute`)
     * 
     * @param boolean $indeed
     * @return void
     */
    public function setNeverEndsAttribute($indeed = true)
    {
        $this->never_deactivates = $indeed;
    }

    // ---- methods ----

    /**
     * check if active on certain moment of time
     * 
     * @param string|integer $datetime something `strtotime()` compatible
     */
    public function isActiveOn($datetime)
    {
        if (!is_int($datetime)) $datetime = strtotime($datetime);
        if (!$datetime) return false;
        return strtotime($this->{$this->starts_at_field}) <= $datetime
            && $datetime <= strtotime($this->{$this->ends_at_field});
    }

    /**
     * (alias of `isActiveOn`)
     *
     * @param string|integer $datetime
     * @return boolean
     */
    public function isActiveAt($datetime)
    {
        return $this->isActiveOn($datetime);
    }
}