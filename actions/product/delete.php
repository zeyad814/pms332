<?php
session_start();
require_once('../../functions/file_system.php');

if (!isset($_GET['id'])) {
    header('Location: ../../views/index.php');
    exit;
}

$id = $_GET['id'];

$products = read_json_file('products.json');
$updated_products = delete_item($products, 'id', $id);

if (write_json_file('products.json', $updated_products)) {
    $_SESSION['success'] = 'تم حذف المنتج بنجاح.';
} else {
    $_SESSION['error'] = 'حدث خطأ أثناء حذف المنتج.';
}

header('Location: ../../views/index.php');
exit;