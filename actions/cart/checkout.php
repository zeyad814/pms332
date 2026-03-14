<?php
session_start();
require_once('../../functions/file_system.php');
require_once('../../functions/validation.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error'] = 'سلة التسوق فارغة!';
    header('Location: ../../views/index.php');
    exit;
}

$_SESSION['old_checkout_data'] = $_POST;
$errors = [];

$name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);
$phone = filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_STRING);
$notes = filter_var($_POST['notes'] ?? '', FILTER_SANITIZE_STRING);

// التحقق من البيانات المطلوبة للعميل
if (!is_required($name)) $errors['name'] = 'الاسم مطلوب.';
if (!is_required($email) || !is_valid_email($email)) $errors['email'] = 'بريد إلكتروني صحيح مطلوب.';
if (!is_required($address)) $errors['address'] = 'العنوان مطلوب.';
if (!is_required($phone)) $errors['phone'] = 'رقم الهاتف مطلوب.';

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/checkout.php');
    exit;
}

// حساب المجموع الكلي
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// إنشاء الطلب
$order_data = [
    'id' => uniqid('order_'),
    'user_id' => $_SESSION['user_id'] ?? 'Guest',
    'customer_info' => [
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'phone' => $phone,
        'notes' => $notes,
    ],
    'items' => $_SESSION['cart'],
    'total' => $total_price,
    'date' => date('Y-m-d H:i:s'),
];

// تخزين الطلب
$orders = read_json_file('orders.json');
$orders[] = $order_data;

if (write_json_file('orders.json', $orders)) {
    unset($_SESSION['cart']); // تفريغ السلة
    unset($_SESSION['old_checkout_data']);
    
    $_SESSION['success'] = 'تم إنشاء طلبك بنجاح! رقم الطلب هو: ' . $order_data['id'];
    header('Location: ../../views/orders.php');
    exit;
} else {
    $_SESSION['error'] = 'حدث خطأ في حفظ الطلب.';
    header('Location: ../../views/checkout.php');
    exit;
}