<?php
require_once 'vendor/autoload.php'; // Include Composer's autoload file
require_once 'Users.php'; // Include Users class
require_once 'StorageUnit.php'; // Include StorageUnit class
require_once 'Product.php'; // Include Product class

session_start();

echo "Welcome to the Warehouse Management System!\n";

$user = new Users('dummy', 0); // Dummy instance to access the method

function isLoggedIn() {
    return isset($_SESSION['username']);
}

while (true) {
    if (isLoggedIn()) {
        echo "\nSelect an option:\n";
        echo "1. Logout\n";
        echo "2. Display Storage Units\n";
        echo "3. Create Storage Unit\n";
        echo "4. Access Storage Unit\n";
        echo "5. Create Product\n";
        echo "6. Display Products in Storage\n";
        echo "7. Edit Product\n";
        echo "8. Delete Product\n";
        echo "9. View Product Change Log\n";
        echo "10. Exit\n";
    } else {
        echo "\nSelect an option:\n";
        echo "1. Register\n";
        echo "2. Login\n";
        echo "3. Exit\n";
    }

    $choice = readline("Enter your choice: ");

    if (isLoggedIn()) {
        switch ($choice) {
            case '1':
                $user->logout();
                break;
            case '2':
                $user->displayStorageUnits();
                break;
            case '3':
                $unitName = readline("Enter storage unit name: ");
                $productCount = (int)readline("Enter number of products: ");
                $user->createStorageUnit($unitName, $productCount);
                break;
            case '4':
                $accessCode = (int)readline("Enter access code: ");
                $user->accessStorageUnit($accessCode);
                break;
            case '5':
                $productName = readline("Enter product name: ");
                $quantity = (int)readline("Enter quantity: ");
                $storageUnitName = readline("Enter storage unit name: ");
                $user->createProduct($productName, $quantity, $storageUnitName);
                break;
            case '6':
                $storageUnitName = readline("Enter storage unit name: ");
                $user->displayProductsInStorageUnit($storageUnitName);
                break;
            case '7':
                $productId = (int)readline("Enter product ID to edit: ");
                $newProductName = readline("Enter new product name: ");
                $newQuantity = (int)readline("Enter new quantity: ");
                Product::editProduct($productId, $newProductName, $newQuantity);
                break;
            case '8':
                $productId = (int)readline("Enter product ID to delete: ");
                Product::deleteProduct($productId);
                break;
            case '9':
                $logEntries = json_decode(file_get_contents('productChangesLog.json'), true);
                echo "Product Change Log:\n";
                foreach ($logEntries as $entry) {
                    echo "Product ID: {$entry['id']}\n";
                    echo "Changes:\n";
                    foreach ($entry['changes'] as $change) {
                        echo "- $change\n";
                    }
                    echo "Timestamp: {$entry['timestamp']}\n";
                    echo "----------------------\n";
                }
                break;
            case '10':
                echo "Goodbye!\n";
                exit;
            default:
                echo "Invalid choice. Please try again.\n";
                break;
        }
    } else {
        switch ($choice) {
            case '1':
                $username = readline("Enter username: ");
                $password = readline("Enter password: ");
                $user->addUser($username, $password);
                break;
            case '2':
                $username = readline("Enter username: ");
                $password = readline("Enter password: ");
                $user->userLogin($username, $password);
                break;
            case '3':
                echo "Goodbye!\n";
                exit;
            default:
                echo "Invalid choice. Please try again.\n";
                break;
        }
    }
}
