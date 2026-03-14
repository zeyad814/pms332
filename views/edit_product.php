<?php 
require_once ('../inc/header.php'); 
require_once ('../functions/file_system.php');

$product_id = $_GET['id'] ?? null;
$products = read_json_file('products.json');
$product_to_edit = find_item($products, 'id', $product_id);

if (!$product_to_edit) {
    $_SESSION['error'] = 'المنتج المطلوب غير موجود.';
    header('Location: index.php');
    exit;
}

$old_data = $_SESSION['old_data_' . $product_id] ?? $product_to_edit;
$errors = $_SESSION['errors_' . $product_id] ?? [];

unset($_SESSION['errors_' . $product_id], $_SESSION['old_data_' . $product_id]);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4 text-center">تعديل المنتج: <?= htmlspecialchars($product_to_edit['name']) ?></h2>
            <div class="card p-4">
                <form action="../actions/product/edit.php" method="POST" enctype="multipart/form-data">
                    
                    <input type="hidden" name="id" value="<?= $product_id ?>">

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">اسم المنتج</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $old_data['name'] ?? ''; ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['name']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= $old_data['description'] ?? ''; ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                                    <div class="text-danger small mt-1"><?= $errors['description']; ?></div>
                        <?php endif; ?> 
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="original_price" class="form-label">السعر قبل التخفيض ($)</label>
                            <input type="number" step="0.01" class="form-control" id="original_price" name="original_price" value="<?= $old_data['original_price'] ?? ''; ?>">
                            <?php if (isset($errors['original_price'])): ?>
                                <div class="text-danger small mt-1"><?= $errors['original_price']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="discount" class="form-label">التخفيض (%)</label>
                            <input type="number" step="1" min="0" max="100" class="form-control" id="discount" name="discount" value="<?= $old_data['discount'] ?? 0; ?>">
                            <?php if (isset($errors['discount'])): ?>
                                <div class="text-danger small mt-1"><?= $errors['discount']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label">الكمية/المخزون</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="<?= $old_data['stock'] ?? ''; ?>">
                            <?php if (isset($errors['stock'])): ?>
                                <div class="text-danger small mt-1"><?= $errors['stock']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">صورة المنتج (اتركها فارغة لعدم التعديل)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        
                        <?php if (!empty($product_to_edit['image'])): ?>
                            <small class="text-muted d-block mt-2">الصورة الحالية:</small>
                            <img src="<?= htmlspecialchars($product_to_edit['image']) ?>" alt="صورة المنتج الحالي" style="width: 100px; height: 100px; object-fit: cover;">
                        <?php endif; ?>

                        <?php if (isset($errors['image'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['image']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mt-3">حفظ التعديلات</button>
                    <a href="index.php" class="btn btn-secondary w-100 mt-2">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once('../inc/footer.php'); ?>