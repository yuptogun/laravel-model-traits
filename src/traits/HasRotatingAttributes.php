<?php
namespace Yuptogun\LaravelModel\Traits;

/**
 * apply it when certain fields has typical values that are being switched/rotated
 * 
 * * `'season' => ['spring', 'summer', 'fall', 'winter']`
 * * `'used' => [1, 0]`
 * 
 * @author Eojin Kim <eojin1211@hanmail.net>
 */
trait HasRotatingAttributes
{
    /**
     * (immediately) rotate the field
     *
     * @param string $field
     * @param string|boolean $direction 'next' == true or 'prev' == false
     * @return boolean true if all updates successful, false otherwise
     */
    public function rotateField($field, $direction = 'next')
    {
        if (!$this->checkIfSwitchable($field)) return false;

        $options = $this->rotating_fields[$field];

        // get current index
        $oldIndex = 0;
        foreach ($options as $index => $value) { if ($this->{$field} == $value) { $oldIndex = $index; break; } }

        // get next index
        $newIndex = $oldIndex;
        if ($direction === FALSE || strtolower($direction) == 'prev') $newIndex--;
        if ($direction === TRUE  || strtolower($direction) == 'next') $newIndex++;
        if ($newIndex < 0 || $newIndex > count($options) - 1) $newIndex = (count($options) + $newIndex) % count($options);

        // update
        return (bool) $this->update([$field => $options[$newIndex]]);
    }

    /**
     * shorthand for options that are only two
     *
     * @param string $field
     * @return boolean
     */
    public function switchField($field)
    {
        if (!$this->checkIfRotatable($field)) return false;

        return $this->rotateField($field);
    }

    public function checkIfRotatable($field)
    {
        if (!isset($this->rotating_fields)) {
            throw new \Exception('getRotatingFieldsAttribute() method undefined!');
            return false;
        }
        if (!isset($this->rotating_fields[$field])) {
            throw new \Exception('$this->rotating_fields has no definition for '.$field.' field!');
            return false;
        }
        if (!is_array($this->rotating_fields[$field])) {
            throw new \Exception('$this->rotating_fields[\''.$field.'\'] should be a simple array!');
            return false;
        }
        return true;
    }

    public function checkIfSwitchable($field)
    {
        $rotatable = $this->checkIfRotatable($field);
        if (count($this->rotating_fields[$field]) <> 2) {
            throw new \Exception('options too many!');
            return false;
        }
        return $rotatable;
    }
}