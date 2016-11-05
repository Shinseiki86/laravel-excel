<?php
namespace Cyberduck\LaravelExcel\Serialiser;

use Illuminate\Database\Eloquent\Model;
use Cyberduck\LaravelExcel\Contract\SerialiserInterface;

class BasicSerialiser implements SerialiserInterface
{
    public function getData(Model $data)
    {
        return $data->toArray();
    }

    public function getDataFromStdClass($data) {
        return get_object_vars($data);
    }

    public function getHeaderRow(array $data=null)
    {
        if($data != null) {
            // Get the first row
            $firstRow = reset($data);

            // Get the array/object keys
            if (is_array($firstRow))
                $tableHeading = array_keys($firstRow);
            else
                $tableHeading = array_keys(get_object_vars($firstRow));

            //return the heading tab
            return $tableHeading;
        }

        //no heading
        return [];
    }
}
