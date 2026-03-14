<?php
session_start();
require_once('../../functions/file_system.php');
require_once('../../functions/validation.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/index.php');
    exit;
}

$id = $_POST['id'] ?? '';
if (empty($id)) {
    $_SESSION['error'] = 'معرف المنتج غير موجود.';
    header('Location: ../../views/index.php');
    exit;
}

$_SESSION['old_data_' . $id] = $_POST;
$errors = [];

// استخراج البيانات الأصلية والتحقق منها
$products = read_json_file('products.json');
$product_to_edit = find_item($products, 'id', $id);

if (!$product_to_edit) {
    $_SESSION['error'] = 'المنتج المطلوب غير موجود للتعديل.';
    header('Location: ../../views/index.php');
    exit;
}

// تصفية المدخلات
$name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
$description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
$original_price = filter_var($_POST['original_price'] ?? null, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$discount = filter_var($_POST['discount'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
$stock = filter_var($_POST['stock'] ?? null, FILTER_SANITIZE_NUMBER_INT);

// 1. التحقق من الحقول النصية والرقمية (نفس منطق add.php)
if (!is_required($name)) {
    $errors['name'] = 'اسم المنتج مطلوب.';
}
if(!is_required($description)){
    $errors['description'] = 'الوصف مطلوب.';
}
if ($original_price === null || $original_price === '') {
    $errors['original_price'] = 'السعر قبل التخفيض مطلوب.';
} elseif ((float)$original_price <= 0) {
    $errors['original_price'] = 'يجب أن يكون السعر قبل التخفيض أكبر من صفر.';
}
$discount = (int)$discount;
if ($discount < 0 || $discount > 100) {
    $errors['discount'] = 'قيمة التخفيض يجب أن تكون بين 0% و 100%.';
}
if ($stock === null || $stock === '') {
    $errors['stock'] = 'كمية المخزون مطلوبة.';
} elseif ((int)$stock < 0) {
    $errors['stock'] = 'يجب أن تكون كمية المخزون صفر أو أكبر.';
}

// 2. معالجة ملف الصورة
$image_file = $_FILES['image'] ?? null;
$image_path = $product_to_edit['image'] ?? ''; // المسار القديم افتراضياً
$allowed_image_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// التحقق مما إذا تم رفع ملف جديد
if ($image_file && $image_file['error'] !== UPLOAD_ERR_NO_FILE) {
    $file_validation_result = validate_file($image_file, $allowed_image_types, 3145728); 
    
    if ($file_validation_result !== true) {
        $errors['image'] = $file_validation_result;
    } else {
        // إذا كان الملف صحيحاً، نقوم برفعه
        $new_image_path = upload_file($image_file);

        if (!$new_image_path) {
            $errors['image'] = 'فشل في رفع ملف الصورة الجديد.';
        } else {
            // حذف الصورة القديمة وتحديث المسار
            delete_file($product_to_edit['image'] ?? '');
            $image_path = $new_image_path; 
        }
    }
} elseif (empty($image_path)) {
    // إذا لم يرفع ملف جديد ولم يكن هناك ملف قديم، نعتبره خطأ
    $errors['image'] = 'يجب أن يحتوي المنتج على صورة.';
}


if (!empty($errors)) {
    $_SESSION['errors_' . $id] = $errors;
    header('Location: ../../views/edit_product.php?id=' . $id);
    exit;
}

// 3. حساب سعر البيع
$calculated_selling_price = (float)$original_price * (1 - $discount / 100);
$selling_price = round($calculated_selling_price, 2);

// 4. تحديث بيانات المنتج
$updated_product_data = [
    'name' => $name,
    'description' => $description,
    'original_price' => (float)$original_price,
    'discount' => $discount,
    'selling_price' => $selling_price,
    'stock' => (int)$stock,
    'image' => $image_path 
];

$updated_products = update_item($products, 'id', $id, $updated_product_data);

if (write_json_file('products.json', $updated_products)) {
    $_SESSION['success'] = 'تم تعديل المنتج بنجاح.';
    unset($_SESSION['old_data_' . $id]);
    header('Location: ../../views/index.php');
    exit;
} else {
    $_SESSION['error'] = 'حدث خطأ أثناء حفظ التعديلات.';
    // إذا فشل حفظ التعديلات، يجب حذف الصورة الجديدة إذا تم رفعها للتو.
    if (isset($new_image_path)) {
        delete_file($new_image_path);
    }
    header('Location: ../../views/edit_product.php?id=' . $id);
    exit;
}