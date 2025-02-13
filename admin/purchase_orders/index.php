<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Purchase Orders</h3>
		<div class="card-tools">
			<a href="?page=purchase_orders/manage_po" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-hover table-striped">
				<colgroup>
					<col width="5%"> 
					<col width="15%">
					<col width="10%">
					<col width="15%">
					<col width="5%">
                    <col width="15">
					<col width="15%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr class="bg-navy disabled">
						<th class="text-center">#</th>
						<th class="text-center">Reference ID</th>
                        <th class="text-center">Supplier</th>
                        <th class="text-center">Item</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Total Amount</th>
						<th class="text-center">Date of Request</th>
						<th class="text-center">Status</th>
						<th class="text-center">Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT * from purchase_list ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $row['id']; ?></td>
							<td class="text-center"><?php echo $row['reference_id']; ?></td>
							<td class="text-center">
                                <?php 
                                $query = $conn->query("SELECT name FROM supplier_list WHERE id = '{$row['supplier_id']}'"); 
                                $result = $query->fetch_assoc();
                                echo $result['name'] ?? 'N/A';
                                ?>
                            </td>
							<td class="text-center">
                                <?php 
                                $query = $conn->query("SELECT name FROM item_list WHERE id = '{$row['item_id']}'"); 
                                $result = $query->fetch_assoc();
                                echo $result['name'] ?? 'N/A';
                                ?>
                            </td>
                            <td class="text-center"><?php echo number_format($row['quantity']) ?></td>
							<td class="text-center"><?php echo number_format($row['total_amount']) ?></td>
							<td class="text-center"><?php echo date("M d,Y",strtotime($row['date_request'])) ; ?></td>
							<td class="text-center">
								<?php 
									switch ($row['status']) {
										case '1':
											echo '<span class="badge badge-success">Approved</span>';
											break;
										case '2':
											echo '<span class="badge badge-danger">Denied</span>';
											break;
										default:
											echo '<span class="badge badge-secondary">Pending</span>';
											break;
									}
								?>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
								  	<a class="dropdown-item" href="?page=purchase_orders/view_po&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item" href="?page=purchase_orders/manage_po&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
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
	
</script>