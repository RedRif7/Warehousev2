<?php
require_once 'StorageUnit.php'; // Ensure StorageUnit class is included
require_once 'Product.php'; // Ensure Product class is included

class Users
{
    private string $name;
    private int $accessCode;

    public function __construct(string $name, int $accessCode)
    {
        $this->name = $name;
        $this->accessCode = $accessCode;
    }

    public function userLogin($username, $password)
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user['name'] == $username && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                echo "Login successful. Welcome, $username!\n";
                return;
            }
        }
        echo "Invalid username or password.\n";
    }

    public function addUser($username, $password)
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user['name'] == $username) {
                echo "Username already taken. Please choose another.\n";
                return;
            }
        }
        $users[] = [
            'name' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        file_put_contents('userData.json', json_encode($users, JSON_PRETTY_PRINT));
        echo "User registered successfully.\n";
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        echo "Logout successful.\n";
    }

    private function getAllUsers()
    {
        $filePath = 'userData.json';
        if (!file_exists($filePath)) {
            return [];
        }
        $data = file_get_contents($filePath);
        return json_decode($data, true);
    }

    public function createStorageUnit($unitName, $productCount)
    {
        $storageUnits = StorageUnit::getAllStorageUnits();
        foreach ($storageUnits as $unit) {
            if ($unit['name'] == $unitName) {
                echo "Storage unit name already taken. Please choose another.\n";
                return;
            }
        }
        $accessCode = rand(1000, 9999);
        $storageUnit = new StorageUnit($unitName, $_SESSION['username'], $accessCode, $productCount);
        $storageUnit->saveStorageUnit();
        echo "Storage unit '$unitName' created successfully with access code: $accessCode\n";
    }

    public function accessStorageUnit($accessCode)
    {
        $storageUnits = StorageUnit::getAllStorageUnits();
        foreach ($storageUnits as $unit) {
            if ($unit['accessCode'] == $accessCode && $unit['owner'] == $_SESSION['username']) {
                echo "Access granted to storage unit: {$unit['name']}\n";
                return;
            }
        }
        echo "Access denied or incorrect access code.\n";
    }

    public function displayStorageUnits()
    {
        $storageUnits = StorageUnit::getAllStorageUnits();
        $userUnits = array_filter($storageUnits, function ($unit) {
            return $unit['owner'] == $_SESSION['username'];
        });

        if (empty($userUnits)) {
            echo "No storage units found for user {$_SESSION['username']}.\n";
            return;
        }

        echo "Storage units for user {$_SESSION['username']}:\n";
        foreach ($userUnits as $unit) {
            echo "Name: {$unit['name']}, Access Code: {$unit['accessCode']}, Product Count: {$unit['productCount']}\n";
        }
    }

    public function createProduct($productName, $quantity, $storageUnitName)
    {
        $storageUnits = StorageUnit::getAllStorageUnits();
        $unitFound = false;

        foreach ($storageUnits as $unit) {
            if ($unit['name'] == $storageUnitName && $unit['owner'] == $_SESSION['username']) {
                $unitFound = true;
                break;
            }
        }

        if (!$unitFound) {
            echo "No access to storage unit '$storageUnitName' or it doesn't exist.\n";
            return;
        }

        $product = new Product($productName, $quantity, $storageUnitName);
        $product->saveProduct();
        echo "Product '$productName' created successfully.\n";
    }

    public function displayProductsInStorageUnit($storageUnitName)
    {
        $products = Product::getAllProducts();
        $storageUnits = StorageUnit::getAllStorageUnits();
        $unitFound = false;

        foreach ($storageUnits as $unit) {
            if ($unit['name'] == $storageUnitName && $unit['owner'] == $_SESSION['username']) {
                $unitFound = true;
                break;
            }
        }

        if (!$unitFound) {
            echo "No access to storage unit '$storageUnitName' or it doesn't exist.\n";
            return;
        }

        $unitProducts = array_filter($products, function ($product) use ($storageUnitName) {
            return $product['storageUnitName'] == $storageUnitName;
        });

        if (empty($unitProducts)) {
            echo "No products found in storage unit '$storageUnitName'.\n";
            return;
        }

        echo "Products in storage unit '$storageUnitName':\n";
        foreach ($unitProducts as $product) {
            echo "ID: {$product['id']}, Name: {$product['name']}, Quantity: {$product['quantity']}, Created At: {$product['createdAt']}\n";
        }
    }
}

// TODO - Izveidot USER registration, kur var izveidot jaunu useri, tikai ar vārdu, varbūt ko citu, un savu paroli. - DONE
// TODO - Dod iespēju izvēlēties useri no saraksta, izvēloties pieprasa paroli. - DONE
// TODO - Kad ielogojies, ir iespēja uztaisīt sev noliktavu ar nosaukumu, un iespējamo produktu daudzumu, vēl katrau noliktavai tiek izveidota parole, ar kuru piekļūt tai
// TODO - Izveidot izvēlni, kurai noliktavai piekļūt, tātad ar paroli, un tā vēl salīdzina 'ownerus', ja sakrīt, parāda noliktavas saturu un ir iespēja to mainīt
// TODO - produkta izveide, simple - id, name, units, when created, viss, izveidojot produktu, to vienkāršī var decodot uz json
// TODO - Varbūt noliktavas produktiem un useriem taisīt atsevišķus josnus, izdomā pats