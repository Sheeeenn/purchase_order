<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $qry = $conn->query("DELETE FROM inventory WHERE id = $id");

    if ($qry) {
        echo "<script>window.location.href = '?page=orders';</script>";
        exit();
    } else {
        echo "<script>window.location.href = '?page=orders';</script>";
        exit();
    }
}

?>



<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Orders</h3>
		<!-- <div class="card-tools">
			<a href="?page=purchase_orders/manage_po" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div> -->
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-hover table-striped">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20">
					<col width="15%">
                    <col width="15%">
					<col width="10%">
					<col width="10%">
					<col width="10%">

				</colgroup>
				<thead>
                <tr class="bg-navy disabled">
						<th class="text-center">#</th>
						<th class="text-center">Item No.</th>
						<th class="text-center">Item Name</th>
                        <th class="text-center">Item Code</th>
						<th class="text-center">Total Price</th>
						<th class="text-center">Stock</th>
						<th class="text-center">Remarks</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from inventory");
						while($row = $qry->fetch_assoc()):
							//$row['item_count'] = $conn->query("SELECT * FROM inventory")->num_rows;
					?>
						<tr>
							<td class="text-center"><?php echo $row['id']; ?></td>
							<td class="text-center"><?php echo $row['item_id']; ?></td>
							<td class="text-center"><?php echo $row['item_name'] ?></td>
                            <td class="text-center"><?php echo $row['item_code'] ?></td>
							<td class="text-center"><?php echo number_format($row['total_price']) ?></td>
							<td class="text-center"><?php echo number_format($row['Stock']) ?></td>
							<td class="text-center">
								<?php 
									if($row['Stock'] <= 5 ){
                                        echo '<span class="badge badge-danger">Restock</span>';
                                    } else {
                                        echo '<span class="badge badge-secondary">Normal</span>';
									}
								?>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
								  	<a class="dropdown-item" href="?page=orders/view_orders&id=<?php echo $row['id']?>&itemname=<?php echo $row['item_name'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item" href="?page=orders/manage_orders&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="dropdown-item delete_data" onclick="return confirm('Are you sure you want to delete this item?');">
                                            <span class="fa fa-trash text-danger"></span> Delete
                                        </button>
                                    </form>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>


<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this rent permanently?","delete_rent",[$(this).attr('data-id')])
		})
		$('.view_details').click(function(){
			uni_modal("Reservaton Details","orders/view_details.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.renew_data').click(function(){
			_conf("Are you sure to renew this rent data?","renew_rent",[$(this).attr('data-id')]);
		})
		$('.table th,.table td').addClass('px-1 py-0 align-middle')
		$('.table').dataTable();
	})
	function delete_rent($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_rent",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
	function renew_rent($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=renew_rent",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>