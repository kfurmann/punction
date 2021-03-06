<?php
namespace Punction\Entities;

use Punction\Entities\Patient;

class PatientBuilder
{

    private $patient = NULL;

    function __construct()
    {
        $this->patient = new Patient();
    }

    function name($name)
    {
        $this->patient->setName($name);
        return $this;
    }
    
    function pesel($pesel)
    {
        $this->patient->setPesel($pesel);
        return $this;
    }
    
    function dataKategoryzacji($dataKategoryzacji)
    {
        $this->patient->setDataKategoryzacji($dataKategoryzacji);
        return $this;
    }
    
    function build()
    {
        return $this->patient;
    }

    /**
     * Maps properities of an $obj to $patient by calling setter of each property.
     * 
     * E.g. value of $obj->prop will become $patient->prop by calling $patient->setProp
     * 
     * @param unknown $patient
     * @param unknown $obj
     * @return unknown
     */
    public static function map($patient, $obj)
    {
        foreach (get_object_vars($obj) as $key => $value) {
            if ($value instanceof \stdClass && property_exists($value, 'date')) {
                $value = new \DateTime($value->date);
            }
            call_user_func(array(
                $patient,
                'set' . ucwords($key)
            ), $value);
        }
        return $patient;
    }
    
    // TO DEL
    /*
     * public static function toDatatablesString($patient)
     * {
     * $string = "";
     * foreach (get_object_vars($patient) as $key => $value) {
     * if ($value instanceof \DateTime) {
     * $value = $value->format('Y-m-d');
     * }
     * $string .= $value . ",";
     * }
     * $string = str_replace("\n", "", trim($string, ","));
     * return $string;
     * }
     */
}