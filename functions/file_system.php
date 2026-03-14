<?php

// المسار الأساسي لمجلد البيانات
define('DATA_PATH', __DIR__ . '/../data/');

/**
 * تقرأ محتوى ملف JSON وتُعيده كمصفوفة PHP.
 */
function read_json_file(string $filename): array
{
    $filepath = DATA_PATH . $filename;
    if (!file_exists($filepath) || !is_readable($filepath)) {
        return [];
    }
    $content = file_get_contents($filepath);
    return json_decode($content, true) ?? [];
}

/**
 * تكتب مصفوفة PHP إلى ملف JSON.
 */
function write_json_file(string $filename, array $data): bool
{
    $filepath = DATA_PATH . $filename;
    $json_content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0777, true);
    }

    return file_put_contents($filepath, $json_content) !== false;
}

/**
 * تبحث عن عنصر في مصفوفة.
 */
function find_item(array $list, string $key, $value): ?array
{
    foreach ($list as $item) {
        if (isset($item[$key]) && $item[$key] === $value) {
            return $item;
        }
    }
    return null;
}

/**
 * تحديث عنصر في مصفوفة.
 */
function update_item(array $list, string $key, $value, array $new_data): array
{
    foreach ($list as $index => $item) {
        if (isset($item[$key]) && $item[$key] === $value) {
            $list[$index] = array_merge($item, $new_data);
            break;
        }
    }
    return $list;
}

/**
 * حذف عنصر من مصفوفة.
 */
function delete_item(array $list, string $key, $value): array
{
    foreach ($list as $index => $item) {
        if (isset($item[$key]) && $item[$key] === $value) {
            unset($list[$index]);
            break;
        }
    }
    return array_values($list);
}
function upload_file(array $file_array): string|bool
{
    $upload_dir = '../../uploads/products/';
    
    if (!is_dir($upload_dir) && !@mkdir($upload_dir, 0777, true)) {
        return false;
    }

    $ext = pathinfo($file_array['name'], PATHINFO_EXTENSION);
    
    $new_file_name = uniqid('img_') . '.' . strtolower($ext);

    $destination = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_array['tmp_name'], $destination)) {
        // نرجع المسار النسبي الذي سيستخدم في ملفات الـ views
        return '../uploads/products/' . $new_file_name; 
    }
    // في حالة الفشل، نرجع false
    return false;
}

/**
 * يقوم بحذف ملف من نظام الملفات.
 */
function delete_file(string $relative_path): bool
{
    if (empty($relative_path)) {
        return true; 
    }
    // المسار المحفوظ في JSON هو: ../uploads/...
    // المسار الحقيقي من ملف actions هو: ../../uploads/...
    $file_to_delete = '../../' . ltrim($relative_path, '../');

    if (file_exists($file_to_delete) && is_writable($file_to_delete)) {
        return @unlink($file_to_delete);
    }
    
    return true; 
}