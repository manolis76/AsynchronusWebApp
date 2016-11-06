<?php

function limparDados($data) {
// If $data is not an array, run strip_tags()
    if (!is_array($data)) {
// Remove all tags except <a> tags
        return strip_tags($data, "<a>");
    }
// If $data is an array, process each element
    else {
// Call sanitizeData recursively for each array element
        return array_map('limparDados', $data);
    }
}

?>
