<?php
session_start();

// حذف كل بيانات السيشن
session_unset(); 
session_destroy();

// بدء سيشن جديدة لرسالة النجاح
session_start();
$_SESSION['success'] = 'تم تسجيل خروجك بنجاح.';

// إعادة التوجيه
header('Location: ../../views/index.php');
exit;
