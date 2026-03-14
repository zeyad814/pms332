<?php 
require_once('../inc/header.php'); 

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];

unset($_SESSION['errors'], $_SESSION['old_data']);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4 text-center">تسجيل الدخول</h2>
            <div class="card p-4">
                <form action="../actions/auth/login.php" method="POST">
                    
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger"><?= $errors['general']; ?></div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $old_data['email'] ?? ''; ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <?php if (isset($errors['password'])): ?>
                            <div class="text-danger small mt-1"><?= $errors['password']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">دخول</button>
                </form>
                <p class="text-center mt-3">ليس لديك حساب؟ <a href="register.php">إنشاء حساب</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once('../inc/footer.php'); ?>