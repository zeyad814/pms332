<?php 
require_once('../inc/header.php');

$cart_items = $_SESSION['cart'] ?? [];
$total_price = 0;

foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">سلة التسوق</h1>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-12">
                <?php if (empty($cart_items)): ?>
                    <div class="alert alert-info text-center">سلة التسوق فارغة حالياً.</div>
                    <div class="text-center"><a href="index.php" class="btn btn-primary">ابدأ التسوق</a></div>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">الصورة</th>
                                <th scope="col">المنتج</th>
                                <th scope="col">السعر للوحدة</th>
                                <th scope="col">الكمية</th>
                                <th scope="col">الإجمالي الفرعي</th>
                                <th scope="col">حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($cart_items as $item): ?>
                            <tr>
                                <th scope="row"><?= $i++ ?></th>
                                <td><img src="<?= htmlspecialchars($item['image'] ?? 'https://dummyimage.com/50x50/dee2e6/6c757d.jpg') ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <input type="number" value="<?= $item['quantity'] ?>" min="1" disabled style="width: 70px;">
                                </td>
                                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                <td>
                                    <a href="../actions/cart/delete_from_cart.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm">حذف</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="5" class="text-end">
                                    **المجموع الكلي**
                                </td>
                                <td colspan="2" class="text-start">
                                    <h3>$<?= number_format($total_price, 2) ?></h3>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" class="text-end">
                                    <a href="checkout.php" class="btn btn-primary">إتمام عملية الشراء (Checkout)</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require_once('../inc/footer.php'); ?>