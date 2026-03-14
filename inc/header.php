<?php 
// تأكد من أن الجلسة بدأت
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// دالة مساعدة للتحقق من تسجيل الدخول
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// حساب عدد المنتجات في السلة
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>نظام إدارة المنتجات - EraaSoft PMS</title>
        <link rel="icon" type="image/x-icon" href="../assets/favicon.ico" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container px-4 px-lg-5">
                <a class="navbar-brand" href="../views/index.php">EraaSoft PMS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item"><a class="nav-link active" aria-current="page" href="../views/index.php">عرض المنتجات</a></li>
                        <li class="nav-item"><a class="nav-link" href="../views/add_product.php">إضافة منتج</a></li>
                        
                        <?php if (is_logged_in()): ?>
                            <li class="nav-item"><span class="nav-link text-success">مرحباً، <?= $_SESSION['user_name'] ?? 'مستخدم' ?></span></li>
                            <li class="nav-item"><a class="nav-link" href="../views/orders.php">الطلبات</a></li>
                            <li class="nav-item"><a class="nav-link text-danger" href="../actions/auth/logout.php">تسجيل الخروج</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="../views/login.php">تسجيل الدخول</a></li>
                            <li class="nav-item"><a class="nav-link" href="../views/register.php">إنشاء حساب</a></li>
                        <?php endif; ?>
                    </ul>
                    
                    <form class="d-flex" action="../views/cart.php" method="GET">
                        <button class="btn btn-outline-dark" type="submit">
                            <i class="bi-cart-fill me-1"></i>
                            السلة
                            <span class="badge bg-dark text-white ms-1 rounded-pill"><?= $cart_count ?></span>
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        <main>