<?php

/**
 * تتحقق من أن القيمة ليست فارغة.
 */
function is_required($value): bool
{
    return !empty($value);
}

/**
 * تتحقق من أن طول الاسم لا يقل عن 3 أحرف.
 */
function is_valid_name(string $name): bool
{
    return strlen($name) >= 3;
}

/**
 * تتحقق من أن الصيغة هي صيغة بريد إلكتروني صحيحة.
 */
function is_valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * تتحقق من قوة كلمة المرور: حروف كبيرة وصغيرة وأرقام.
 */
function is_strong_password(string $password): bool
{
    return preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

/**
 * تتحقق من تطابق كلمتي المرور.
 */
function passwords_match(string $password, string $confirmation): bool
{
    return $password === $confirmation;
}
function validate_file(array $file_array, array $allowed_types = [], int $max_size = 3145728): string|bool
{
    if ($file_array['error'] === UPLOAD_ERR_NO_FILE) {
        return 'يجب رفع ملف.';
    }

    if ($file_array['error'] !== UPLOAD_ERR_OK) {
        return 'حدث خطأ غير متوقع أثناء رفع الملف.';
    }

    if (!in_array($file_array['type'], $allowed_types)) {
        $readable_types = array_map(fn($t) => str_replace('image/', '', $t), $allowed_types);
        return 'صيغة الملف غير مسموح بها. الصيغ المسموحة: ' . implode(', ', $readable_types) . '.';
    }

    if ($file_array['size'] > $max_size) {
        // 3MB is 3145728 bytes
        return 'حجم الملف كبير جداً. الحد الأقصى: ' . ($max_size / 1048576) . ' ميجابايت.';
    }
    
    if (!is_uploaded_file($file_array['tmp_name'])) {
        return 'الملف المرفوع غير صالح.';
    }

    return true; 
}