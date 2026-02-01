<style>
    .img-thumb-path{
        width:100px;
        height:80px;
        object-fit:scale-down;
        object-position:center center;
    }
</style>
<div class="card card-outline card-primary rounded-0 shadow">
	<div class="card-header">
		<h3 class="card-title">List of Tests</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-hover table-striped">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="30%">
					<col width="20%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr class="bg-gradient-primary text-light">
						<th>#</th>
						<th>Date Created</th>
						<th>Name</th>
						<th>Price</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT * from `test_list` where delete_flag = 0 and status = 1 order by `name` asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class=""><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td class=""><p class="m-0 truncate-1"><?php echo $row['name'] ?></p></td>
							<td class=""><?= number_format($row['cost'],2) ?></td>
							<td class="text-center">
								<span class="rounded-pill badge badge-primary col-6">Active</span>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
								  	<a class="dropdown-item view_data" href="javascript:void(0)" data-id ="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
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
		// Initialize Bootstrap dropdowns
		$('.dropdown-toggle').dropdown();
		
		$('.view_data').click(function(){
			var id = $(this).attr('data-id');
			if(typeof uni_modal === 'function'){
				uni_modal("Test Details","tests/view_test.php?id="+id);
			} else {
				// Fallback - open in new window or show alert
				window.location.href = "?page=tests/view_test&id="+id;
			}
		})
		$('.table td, .table th').addClass('py-1 px-2 align-middle')
		if($.fn.dataTable){
			$('.table').dataTable({
				columnDefs: [
					{ orderable: false, targets: 5 }
				],
			});
		}
	})
</script>
