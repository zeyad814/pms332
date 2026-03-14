<?php 
require_once ('../inc/header.php'); 
require_once ('../functions/file_system.php');

if (!is_logged_in()) {
    $_SESSION['error'] = 'يجب عليك تسجيل الدخول لعرض الطلبات.';
    header('Location: login.php');
    exit;
}

$all_orders = read_json_file('orders.json');

// تصفية الطلبات: 
$user_id = $_SESSION['user_id']; 


    $user_orders = array_filter($all_orders, function($order) use ($user_id) {
        return ($order['user_id'] ?? 'Guest') === $user_id;
    });


// عكس ترتيب المصفوفة لعرض أحدث الطلبات أولاً
$user_orders = array_reverse($user_orders);

?>

<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">قائمة الطلبات</h1>
            <p class="lead fw-normal text-white-50 mb-0">طلباتك السابقة</p>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <?php if (empty($user_orders)): ?>
            <div class="alert alert-info text-center">
                لا توجد طلبات لعرضها حالياً.
            </div>
            <div class="text-center"><a href="index.php" class="btn btn-primary">ابدأ التسوق</a></div>
        <?php else: ?>
            
            <?php foreach ($user_orders as $order): ?>
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">الطلب رقم: <span class="text-primary"><?= htmlspecialchars($order['id']) ?></span> 
                    <span class="float-start text-muted small">التاريخ: <?= htmlspecialchars(date('Y-m-d H:i', strtotime($order['date']))) ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6>**بيانات العميل**</h6>
                            <ul class="list-unstyled small">
                                <li>**الاسم:** <?= htmlspecialchars($order['customer_info']['name'] ?? 'N/A') ?></li>
                                <li>**الإيميل:** <?= htmlspecialchars($order['customer_info']['email'] ?? 'N/A') ?></li>
                                <li>**العنوان:** <?= htmlspecialchars($order['customer_info']['address'] ?? 'N/A') ?></li>
                                <li>**الهاتف:** <?= htmlspecialchars($order['customer_info']['phone'] ?? 'N/A') ?></li>
                            </ul>
                            <p class="small">**ملاحظات:** <?= htmlspecialchars($order['customer_info']['notes'] ?? 'لا يوجد') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>**تفاصيل المنتجات**</h6>
                            <table class="table table-sm table-striped small">
                                <thead>
                                    <tr>
                                        <th>الصورة</th>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><img src="<?= htmlspecialchars($item['image'] ?? 'https://dummyimage.com/30x30/dee2e6/6c757d.jpg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 30px; height: 30px; object-fit: cover;"></td>
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="alert alert-success text-center mt-3">
                                **الإجمالي الكلي للطلب:** <h3>$<?= number_format($order['total'] ?? 0, 2) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</section>
<?php require_once ('../inc/footer.php'); ?>