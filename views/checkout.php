<?php 
require_once('../inc/header.php'); 
require_once('../functions/file_system.php');

// افتراض منطق تحميل السلة وحساب الإجمالي
$cart_items = $_SESSION['cart'] ?? [];
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

if (empty($cart_items)) {
    $_SESSION['error'] = 'لا يمكن إتمام عملية الشراء والسلة فارغة.';
    header('Location: cart.php');
    exit;
}

$old_data = $_SESSION['old_data_checkout'] ?? [];
$errors = $_SESSION['errors_checkout'] ?? [];
unset($_SESSION['errors_checkout'], $_SESSION['old_data_checkout']);

?>

<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">إتمام عملية الشراء</h1>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row">
            
            <div class="col-md-4 mb-4">
                <div class="border p-3">
                    <h5 class="mb-3">ملخص الطلب</h5>
                    <ul class="list-unstyled">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="border-bottom p-2 my-1 d-flex justify-content-between align-items-center"> 
                                <span class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($item['image'] ?? 'https://dummyimage.com/30x30/dee2e6/6c757d.jpg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 30px; height: 30px; object-fit: cover; margin-left: 10px;">
                                    <?= htmlspecialchars($item['name']) ?>
                                </span>
                                <span class="text-success bold"><?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>المجموع الكلي:</strong>
                        <strong class="text-danger">$<?= number_format($total_price, 2) ?></strong>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <h5 class="mb-3">بيانات العميل</h5>
                <form action="../actions/cart/checkout.php" method="POST" class="form border my-2 p-3">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= $old_data['name'] ?? ''; ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $old_data['email'] ?? ''; ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <input type="text" name="address" id="address" class="form-control" value="<?= $old_data['address'] ?? ''; ?>">
                        <?php if (isset($errors['address'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['address']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">الهاتف</label>
                        <input type="number" name="phone" id="phone" class="form-control" value="<?= $old_data['phone'] ?? ''; ?>">
                        <?php if (isset($errors['phone'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['phone']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"><?= $old_data['notes'] ?? ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success w-100">تأكيد الطلب ($<?= number_format($total_price, 2) ?>)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once('../inc/footer.php'); ?>