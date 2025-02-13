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
                    <p style="font-size: 22px;"><b><?php echo $reference_id ?></b></p>
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
							if(isset($id)):
							$order_items_qry = $conn->query("SELECT * from purchase_list where id = $id");
                            $sub_total = 0;
							echo $conn->error;
							while($row = $order_items_qry->fetch_assoc()):
                                $sub_total += ($row['quantity'] * $row['amount_requested']);
							?>
							<tr class="po-item" data-id="">
								<td class="align-middle p-0 text-center">
									<input type="number" class="text-center w-100 border-0" step="any" name="quantity" value="<?php echo $row['quantity'] ?>"/>
								</td>
								<td class="align-middle p-1">
									<input type="text" class="text-center w-100 border-0" name="requestor_name" value="<?php echo $row['requestor_name'] ?>"/>
								</td>
								<td class="align-middle p-1">
                                <select name="item_id" id="item_id" class="custom-select custom-select-sm rounded-0 select2">
                                    <?php 
                                        $selected_item = "Select an Item";
                                        $selected_item_id = "";
                                        if (isset($item_id)) {
                                            $item_qry = $conn->query("SELECT * FROM item_list WHERE id = '{$item_id}'");
                                            if ($item_qry->num_rows > 0) {
                                                $item = $item_qry->fetch_array();
                                                $selected_item = $item['name'];
                                                $selected_item_id = $item['id'];
                                            }
                                        }
                                    ?>
                                    <option value="<?php echo $selected_item_id; ?>" selected><?php echo $selected_item; ?></option>
                                    <?php 
                                        $item_qry = $conn->query("SELECT * FROM `item_list` ORDER BY `name` ASC");
                                        while($row2 = $item_qry->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row2['id']; ?>" <?php echo (isset($item_id) && $item_id == $row2['id']) ? 'selected' : ''; ?>>
                                        <?php echo $row2['name']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
								</td>
								<td class="align-middle p-1 item-description">
                                    <input type="text" class="text-center w-100 border-0" name="detachment" value="<?php echo $row['detachment']?>"/>
                                </td>
								<td class="align-middle p-1">
									<input type="number" step="any" class="text-right w-100 border-0" name="amount_requested"  value="<?php echo ($row['amount_requested']) ?>"/>
								</td>
								<td class="align-middle p-1 text-right total-price"><?php echo number_format($row['quantity'] * $row['amount_requested']) ?></td>
							</tr>
							<?php endwhile;endif; ?>
						</tbody>
						<tfoot>
							<tr class="bg-lightblue">
                                <tr>
                                    <th class="p-1 text-right" colspan="5">Sub Total</th>
                                    <th class="p-1 text-right" id="sub_total"><?php echo number_format($sub_total) ?></th>
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
                            <p class="m-0"><b>Received by:</b></p>
                            <div>
                                <input type="text" class="text-center w-100 form-control rounded-0" name="received_by" value="<?php echo $received_by?>"/>
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
						<div class="col-md-6">
							<label for="status" class="control-label">Status</label>
							<select name="status" id="status" class="form-control form-control-sm rounded-0">
								<option value="0" <?php echo isset($status) && $status == 0 ? 'selected': '' ?>>Pending</option>
								<option value="1" <?php echo isset($status) && $status == 1 ? 'selected': '' ?>>Approved</option>
								<option value="2" <?php echo isset($status) && $status == 2 ? 'selected': '' ?>>Denied</option>
							</select>
						</div>
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

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $supplier_id = isset($_POST['supplier_id']) ? $_POST['supplier_id'] : '';
    $received_by = isset($_POST['received_by']) ? $_POST['received_by'] : '';
    $date_purchase = isset($_POST['date_purchase']) ? $_POST['date_purchase'] : null;
    $date_received = isset($_POST['date_recieved']) ? $_POST['date_recieved'] : null;
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $detachment = isset($_POST['detachment']) ? $_POST['detachment'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 0;
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
        $update_query = "UPDATE purchase_list SET supplier_id='$supplier_id', item_id='$item_id', quantity='$quantity', discount_percentage='$discount_percentage', discount_amount='$discount_amount', tax_percentage='$tax_percentage', tax_amount='$tax_amount', detachment='$detachment', requestor_name='$requestor_name', received_by='$received_by', amount_requested='$amount_requested', total_amount='$total_amount', date_purchase='$date_purchase', date_recieved='$date_received', notes='$notes', status='$status' WHERE id='$id'";
        if ($conn->query($update_query)) {
            $purchase_id = $id;
        }
    } else {
        // Insert a new purchase
        $insert_query = "INSERT INTO purchase_list (supplier_id, received_by, date_purchase, date_recieved, notes, status, discount_percentage, tax_percentage, discount_amount, tax_amount) VALUES ('$supplier_id', '$received_by', '$date_purchase', '$date_received', '$notes', '$status', '$discount_percentage', '$tax_percentage', '$discount_amount', '$tax_amount')";
        if ($conn->query($insert_query)) {
            $purchase_id = $conn->insert_id;
        }
    }

    echo "<script>alert('Purchase order saved successfully!'); window.location.href='?page=purchase_orders/view_po&id=$purchase_id';</script>";
}
?>