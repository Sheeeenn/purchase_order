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


				</div>
				<div class="col-6">
                    <p  class="m-0"><b>Reference #:</b></p>
                    <p style="font-size: 22px;"><b><?php echo isset($reference_id) ? $reference_id : "REF-000000000"?></b></p>
                </div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<table class="table table-striped table-bordered" id="item-list">
						<colgroup>
                            <col width="10%">
                            <col width="10%">
                            <col width="20%">
                            <col width="30%">
                            <col width="15%">
                            <col width="15%">
						</colgroup>
						<thead>
							<tr class="bg-navy disabled">
								<th class="px-1 py-1 text-center">Qty</th>
								<th class="px-1 py-1 text-center">Req Name</th>
								<th class="px-1 py-1 text-center">Item</th>
								<th class="px-1 py-1 text-center">Detachment</th>
								<th class="px-1 py-1 text-center">Amount Requested</th>
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
                                <td class="align-middle p-0 text-center">
                                    <input type="number" class="text-center w-100 border-0" step="any" name="quantity" value="<?php echo htmlspecialchars($row['quantity']) ?>"/>
                                </td>
                                <td class="align-middle p-1">
                                    <input type="text" class="text-center w-100 border-0" name="requestor_name" value="<?php echo htmlspecialchars($row['requestor_name']) ?>"/>
                                </td>
                                <td class="align-middle p-1">
                                    <select name="item_id" id="item_id" class="custom-select custom-select-sm rounded-0 select2">
                                        <?php 
                                            $selected_item = "Select an Item";
                                            $selected_item_id = $row['item_id'] ?? '';

                                            if (!empty($selected_item_id)) {
                                                $item_qry = $conn->query("SELECT * FROM item_list WHERE id = '{$selected_item_id}'");
                                                if ($item_qry->num_rows > 0) {
                                                    $item = $item_qry->fetch_array();
                                                    $selected_item = $item['name'];
                                                }
                                            }
                                        ?>
                                        <option value="<?php echo htmlspecialchars($selected_item_id); ?>" selected><?php echo $selected_item; ?></option>
                                        <?php 
                                            $item_qry = $conn->query("SELECT * FROM `item_list` ORDER BY `name` ASC");
                                            while($row2 = $item_qry->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $row2['id']; ?>" <?php echo (!empty($selected_item_id) && $selected_item_id == $row2['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($row2['name']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td class="align-middle p-1 item-description">
                                    <input type="text" class="text-center w-100 border-0" name="detachment" value="<?php echo htmlspecialchars($row['detachment']) ?>"/>
                                </td>
                                <td class="align-middle p-1">
                                    <input type="number" step="any" class="text-right w-100 border-0" name="amount_requested" value="<?php echo htmlspecialchars($row['amount_requested']) ?>"/>
                                </td>
                                <td class="align-middle p-1 text-right total-price">
                                    <?php echo number_format((float)$row['quantity'] * (float)$row['amount_requested'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr class="bg-lightblue">
                                <tr>
                                    <th class="p-1 text-right" colspan="5">Sub Total</th>
                                    <th class="p-1 text-right" id="sub_total"><?php echo isset($sub_total) ? number_format($sub_total) : 0 ?></th>
                                </tr>
								<tr>
									<th class="p-1 text-right" colspan="5">Discount (%)
									<input type="number" step="any" name="discount_percentage" class="border-light text-right" value="<?php echo isset($discount_percentage) ? $discount_percentage : 0 ?>">
									</th>
									<th class="p-1"><input type="text" class="w-100 border-0 text-right" readonly value="<?php echo isset($discount_amount) ? $discount_amount : 0 ?>" name="discount_amount"></th>
								</tr>
								<tr>
									<th class="p-1 text-right" colspan="5">Tax Inclusive (%)
									<input type="number" step="any" name="tax_percentage" class="border-light text-right" value="<?php echo isset($tax_percentage) ? $tax_percentage : 0 ?>">
									</th>
									<th class="p-1"><input type="text" class="w-100 border-0 text-right" readonly value="<?php echo isset($tax_amount) ? $tax_amount : 0 ?>" name="tax_amount"></th>
								</tr>
								<tr>
                                <th class="p-1 text-right" colspan="5">Total</th>
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
                        <div class="col-6 row">
                            <div class="col-6">
                                <p class="m-0"><b>Date of Purchase:</b></p>
                                <input type="date" name="date_purchase" class="form-control" 
                                    value="<?php echo ($date_purchase == '0000-00-00' || empty($date_purchase)) 
                                        ? '' 
                                        : date('Y-m-d', strtotime($date_purchase)); ?>">
                            </div>
                            <div class="col-6">
                                <p class="m-0"><b>Date Received:</b></p>
                                <input type="date" name="date_recieved" class="form-control" 
                                    value="<?php echo ($date_recieved == '0000-00-00' || empty($date_recieved)) 
                                        ? '' 
                                        : date('Y-m-d', strtotime($date_recieved)); ?>">
                            </div>
                        </div>
                    </div>
					<div class="row">
						<div class="col-md-6">
							<label for="notes" class="control-label">Notes</label>
							<textarea name="notes" id="notes" cols="10" rows="4" class="form-control rounded-0"><?php echo isset($notes) ? $notes : '' ?></textarea>
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
    $date_purchase = isset($_POST['date_purchase']) ? $_POST['date_purchase'] : null;
    $date_received = isset($_POST['date_recieved']) ? $_POST['date_recieved'] : null;
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $detachment = isset($_POST['detachment']) ? $_POST['detachment'] : '';
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
            detachment = '$detachment',
            requestor_name = '$requestor_name',
            received_by = '$received_by',
            amount_requested = '$amount_requested',
            total_amount = '$total_amount',
            date_purchase = '$date_purchase',
            date_recieved = '$date_received',
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
                                    VALUES ('$item_name', '$reference_number', '$total_amount', '$quantity', 'TEST', 'TEST', 'TEST')";
                    if ($conn->query($insert_query)) {
                        $reference_id = $conn->insert_id;
    
                        // Update purchase_list with reference_id and set checked = 1
                        $update_purchase_query = "UPDATE purchase_list SET reference_id = '$reference_id', checked = 1 WHERE id = '$purchase_id'";
                        $conn->query($update_purchase_query);
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
        $insert_query = "INSERT INTO purchase_list (reference_id, supplier_id, item_id, quantity, discount_percentage, discount_amount, tax_percentage, tax_amount, detachment, requestor_name, received_by, amount_requested, total_amount, date_purchase, date_recieved, notes, status) 
        VALUES ('$ref', '$supplier_id', '$item_id', '$quantity', '$discount_percentage', '$discount_amount', '$tax_percentage', '$tax_amount', '$detachment', '$requestor_name', '$received_by', '$amount_requested', '$total_amount', '$date_purchase', '$date_received', '$notes', '$status')";
    
        if ($conn->query($insert_query)) {
            $purchase_id = $conn->insert_id;
    
            // Check if item_id exists in inventory
            $check_query = "SELECT * FROM inventory WHERE item_id = '$item_id'";
            $result = $conn->query($check_query);
    
            if ($result->num_rows > 0) {
                // Item exists, update Stock and total_price
                $update_inventory_query = "UPDATE inventory 
                                           SET Stock = Stock + '$quantity', 
                                               total_price = total_price + '$total_amount' 
                                           WHERE item_id = '$item_id'";
                $conn->query($update_inventory_query);
            } else {
                // Item does not exist, insert new inventory record
                $get_item_name = $conn->query("SELECT name FROM item_list WHERE id = '$item_id'");
                $item_name_row = $get_item_name->fetch_assoc();
                $item_name = $item_name_row['name'];
    
                $insert_inventory_query = "INSERT INTO inventory (item_id, item_name, total_price, Stock) 
                                           VALUES ('$item_id', '$item_name', '$total_amount', '$quantity')";
                $conn->query($insert_inventory_query);
            }
        }
    }
    
    echo "<script>alert('Purchase order saved successfully!'); window.location.href='?page=purchase_orders/view_po&id=$purchase_id';</script>";
}
?>