<?php 
require_once ('../inc/header.php'); 
require_once ('../functions/file_system.php');

$products = read_json_file('products.json');

$success_message = $_SESSION['success'] ?? null;
$error_message = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">كل المنتجات</h1>
                    <p class="lead fw-normal text-white-50 mb-0">تصفح منتجاتنا المميزة</p>
                </div>
            </div>
        </header>
        
        <section class="py-5">
            <div class="container px-4 px-lg-5 mt-5">
                <?php if ($success_message): ?>
                    <div class="alert alert-success text-center"><?= $success_message ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger text-center"><?= $error_message ?></div>
                <?php endif; ?>

                <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                    
                    <?php if (empty($products)): ?>
                        <div class="col-12 text-center">
                            <p class="lead">لا توجد منتجات لعرضها حالياً.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($products as $product): ?>
                    <div class="col mb-5">
                        <div class="card h-100">
                            
                            <img class="card-img-top" src="<?= htmlspecialchars($product['image'] ?? 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 300px; object-fit: cover;" />
                            
                            <?php if (($product['discount'] ?? 0) > 0): ?>
                                <div class="badge bg-danger text-white position-absolute" style="top: 0.5rem; right: 0.5rem">تخفيض!</div>
                            <?php endif; ?>

                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder"><?= htmlspecialchars($product['name']) ?></h5>
                                    <span class="text-muted small">المخزون: <?= $product['stock'] ?></span>
                                    
                                    <div class="mt-2">
                                        <?php 
                                        $original_price = $product['original_price'] ?? 0;
                                        $selling_price = $product['selling_price'] ?? $original_price;
                                        ?>

                                        <?php if (($product['discount'] ?? 0) > 0): ?>
                                            <span class="text-muted text-decoration-line-through me-2">$<?= number_format($original_price, 2) ?></span>
                                            <span class="fw-bold text-danger">$<?= number_format($selling_price, 2) ?></span>
                                        <?php else: ?>
                                            $<?= number_format($selling_price, 2) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center">
                                    <form action="../actions/cart/add_to_cart.php" method="POST" class="d-inline mb-2">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-outline-dark w-100" <?= ($product['stock'] <= 0) ? 'disabled' : '' ?>>
                                            <i class="bi-cart-fill me-1"></i> أضف للسلة
                                        </button>
                                    </form>
                                    
                                
                                    <a class="btn btn-sm btn-outline-info w-100 mt-2" href="edit_product.php?id=<?= $product['id'] ?>">
                                        <i class="bi-pencil-fill"></i> تعديل
                                    </a>
                                    <a class="btn btn-sm btn-outline-danger w-100 mt-2" href="../actions/product/delete.php?id=<?= $product['id'] ?>" onclick="return confirm('هل أنت متأكد من حذف المنتج؟')">
                                        <i class="bi-trash-fill"></i> حذف
                                    </a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </section>
<?php require_once ('../inc/footer.php'); ?>