<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/login.php');
    exit;
}

require_once('../../functions/file_system.php');
require_once('../../functions/validation.php');

$errors = [];
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!is_required($email) || !is_required($password)) {
    $errors['general'] = 'يجب إدخال البريد الإلكتروني وكلمة المرور.';
}

if (empty($errors)) {
    $users = read_json_file('users.json');
    $user = find_item($users, 'email', $email);

    if ($user && password_verify($password, $user['password'])) {
        
        // تسجيل الدخول بنجاح
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'] ?? false; 

        $_SESSION['success'] = 'مرحباً بك، ' . $user['name'] . '!';
        header('Location: ../../views/index.php');
        exit;
        
    } else {
        $errors['general'] = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
    }
}

$_SESSION['errors'] = $errors;
$_SESSION['old_data'] = ['email' => $email];
header('Location: ../../views/login.php');
exit;