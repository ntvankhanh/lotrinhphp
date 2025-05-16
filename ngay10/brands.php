<?php
if (isset($_GET['category'])) {
    $category = $_GET['category'];
    $xml = simplexml_load_file('brands.xml');
    
    echo "<select>";
    foreach ($xml->brand as $brand) {
        if ((string)$brand['category'] === $category) {
            echo "<option>{$brand->name}</option>";
        }
    }
    echo "</select>";
}
?>