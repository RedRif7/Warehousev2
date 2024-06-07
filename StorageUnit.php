<?php

class StorageUnit
{
    private static $filePath = 'storageUnitData.json';

    private $name;
    private $owner;
    private $accessCode;
    private $productCount;

    public function __construct($name, $owner, $accessCode, $productCount)
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->accessCode = $accessCode;
        $this->productCount = $productCount;
    }

    public function saveStorageUnit()
    {
        $storageUnits = self::getAllStorageUnits();
        $storageUnits[] = [
            'name' => $this->name,
            'owner' => $this->owner,
            'accessCode' => $this->accessCode,
            'productCount' => $this->productCount
        ];
        file_put_contents(self::$filePath, json_encode($storageUnits, JSON_PRETTY_PRINT));
    }

    public static function getAllStorageUnits()
    {
        if (!file_exists(self::$filePath)) {
            return [];
        }
        $data = file_get_contents(self::$filePath);
        return json_decode($data, true);
    }
}
