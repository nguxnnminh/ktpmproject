<?php
function loadData($filename) {
    if (!file_exists($filename)) {
        return []; // File không tồn tại → trả mảng rỗng
    }

    $json = file_get_contents($filename);

    // Nếu file rỗng → cũng trả mảng rỗng
    if (trim($json) === '') {
        return [];
    }

    $data = json_decode($json, true);

    // Nếu lỗi khi decode hoặc kết quả không phải mảng → trả mảng rỗng
    return is_array($data) ? $data : [];
}

function saveData($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}
?>