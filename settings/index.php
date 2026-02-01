<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: scale-down;
		border-radius: 100% 100%;
	}
	img#cimg2{
		height: 50vh;
		width: 100%;
		object-fit: contain;
	}
</style>
<div class="col-lg-12">
	<div class="card card-outline card-dark rounded-0 shadow">
		<div class="card-header">
			<h5 class="card-title">System Information</h5>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="name" class="control-label">System Name</label>
				<input type="text" class="form-control form-control-sm" readonly value="<?php echo $_settings->info('name') ?>">
			</div>
			<div class="form-group">
				<label for="short_name" class="control-label">System Short Name</label>
				<input type="text" class="form-control form-control-sm" readonly value="<?php echo $_settings->info('short_name') ?>">
			</div>
			<div class="form-group">
				<label class="control-label">System Logo</label>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="System Logo" id="cimg" class="img-fluid img-thumbnail">
			</div>
			<div class="form-group">
				<label class="control-label">Cover Image</label>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image($_settings->info('cover')) ?>" alt="Cover" id="cimg2" class="img-fluid img-thumbnail bg-gradient-dark border-dark">
			</div>
		</div>
	</div>
</div>
<div class="col-lg-12 mt-3">
	<div class="card card-outline card-info rounded-0 shadow">
		<div class="card-header">
			<h5 class="card-title">About This System</h5>
		</div>
		<div class="card-body">
			<p>This is the <strong><?php echo $_settings->info('name') ?></strong> - a comprehensive lab management solution for diagnostic centers and pathological laboratories.</p>
			<p><strong>Features:</strong></p>
			<ul>
				<li>Appointment booking and management</li>
				<li>Test catalog and pricing</li>
				<li>Patient records management</li>
				<li>Test results and reports</li>
			</ul>
			<p class="text-muted"><small>Contact the administrator for any system changes or support.</small></p>
		</div>
	</div>
</div>
