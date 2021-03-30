<?php
namespace Yuptogun\LaravelModel\Traits;

/**
 * apply it to models that has 'sort' field
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
trait HasSortableOrderAttributes
{
    /**
     * mass update the field
     *
     * @param array $orders new sort set
     * @param boolean $byIndex true if indexes are to be sorted
     * @return boolean true if all successful
     * @todo test $byIndex == true case
     */
    public function sortBySortableOrder($orders, $byIndex = false)
    {
        // 필요한 설정값이 없거나 입력값이 엉망이면 중단한다.
        if (!isset($this->sortable_order_field) || !is_array($orders)) return false;
        $field = $this->sortable_order_field;

        // e.g. [0 => 3, 1 => 1, 2 => 2]
        if ($byIndex) {

            $newOrders = [];
            foreach ($orders as $newIndex => $oldIndex) $newOrders[$oldIndex - 1] = $newIndex + 1;

            $models = self::orderBy($field)->get();
            foreach ($models as $index => $model) {
                if ($model->{$field} != $newOrders[$index]) {
                    if (!$model->update([$field => $newOrders[$index]])) return false;
                }
            }

        // e.g. [0 => 3136, 1 => 3135, 2 => 2];
        } else {
            foreach ($orders as $index => $key) {
                if (!$model = self::find($key)) return false;
                if (!$model->update([$field => $index + 1])) return false;
            }
        }

        return true;
    }

    /**
     * set value of 'sort' field
     *
     * @param mixed $set 'new' or integer
     * @return void if no `$set` given nothing would happen
     */
    public function setOrder($set = null)
    {
        $order = $this->{$this->sortable_order_field};
        if (is_int($set)) {
            $order = $set;
        } else if (strtolower($set) == 'new') {
            $latest = (new self)->orderBy($this->sortable_order_field, 'desc')->first()->{$this->sortable_order_field};
            $order = $latest + 1;
        }
        $this->{$this->sortable_order_field} = $order;
    }
}