<?php

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class Product
{
    private static $filePath = 'productData.json';

    private $id;
    private $name;
    private $quantity;
    private $createdAt;
    private $storageUnitName;

    public function __construct($name, $quantity, $storageUnitName)
    {
        $this->id = $this->generateId();
        $this->name = $name;
        $this->quantity = $quantity;
        $this->createdAt = Carbon::now()->toDateTimeString();
        $this->storageUnitName = $storageUnitName;
    }

    private function generateId()
    {
        return Uuid::uuid4()->toString();
    }

    public function saveProduct()
    {
        $products = self::getAllProducts();
        $products[] = [
            'id' => $this->id,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'createdAt' => $this->createdAt,
            'storageUnitName' => $this->storageUnitName
        ];
        file_put_contents(self::$filePath, json_encode($products, JSON_PRETTY_PRINT));
    }

    public static function getAllProducts()
    {
        if (!file_exists(self::$filePath)) {
            return [];
        }
        $data = file_get_contents(self::$filePath);
        return json_decode($data, true);
    }

    public static function editProduct($id, $newName, $newQuantity)
    {
        $products = self::getAllProducts();
        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $changes = [];
                if ($product['name'] != $newName) {
                    $changes[] = "Name changed from {$product['name']} to $newName";
                    $product['name'] = $newName;
                }
                if ($product['quantity'] != $newQuantity) {
                    $changes[] = "Quantity changed from {$product['quantity']} to $newQuantity";
                    $product['quantity'] = $newQuantity;
                }
                if ($changes) {
                    self::logChanges($id, $changes);
                }
                break;
            }
        }
        file_put_contents(self::$filePath, json_encode($products, JSON_PRETTY_PRINT));
        echo "Product edited successfully.\n";
    }

    public static function deleteProduct($id)
    {
        $products = self::getAllProducts();
        foreach ($products as $index => $product) {
            if ($product['id'] == $id) {
                unset($products[$index]);
                file_put_contents(self::$filePath, json_encode(array_values($products), JSON_PRETTY_PRINT));
                echo "Product deleted successfully.\n";
                return;
            }
        }
        echo "Product not found.\n";
    }

    private static function logChanges($id, $changes)
    {
        $logFilePath = 'productChangesLog.json';
        $logEntries = file_exists($logFilePath) ? json_decode(file_get_contents($logFilePath), true) : [];
        $logEntries[] = [
            'id' => $id,
            'changes' => $changes,
            'timestamp' => Carbon::now()->toDateTimeString()
        ];
        file_put_contents($logFilePath, json_encode($logEntries, JSON_PRETTY_PRINT));
    }
}
