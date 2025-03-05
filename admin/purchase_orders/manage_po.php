<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from purchase_list where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
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
	/* Chrome, Safari, Edge, Opera */
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
		}

		/* Firefox */
		input[type=number] {
		-moz-appearance: textfield;
		}
		[name="tax_percentage"],[name="discount_percentage"]{
			width:5vw;
		}
</style>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update Purchase Order Details": "New Purchase Order" ?> </h3>
	</div>
	<div class="card-body">
		<form action="" id="purchase-form" method="POST">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="row">
				<div class="col-md-6 form-group">
				<label for="supplier_id">Supplier</label>
				<select name="supplier_id" id="supplier_id" class="custom-select custom-select-sm rounded-0 select2">
                    <?php 
                        $selected_supplier = "Select a Supplier";
                        $selected_supplier_id = "";
                        if (isset($supplier_id)) {
                            $sup_qry = $conn->query("SELECT * FROM supplier_list WHERE id = '{$supplier_id}'");
                            if ($sup_qry->num_rows > 0) {
                                $supplier = $sup_qry->fetch_array();
                                $selected_supplier = $supplier['name'];
                                $selected_supplier_id = $supplier['id'];
                            }
                        }
                    ?>
                    <option value="<?php echo $selected_supplier_id; ?>" selected><?php echo $selected_supplier; ?></option>
                    <?php 
                        $supplier_qry = $conn->query("SELECT * FROM `supplier_list` ORDER BY `name` ASC");
                        while($row = $supplier_qry->fetch_assoc()):
                    ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo (isset($supplier_id) && $supplier_id == $row['id']) ? 'selected' : ''; ?>>
                        <?php echo $row['name']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="supplier_id">Payment Term</label>
                <select name="payment_term" id="payment_term" class="custom-select custom-select-sm rounded-0">
                    <?php 
                        $selected_payment_term = $row['payment_term'] ?? 30;
                    ?>
                    <option value="30" <?php echo ($selected_payment_term == 30) ? "selected" : ""; ?>>30</option>
                    <option value="60" <?php echo ($selected_payment_term == 60) ? "selected" : ""; ?>>60</option>
                    <option value="90" <?php echo ($selected_payment_term == 90) ? "selected" : ""; ?>>90</option>
                    <option value="120" <?php echo ($selected_payment_term == 120) ? "selected" : ""; ?>>120</option>
                </select>

				</div>
				<div class="col-6">
                <?php if (!empty($reference_id)) : ?>
                    <p class="m-0"><b>Reference #:</b></p>
                    <p style="font-size: 15px;"><b><?php echo htmlspecialchars($reference_id); ?></b></p>
                <?php endif; ?>


                    <label for="supplier_id">Payment Type</label>
                    <select name="payment_type" id="payment_type" class="custom-select custom-select-sm rounded-0">
                        <?php 
                            $selected_payment_type = $row['payment_type'] ?? "CASH";
                        ?>
                        <option value="CASH" <?php echo ($selected_payment_type == "CASH") ? "selected" : ""; ?>>CASH</option>
                        <option value="CBC" <?php echo ($selected_payment_type == "CBC") ? "selected" : ""; ?>>CBC</option>
                        <option value="PBC" <?php echo ($selected_payment_type == "PBC") ? "selected" : ""; ?>>PBC</option>
                    </select>
                </div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<table class="table table-striped table-bordered" id="item-list">
						<colgroup>
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
                            <col width="10%">
						</colgroup>
						<thead>
							<tr class="bg-navy disabled">
								<th class="px-1 py-1 text-center">Item Code</th>
								<th class="px-1 py-1 text-center">Item Name</th>
								<th class="px-1 py-1 text-center">Req Name</th>
                                <th class="px-1 py-1 text-center">Cost Center</th>
								<th class="px-1 py-1 text-center">Detachment</th>
                                <th class="px-1 py-1 text-center">Category</th>
                                <th class="px-1 py-1 text-center">Unit Of Measurement</th>
								<th class="px-1 py-1 text-center">Quantity</th>
                                <th class="px-1 py-1 text-center">Unit Price</th>
								<th class="px-1 py-1 text-center">Total</th>
							</tr>
						</thead>
						<tbody>
                        <?php 
                        $sub_total = 0;
                        $order_items = []; // Default empty array

                        if (!empty($id)) {
                            $order_items_qry = $conn->query("SELECT * FROM purchase_list WHERE id = $id");
                            echo $conn->error;

                            while ($row = $order_items_qry->fetch_assoc()) {
                                $order_items[] = $row; // Store results in array
                                $sub_total += ($row['quantity'] * $row['amount_requested']);
                            }
                        }

                        // If no results (or no ID), include at least one blank row
                        if (empty($order_items)) {
                            $order_items[] = [
                                'quantity' => '',
                                'requestor_name' => '',
                                'detachment' => '',
                                'amount_requested' => '',
                                'item_id' => '',
                            ];
                        }

                        foreach ($order_items as $row): ?>
                            <tr class="po-item" data-id="">

                            <?php 
                                $selected_item = "Select an Item";
                                $selected_item_id = $row['item_id'] ?? '';
                                $selected_code = '';
                                $selected_unit_price = 0;

                                if (!empty($selected_item_id)) {
                                    $item_qry = $conn->query("SELECT * FROM item_list WHERE id = '{$selected_item_id}'");
                                    if ($item_qry->num_rows > 0) {
                                        $item = $item_qry->fetch_array();
                                        $selected_item = $item['name'];
                                        $selected_code = $item['code'];
                                        $selected_unit_price = $item['unit_price'];
                                    }
                                }
                            ?>

                            <td class="align-middle p-0 text-center">
                                <input type="text" class="text-center w-100 border-0" id="code" name="code" 
                                    value="<?php echo htmlspecialchars($selected_code); ?>" readonly />
                            </td>

                            <td class="align-middle p-1">
                                <select name="item_id" id="item_id" class="custom-select custom-select-sm rounded-0 select2" onchange="updateItemDetails()">
                                   
                                    <option value="<?php echo htmlspecialchars($selected_item_id); ?>" 
                                            data-code="<?php echo htmlspecialchars($selected_code); ?>" 
                                            data-unit-price="<?php echo htmlspecialchars($selected_unit_price); ?>" 
                                            selected>
                                        <?php echo $selected_item; ?>
                                    </option>

                                    <?php 
                                        $item_qry = $conn->query("SELECT * FROM `item_list` ORDER BY `name` ASC");
                                        while($row2 = $item_qry->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row2['id']; ?>" 
                                            data-code="<?php echo htmlspecialchars($row2['code']); ?>" 
                                            data-unit-price="<?php echo htmlspecialchars($row2['unit_price']); ?>">
                                        <?php echo htmlspecialchars($row2['name']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </td>

                            <td class="align-middle p-1">
                                <input type="text" class="text-center w-100 border-0" name="requestor_name" value="<?php echo htmlspecialchars($row['requestor_name']) ?>"/>
                            </td>

                            <td class="align-middle p-0 text-center">
                                <select name="cost_center" id="cost_center" class="custom-select custom-select-sm rounded-0">
                                    <?php 
                                        $selected_category = $row['cost_center'] ?? 'Finance';
                                    ?>
                                    <option value="Finance" <?php echo ($selected_category == "Finance") ? "selected" : ""; ?>>Finance</option>
                                    <option value="HR & Admin" <?php echo ($selected_category == "HR & Admin") ? "selected" : ""; ?>>HR & Admin</option>
                                    <option value="Sales & Marketing" <?php echo ($selected_category == "Sales & Marketing") ? "selected" : ""; ?>>Sales & Marketing</option>
                                    <option value="Executive Office" <?php echo ($selected_category == "Executive Office") ? "selected" : ""; ?>>Executive Office</option>
                                    <option value="General" <?php echo ($selected_category == "General") ? "selected" : ""; ?>>General</option>
                                    <option value="Supply Chain" <?php echo ($selected_category == "Supply Chain") ? "selected" : ""; ?>>Supply Chain</option>
                                    <option value="Ops. Excellence" <?php echo ($selected_category == "Ops. Excellence") ? "selected" : ""; ?>>Ops. Excellence</option>
                                    <option value="Operations" <?php echo ($selected_category == "Operations") ? "selected" : ""; ?>>Operations</option>
                                </select>
                            </td>

                            <td class="align-middle p-1">
                                <select name="detachment" id="detachment" class="custom-select custom-select-sm rounded-0 select2">
                                    <?php 
                                        $selected_detachment_id = $row['detachment'] ?? '';
                                        $selected_detachment = "N/A";

                                        if (!empty($selected_detachment_id)) {
                                            $detachment_qry = $conn->query("SELECT * FROM detachment WHERE id = '{$selected_detachment_id}'");
                                            if ($detachment_qry->num_rows > 0) {
                                                $detachment = $detachment_qry->fetch_array();
                                                $selected_detachment = $detachment['name'];
                                            }
                                        }
                                    ?>
                                    <option value="<?php echo htmlspecialchars($selected_detachment_id); ?>" selected><?php echo $selected_detachment; ?></option>
                                    <?php 
                                        $detachment_qry = $conn->query("SELECT * FROM `detachment` ORDER BY `name` ASC");
                                        while($row2 = $detachment_qry->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row2['id']; ?>" <?php echo (!empty($selected_detachment_id) && $selected_detachment_id == $row2['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row2['name']); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </td>

                            <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                let costCenter = document.getElementById("cost_center");
                                let detachment = document.getElementById("detachment");

                                function toggleDetachment() {
                                    if (costCenter.value === "Operations") {
                                        detachment.disabled = false;
                                    } else {
                                        detachment.disabled = true;
                                        detachment.innerHTML = '<option value="">N/A</option>'; // Reset to N/A when disabled
                                    }
                                }

                                costCenter.addEventListener("change", toggleDetachment);
                                toggleDetachment(); // Run once on page load
                            });
                            </script>


                            <td class="align-middle p-0 text-center">
                                <select name="category" id="category" class="custom-select custom-select-sm rounded-0">
                                    <?php 
                                        $selected_category = $row['category'] ?? 'General';
                                    ?>
                                    <option value="General" <?php echo ($selected_category == "General") ? "selected" : ""; ?>>General</option>
                                    <option value="Office" <?php echo ($selected_category == "Office") ? "selected" : ""; ?>>Office</option>
                                </select>
                            </td>

                            <td class="align-middle p-0 text-center">
                                <select name="unit_meas" id="unit_meas" class="custom-select custom-select-sm rounded-0">
                                    <?php 
                                        $selected_unit_meas = $row['unit_meas'] ?? 'Piece';
                                    ?>
                                    <option value="Piece" <?php echo ($selected_unit_meas == "Piece") ? "selected" : ""; ?>>Piece</option>
                                    <option value="Box" <?php echo ($selected_unit_meas == "Box") ? "selected" : ""; ?>>Box</option>
                                </select>
                            </td>

                            <td class="align-middle p-0 text-center">
                                <input type="number" class="text-center w-100 border-0" step="any" name="quantity" id="quantity" 
                                    value="<?php echo htmlspecialchars($row['quantity']); ?>" oninput="calculateTotal()" />
                            </td>

                            <td class="align-middle p-1" style="">
                                <input type="number" step="any" class="text-right w-100 border-0" name="amount_requested" id="amount_requested" 
                                    value="<?php echo htmlspecialchars($selected_unit_price); ?>" readonly />
                            </td>

                            <td class="align-middle p-1 text-right total-price" id="total_price">
                                <?php echo number_format((float)$row['quantity'] * (float)$selected_unit_price, 2); ?>
                            </td>

                            <script>
                                function updateItemDetails() {
                                    var select = document.getElementById("item_id");
                                    var selectedOption = select.options[select.selectedIndex];

                                    var itemCode = selectedOption.getAttribute("data-code");
                                    var unitPrice = parseFloat(selectedOption.getAttribute("data-unit-price")) || 0;

                                    document.getElementById("code").value = itemCode;
                                    document.getElementById("amount_requested").value = unitPrice;

                                    calculateTotal();
                                }

                                function calculateTotal() {
                                    var quantity = parseFloat(document.getElementById("quantity").value) || 0;
                                    var unitPrice = parseFloat(document.getElementById("amount_requested").value) || 0;
                                    var totalPrice = (quantity * unitPrice).toFixed(2);

                                    document.getElementById("total_price").innerText = totalPrice;
                                }
                            </script>

                            </tr>
                        <?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr class="bg-lightblue">
                                <tr>
                                    <th class="p-1 text-right" colspan="8">Sub Total</th>
                                    <th class="p-1 text-right" id="sub_total"><?php echo isset($sub_total) ? number_format($sub_total) : 0 ?></th>
                                </tr>
								<tr>
									<th class="p-1 text-right" colspan="8">Discount (%)
									<input type="number" step="any" name="discount_percentage" class="border-light text-right" value="<?php echo isset($discount_percentage) ? $discount_percentage : 0 ?>">
									</th>
									<th class="p-1"><input type="text" class="w-100 border-0 text-right" readonly value="<?php echo isset($discount_amount) ? $discount_amount : 0 ?>" name="discount_amount"></th>
								</tr>
								<tr>
									<th class="p-1 text-right" colspan="8">Tax Inclusive (%)
									<input type="number" step="any" name="tax_percentage" class="border-light text-right" value="<?php echo isset($tax_percentage) ? $tax_percentage : 0 ?>">
									</th>
									<th class="p-1"><input type="text" class="w-100 border-0 text-right" readonly value="<?php echo isset($tax_amount) ? $tax_amount : 0 ?>" name="tax_amount"></th>
								</tr>
								<tr>
                                <th class="p-1 text-right" colspan="8">Total</th>
                                <th class="p-1 text-right"> <input  name="total_amount" id="total" type="text" class="w-100 border-0 text-right" readonly value="<?php echo isset($tax_amount) ? number_format($sub_total - $discount_amount) : 0 ?>"></th>
                            </tr>
							</tr>
						</tfoot>
					</table>
                    <div class="row mb-2">
                        <div class="col-6">
                            <p class="m-0"><b>Prepared by:</b></p>
                            <div>
                                <input type="text" class="text-center w-100 form-control rounded-0" name="received_by" value="<?php echo isset($received_by) ? $received_by : ""?>"/>
                            </div>
                        </div>
                        <?php if(isset($id)): ?>
                            <div class="col-md-6">
                                <label for="status" class="control-label">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm rounded-0" 
                                    <?php echo ($_settings->userdata('type') != 3) ? 'disabled' : ''; ?>> 
                                    <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : ''; ?>>Pending</option>
                                    <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : ''; ?>>Approved</option>
                                    <option value="2" <?php echo isset($status) && $status == 2 ? 'selected' : ''; ?>>Denied</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
					<div class="row">
						<div class="col-md-6">
							<label for="notes" class="control-label">Notes</label>
							<textarea name="notes" id="notes" cols="10" rows="4" class="form-control rounded-0"><?php echo isset($notes) ? $notes : '' ?></textarea>
						</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="purchase-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=purchase_orders">Cancel</a>
	</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    function calculateTotals() {
        let subTotal = 0;
        
        document.querySelectorAll("#item-list tbody tr").forEach(row => {
            let qty = parseFloat(row.querySelector("input[name='quantity']").value) || 0;
            let amountRequested = parseFloat(row.querySelector("input[name='amount_requested']").value) || 0;
            let total = qty * amountRequested;
            subTotal += total;
            
            row.querySelector(".total-price").textContent = total.toFixed(2);
        });
        
        document.getElementById("sub_total").textContent = subTotal.toFixed(2);

        let discountPercentage = parseFloat(document.querySelector("input[name='discount_percentage']").value) || 0;
        let discountAmount = (subTotal * discountPercentage) / 100;
        document.querySelector("input[name='discount_amount']").value = discountAmount.toFixed(2);

        let taxPercentage = parseFloat(document.querySelector("input[name='tax_percentage']").value) || 0;
        let taxAmount = ((subTotal - discountAmount) * taxPercentage) / 100;
        document.querySelector("input[name='tax_amount']").value = taxAmount.toFixed(2);

        let totalAmount = subTotal - discountAmount + taxAmount;
        document.querySelector("input[name='total_amount']").value = totalAmount.toFixed(2);
    }

    document.querySelectorAll("input[name='quantity'], input[name='amount_requested'], input[name='discount_percentage'], input[name='tax_percentage']").forEach(input => {
        input.addEventListener("input", calculateTotals);
    });

    calculateTotals(); // Initial Calculation on page load
});

</script>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $supplier_id = isset($_POST['supplier_id']) ? $_POST['supplier_id'] : '';
    $received_by = isset($_POST['received_by']) ? $_POST['received_by'] : '';
    //$date_purchase = isset($_POST['date_purchase']) ? $_POST['date_purchase'] : null;
    //$date_received = isset($_POST['date_recieved']) ? $_POST['date_recieved'] : null;
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $cost_center = isset($_POST['cost_center']) ? $_POST['cost_center'] : "Finance";
    $detachment = isset($_POST['detachment']) ? $_POST['detachment'] : 1;

    $category = isset($_POST['category']) ? $_POST['category'] : "General";
    $payment_term = isset($_POST['payment_term']) ? $_POST['payment_term'] : 30;
    $payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : "CASH";
    $unit_meas = isset($_POST['unit_meas']) ? $_POST['unit_meas'] : "Piece";

    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : 1;
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
    $requestor_name = isset($_POST['requestor_name']) ? $_POST['requestor_name'] : '';
    $amount_requested = isset($_POST['amount_requested']) ? $_POST['amount_requested'] : 0;
    $discount_percentage = isset($_POST['discount_percentage']) ? $_POST['discount_percentage'] : 0;
    $tax_percentage = isset($_POST['tax_percentage']) ? $_POST['tax_percentage'] : 0;
    $discount_amount = isset($_POST['discount_amount']) ? $_POST['discount_amount'] : 0;
    $tax_amount = isset($_POST['tax_amount']) ? $_POST['tax_amount'] : 0;
    $total_amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : 0;

    if (!empty($id)) {
        // Update the existing purchase
        $update_query = "UPDATE purchase_list SET 
            supplier_id = '$supplier_id',
            item_id = '$item_id',
            quantity = '$quantity',
            discount_percentage = '$discount_percentage',
            discount_amount = '$discount_amount',
            tax_percentage = '$tax_percentage',
            tax_amount = '$tax_amount',
            cost_center = '$cost_center',
            detachment = '$detachment',
            category = '$category',
            payment_term = '$payment_term',
            payment_type = '$payment_type',
            unit_meas = '$unit_meas',
            requestor_name = '$requestor_name',
            received_by = '$received_by',
            amount_requested = '$amount_requested',
            total_amount = '$total_amount',
            notes = '$notes'";
    
        if (isset($_POST['status'])) {
            $status = $_POST['status'];
            $update_query .= ", status = '$status'";
        }
    
        $update_query .= " WHERE id = '$id'";
    
        if ($conn->query($update_query)) {
            $purchase_id = $id;
    
            // Check if status == 2
            $check_query = "SELECT checked, reference_id FROM purchase_list WHERE id = '$purchase_id' AND status = 1";
            $check_result = $conn->query($check_query);
    
            if ($check_result->num_rows > 0) {
                $row = $check_result->fetch_assoc();
                if ($row['checked'] == 0) {
                    // Use existing reference_id or generate a new one
                    $reference_number = $row['reference_id'];
    
                    // Fetch item name from item_list using item_id
                    $item_name = "";
                    $item_query = "SELECT name FROM item_list WHERE id = '$item_id'";
                    $item_result = $conn->query($item_query);
                    if ($item_result->num_rows > 0) {
                        $item_row = $item_result->fetch_assoc();
                        $item_name = $item_row['name'];
                    }
    
                    // Insert into approved table
                    $insert_query = "INSERT INTO approved (item_name, reference, total_price, stock, payment_term, payment_type, unit_measurement) 
                                    VALUES ('$item_name', '$reference_number', '$total_amount', '$quantity', '$payment_term', '$payment_type', '$unit_meas')";
                    if ($conn->query($insert_query)) {
                        $reference_id = $conn->insert_id;
    
                        // Update purchase_list with reference_id and set checked = 1
                        $update_purchase_query = "UPDATE purchase_list SET checked = 1 WHERE id = '$purchase_id'";
                        $conn->query($update_purchase_query);

                        $update_inventory= "UPDATE inventory SET total_price = total_price + '$total_amount', Stock = Stock + '$quantity' WHERE item_id = '$item_id'";
                        $conn->query($update_inventory);
                    }
                }
            }
        }
    } else {
        $status = isset($_POST['status']) ? $_POST['status'] : 0;
        // Generate a unique reference ID
        $range = mt_rand(100000000, 999999999);
        $ref = "AGF-PO" . $range;
    
        // Insert a new purchase
        $insert_query = "INSERT INTO purchase_list (reference_id, 
        supplier_id, 
        item_id, 
        quantity, 
        discount_percentage, 
        discount_amount, 
        tax_percentage, 
        tax_amount, 
        cost_center,
        detachment,
        category,
        payment_term, 
        payment_type, 
        unit_meas, 
        requestor_name, 
        received_by, 
        amount_requested, 
        total_amount, 
        notes, 
        status) 
        VALUES ('$ref', 
        '$supplier_id', 
        '$item_id', 
        '$quantity', 
        '$discount_percentage', 
        '$discount_amount', 
        '$tax_percentage', 
        '$tax_amount', 
        '$cost_center',
        '$detachment',
        '$category',
        '$payment_term', 
        '$payment_type', 
        '$unit_meas',  
        '$requestor_name', 
        '$received_by', 
        '$amount_requested', 
        '$total_amount', 
        '$notes', 
        '$status')";
    
        if ($conn->query($insert_query)) {
            $purchase_id = $conn->insert_id;
    
            // // Check if item_id exists in inventory
            // $check_query = "SELECT * FROM inventory WHERE item_id = '$item_id'";
            // $result = $conn->query($check_query);
    
            // if ($result->num_rows > 0) {
            //     // Item exists, update Stock and total_price
            //     $update_inventory_query = "UPDATE inventory 
            //                                SET Stock = Stock + '$quantity', 
            //                                    total_price = total_price + '$total_amount' 
            //                                WHERE item_id = '$item_id'";
            //     $conn->query($update_inventory_query);
            // } else {
                // Item does not exist, insert new inventory record
                // $get_item_name = $conn->query("SELECT name FROM item_list WHERE id = '$item_id'");
                // $item_name_row = $get_item_name->fetch_assoc();
                // $item_name = $item_name_row['name'];
    
                // $insert_inventory_query = "INSERT INTO inventory (item_id, item_name, total_price, Stock) 
                //                            VALUES ('$item_id', '$item_name', '$total_amount', '$quantity')";
                // $conn->query($insert_inventory_query);
        }
    }
    
    echo "<script>alert('Purchase order saved successfully!'); window.location.href='?page=purchase_orders/view_po&id=$purchase_id';</script>";
}
?>