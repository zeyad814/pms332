<?php 
require_once ('../inc/header.php'); 

$errors = $_SESSION['errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];

unset($_SESSION['errors'], $_SESSION['old_data']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4 text-center">إضافة منتج جديد</h2>
            <div class="card p-4">
                <form action="../actions/product/add.php" method="POST" enctype="multipart/form-data">
                    
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
                        <label for="image" class="form-label">صورة المنتج</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <?php if (isset($errors['image'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['image']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-3">إضافة المنتج</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once('../inc/footer.php'); ?>