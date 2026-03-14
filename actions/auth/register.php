<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/register.php');
    exit;
}

require_once('../../functions/file_system.php');
require_once('../../functions/validation.php');

$_SESSION['old_data'] = $_POST;
$errors = [];

$name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$password_confirmation = $_POST['password_confirmation'] ?? '';

// التحقق من الشروط المطلوبة (كما طلبت)
if (!is_required($name)) {
    $errors['name'] = 'الاسم مطلوب.';
} elseif (!is_valid_name($name)) {
    $errors['name'] = 'يجب أن يتكون الاسم من 3 أحرف على الأقل.';
}

if (!is_required($email)) {
    $errors['email'] = 'البريد الإلكتروني مطلوب.';
} elseif (!is_valid_email($email)) {
    $errors['email'] = 'صيغة البريد الإلكتروني غير صحيحة.';
} else {
    $users = read_json_file('users.json');
    if (find_item($users, 'email', $email)) {
        $errors['email'] = 'البريد الإلكتروني مُسجل بالفعل.';
    }
}

if (!is_required($password)) {
    $errors['password'] = 'كلمة المرور مطلوبة.';
} elseif (!is_strong_password($password)) {
    $errors['password'] = 'يجب أن تتكون كلمة المرور من حروف كبيرة، حروف صغيرة وأرقام.';
}

if (!is_required($password_confirmation)) {
    $errors['password_confirmation'] = 'تأكيد كلمة المرور مطلوب.';
} elseif (!passwords_match($password, $password_confirmation)) {
    $errors['password_confirmation'] = 'كلمتا المرور غير متطابقتين.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/register.php');
    exit;
}

// حفظ بيانات المستخدم
$new_user = [
    'id' => uniqid('user_'),
    'name' => $name,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'is_admin' => false 
];

$users[] = $new_user;

if (write_json_file('users.json', $users)) {
    $_SESSION['success'] = 'تم إنشاء حسابك بنجاح. يمكنك الآن تسجيل الدخول.';
    unset($_SESSION['old_data']);
    header('Location: ../../views/login.php');
    exit;
} else {
    $_SESSION['errors']['general'] = 'حدث خطأ في النظام.';
    header('Location: ../../views/register.php');
    exit;
}