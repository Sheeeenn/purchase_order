<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>


<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from inventory where id = '{$_GET['id']}' ");
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
		<h3 class="card-title"><?php echo isset($id) ? "Update Order Details": "New Order" ?> </h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-flat btn-success" id="print" type="button"><i class="fa fa-print"></i> Print</button>
		    <a class="btn btn-sm btn-flat btn-primary" href="?page=orders/manage_orders&id=<?php echo $id ?>">Edit</a>
		    <a class="btn btn-sm btn-flat btn-default" href="?page=orders">Back</a>
        </div>
	</div>
	<div class="card-body" id="out_print">
        <div class="row">
        <div class="col-6 d-flex align-items-center">
            <div>
                <p class="m-0"><?php echo $_settings->info('company_name') ?></p>
                <p class="m-0"><?php echo $_settings->info('company_email') ?></p>
                <p class="m-0"><?php echo $_settings->info('company_address') ?></p>
            </div>
        </div>
        <div class="col-6">
            <center><img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="" height="200px"></center>
            <h2 class="text-center"><b>ORDER</b></h2>
        </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered" id="item-list">
                    <colgroup>
                        <col width="15%">
                        <col width="15%">
                        <col width="20%">
                        <col width="30%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr class="bg-navy disabled" style="">
                            <th class="bg-navy disabled text-light px-1 py-1 text-center">Stock</th>
                            <th class="bg-navy disabled text-light px-1 py-1 text-center">Item ID</th>
                            <th class="bg-navy disabled text-light px-1 py-1 text-center">Item Name</th>
                            <th class="bg-navy disabled text-light px-1 py-1 text-center">Description</th>
                            <th class="bg-navy disabled text-light px-1 py-1 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($id)):
                        $order_items_qry = $conn->query("SELECT * from Inventory where id = $id");
                        $sub_total = 0;
                        while($row = $order_items_qry->fetch_assoc()):
                            //$sub_total += ($row['quantity'] * $row['unit_price']);
                        ?>
                        <tr class="po-item" data-id="">
                            <td class="align-middle p-0 text-center"><?php echo $row['Stock'] ?></td>
                            <td class="align-middle p-1 text-center"><?php echo $row['item_id'] ?></td>
                            <td class="align-middle p-1 text-center"><?php echo $row['item_name'] ?></td>
                            <td class="align-middle p-1 item-description"><?php $new = $row['item_id']; $order_items_qry = $conn->query("SELECT * from item_list where id = $new"); while($row2 = $order_items_qry->fetch_assoc()){ echo $row2['description']; }?></td>
                            <td class="align-middle p-1 text-center"><?php echo number_format($row['total_price']) ?></td>
                        </tr>
                        <?php endwhile;endif; ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-6">
                        <label for="notes" class="control-label">Notes</label>
                        <p><?php echo isset($notes) ? $notes : '' ?></p>
                    </div>
                    <div class="col-6">
                        <label for="status" class="control-label">Status</label>
                        <br>
                        <?php 
                        if($Stock <= 5 ){
                            echo '<span class="badge badge-danger">Restock</span>';
                        } else {
                            echo '<span class="badge badge-secondary">Normal</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<table class="d-none" id="item-clone">
	<tr class="po-item" data-id="">
		<td class="align-middle p-1 text-center">
			<button class="btn btn-sm btn-danger py-0" type="button" onclick="rem_item($(this))"><i class="fa fa-times"></i></button>
		</td>
		<td class="align-middle p-0 text-center">
			<input type="number" class="text-center w-100 border-0" step="any" name="qty[]"/>
		</td>
		<td class="align-middle p-1">
			<input type="text" class="text-center w-100 border-0" name="unit[]"/>
		</td>
		<td class="align-middle p-1">
			<input type="hidden" name="item_id[]">
			<input type="text" class="text-center w-100 border-0 item_id" required/>
		</td>
		<td class="align-middle p-1 item-description"></td>
		<td class="align-middle p-1">
			<input type="number" step="any" class="text-right w-100 border-0" name="unit_price[]" value="0"/>
		</td>
		<td class="align-middle p-1 text-right total-price">0</td>
	</tr>
</table>
<script>
	$(function(){
        $('#print').click(function(e){
            e.preventDefault();
            start_loader();
            var _h = $('head').clone()
            var _p = $('#out_print').clone()
            var _el = $('<div>')
                _p.find('thead th').attr('style','color:black !important')
                _el.append(_h)
                _el.append(_p)
                
            var nw = window.open("","","width=1200,height=950")
                nw.document.write(_el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        end_loader();
                        nw.close()
                    }, 300);
                }, 200);
        })
    })
</script>