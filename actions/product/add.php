<?php
session_start();
require_once('../../functions/file_system.php');
require_once('../../functions/validation.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/add_product.php');
    exit;
}

$_SESSION['old_data'] = $_POST;
$errors = [];

// // تصفية المدخلات
// $name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
// $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
// $original_price = filter_var($_POST['original_price'] ?? null, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
// $discount = filter_var($_POST['discount'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
// $stock = filter_var($_POST['stock'] ?? null, FILTER_SANITIZE_NUMBER_INT);

// // 1. التحقق من اسم المنتج
// if (!is_required($name)) {
//     $errors['name'] = 'اسم المنتج مطلوب.';
// }
// if(!is_required($description)){
//     $errors['description'] = 'الوصف مطلوب.';
// }
// // 2. التحقق من السعر قبل التخفيض
// if ($original_price === null || $original_price === '') {
//     $errors['original_price'] = 'السعر قبل التخفيض مطلوب.';
// } elseif ((float)$original_price <= 0) {
//     $errors['original_price'] = 'يجب أن يكون السعر قبل التخفيض أكبر من صفر.';
// }

// // 3. التحقق من التخفيض
// $discount = (int)$discount;
// if ($discount < 0 || $discount > 100) {
//     $errors['discount'] = 'قيمة التخفيض يجب أن تكون بين 0% و 100%.';
// }

// // 4. التحقق من المخزون
// if ($stock === null || $stock === '') {
//     $errors['stock'] = 'كمية المخزون مطلوبة.';
// } elseif ((int)$stock < 0) {
//     $errors['stock'] = 'يجب أن تكون كمية المخزون صفر أو أكبر.';
// }

// 5. التحقق من ملف الصورة
$image_file = $_FILES['image'] ?? null;

$allowed_image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

if ($image_file) {
    $file_validation_result = validate_file($image_file, $allowed_image_types, 3145728); // 3MB max
    if ($file_validation_result !== true) {
        $errors['image'] = $file_validation_result;
    }
} else {
    $errors['image'] = 'يجب رفع ملف صورة.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/add_product.php');
    exit;
}

// 6. رفع الملف وحفظ مساره
$image_path = upload_file($image_file);

if (!$image_path) {
    $_SESSION['error'] = 'فشل في رفع ملف الصورة. تأكد من صلاحية مجلد الرفع (uploads/products).';
    header('Location: ../../views/add_product.php');
    exit;
}

// 7. حساب سعر البيع (السعر بعد التخفيض)
$calculated_selling_price = (float)$original_price * (1 - $discount / 100);
$selling_price = round($calculated_selling_price, 2); 

// 8. إنشاء المنتج الجديد
$new_product = [
    'id' => uniqid('prod_'),
    'name' => $name,
    'description' => $description,
    'original_price' => (float)$original_price, 
    'discount' => $discount,                     
    'selling_price' => $selling_price,           
    'stock' => (int)$stock,
    'image' => $image_path 
];

$products = read_json_file('products.json');
$products[] = $new_product;

if (write_json_file('products.json', $products)) {
    $_SESSION['success'] = 'تمت إضافة المنتج بنجاح.';
    unset($_SESSION['old_data']);
    header('Location: ../../views/index.php');
    exit;
} else {
    $_SESSION['error'] = 'حدث خطأ أثناء حفظ بيانات المنتج.';
    delete_file($image_path); // حذف الملف إذا فشل الحفظ
    header('Location: ../../views/add_product.php');
    exit;
}