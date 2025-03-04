<?php
//require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `item_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<style>
    span.select2-selection.select2-selection--single {
        border-radius: 0;
        padding: 0.25rem 0.5rem;
        padding-top: 0.25rem;
        padding-right: 0.5rem;
        padding-bottom: 0.25rem;
        padding-left: 0.5rem;
        height: auto;
    }
</style>
<form action="" id="item-form" method="POST">
    <input type="hidden" name="id" value="<?php echo isset($id);?>">

    <div class="container-fluid">
        <div class="form-group">
            <label for="name" class="control-label">Item Name</label>
            <input type="text" name="name" id="name" class="form-control rounded-0" value="<?php echo isset($name) ? $name :"" ?>" required>
        </div>
        <div class="form-group">
            <label for="code" class="control-label">Item Code</label>
            <input type="text" name="code" id="code" class="form-control rounded-0" 
                value="<?php echo isset($code) ? htmlspecialchars($code) : ''; ?>" 
                disabled required>
        </div>

        <div class="form-group">
            <label for="code" class="control-label">Category</label>
            <select name="category" id="category" class="custom-select custom-select-sm rounded-0">
                <?php 
                    $selected_category = $category ? $category : '';
                ?>
                <option value="Office Supply" <?php echo ($selected_category == "Office Supply") ? "selected" : ""; ?>>Office Supply</option>
                <option value="PPE" <?php echo ($selected_category == "PPE") ? "selected" : ""; ?>>PPE</option>
                <option value="Furniture" <?php echo ($selected_category == "Furniture") ? "selected" : ""; ?>>Furniture</option>
                <option value="Equipment" <?php echo ($selected_category == "Equipment") ? "selected" : ""; ?>>Equipment</option>
                <option value="Service" <?php echo ($selected_category == "Service") ? "selected" : ""; ?>>Service</option>
                <option value="Others" <?php echo ($selected_category == "Others") ? "selected" : ""; ?>>Others</option>

            </select>
        </div>
        
        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea rows="3" name="description" id="description" class="form-control rounded-0" required><?php echo isset($description) ? $description :"" ?></textarea>
        </div>
        <div class="form-group">
            <label for="unit" class="control-label">Unit Price</label>
            <input type="text" name="unit" id="unit" class="form-control rounded-0" value="<?php echo isset($unit_price) ? number_format($unit_price) :"0" ?>" required>
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" class="form-control rounded-0" required>
            <option value="1" <?php echo isset($status) && $status == "1" ? "selected" : ""; ?>>Active</option>
            <option value="0" <?php echo isset($status) && $status == "0" ? "selected" : ""; ?>>Inactive</option>

            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary rounded-0">Save</button>
        </div>
    </div>
</form>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Generate a unique number
    $range = mt_rand(10000, 99999); // Adjust the number range as needed

    // Define category prefixes
    $prefixes = [
        "Office Supply" => "OFC",
        "PPE" => "PPE",
        "Furniture" => "FUR",
        "Equipment" => "EQU",
        "Service" => "SER",
        "Others" => "OTH"
    ];

    // Set default prefix in case category is not found
    $prefix = isset($prefixes[$category]) ? $prefixes[$category] : "OTH"; 

    // Generate final reference code
    $code = $prefix . "-" . $range;

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    //$code = mysqli_real_escape_string($conn, $_POST['code']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $unit_price = floatval(str_replace(',', '', $_POST['unit'])); // Ensure correct number format
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    print_r($_POST);

    if ($id) {
        // Update existing record
        $sql = "UPDATE item_list SET name='$name', category='$category', description='$description', unit_price='$unit_price', status='$status' WHERE id='$id'";
        if ($conn->query($sql)) {
            echo "<script>window.location.href='?page=items';</script>";
        }
    } else {
        // Insert new record into item_list
        $sql = "INSERT INTO item_list (name, code, category, description, unit_price, status) VALUES ('$name', '$code', '$category', '$description', '$unit_price', '$status')";
        if ($conn->query($sql)) {
            // Get the last inserted ID
            $item_id = $conn->insert_id;
    
            // Insert into inventory using the new item_id
            $sql = "INSERT INTO inventory (item_id, item_name, item_code, total_price, stock, notes) VALUES ('$item_id', '$name', '$code', 0, 0, '')";
            if ($conn->query($sql)) {
                echo "<script>window.location.href='?page=items';</script>";
            }
        }
    }
    
}
?>
