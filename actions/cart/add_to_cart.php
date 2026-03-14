<?php
session_start();
require_once('../../functions/file_system.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    header('Location: ../../views/index.php');
    exit;
}

$product_id = $_POST['product_id'];
$quantity = (int)($_POST['quantity'] ?? 1);

if ($quantity <= 0) {
    $_SESSION['error'] = 'الكمية غير صحيحة.';
    header('Location: ../../views/index.php');
    exit;
}

$products = read_json_file('products.json');
$product_info = find_item($products, 'id', $product_id);

if (!$product_info) {
    $_SESSION['error'] = 'المنتج غير موجود.';
    header('Location: ../../views/index.php');
    exit;
}

// نستخدم سعر البيع (selling_price) والصورة المخزنة
$price_to_use = $product_info['selling_price'] ?? $product_info['original_price'] ?? 0;
// استخدام مسار الصورة المرفوع
$image_url = $product_info['image'] ?? 'https://dummyimage.com/50x50/dee2e6/6c757d.jpg';


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$item_found = false;
foreach ($_SESSION['cart'] as $index => $item) {
    if ($item['id'] === $product_id) {
        // التحقق من المخزون
        if (($item['quantity'] + $quantity) > $product_info['stock']) {
             $_SESSION['error'] = 'الكمية المطلوبة غير متوفرة في المخزون (' . $product_info['stock'] . ').';
             header('Location: ../../views/index.php');
             exit;
        }
        
        $_SESSION['cart'][$index]['quantity'] += $quantity;
        $item_found = true;
        break;
    }
}

if (!$item_found) {
    if ($quantity > $product_info['stock']) {
         $_SESSION['error'] = 'الكمية المطلوبة غير متوفرة في المخزون (' . $product_info['stock'] . ').';
         header('Location: ../../views/index.php');
         exit;
    }
    
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_info['name'],
        'price' => $price_to_use, // سعر البيع
        'quantity' => $quantity,
        'image' => $image_url, // حفظ مسار الصورة
    ];
}

$_SESSION['success'] = 'تمت إضافة المنتج "' . $product_info['name'] . '" إلى السلة.';
header('Location: ../../views/cart.php');
exit;