<?php $this->load->view('station/templates/header'); ?>
<style>
	p.detail{
	color: #26556e;
	margin:0;
	}
	table.details, table.inner-table{
		width: 100%;
	}
	table.details th, td{
		border-top: 1px solid #e5e5e5;
		padding: 5px;
	}table.inner-table th, td{
		border: none;
   }
	table.details th{
		background: #e5e5e5;
	}
   table.inner-table th{
		background: #fff;
   }
	table.details tr.table-title td{
		font-size: 20px;
		border: none;
		border-bottom: 2px solid #000;
	}
	.document-img{
		width: 75px;
		height: 75px;
		object-fit: contain;
	}
   .gallery-grid{
      width: auto;
      height: 200px;
   }
   .item-gallery img{
      min-width: auto;
   }
   .js__isotope_item{
      float: none;
      margin: 0;
   }
   .item-gallery{
      display: inline-block;
   }
</style>
<?php if(count($result)>0) $result = $result[0]; ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
               
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Title</label>
                     <p class="detail"><?php echo $result['name']; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Parking Order</label>
                     <p class="detail"><?php echo '# '.sprintf("%02d", $result['order']); ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Parking Type</label>
                     <p class="detail"><?php echo $parking_type[$result['parking_type']]; ?></p>
                  </div>
               </div>
					
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Light Color</label>
                     <p class="detail"><?php echo $light_color[$result['light_color']]; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Cable Status</label>
                     <p class="detail"><?php echo $cable_status[$result['cable_status']]; ?></p>
                  </div>
                  
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Parking Status</label>
                     <p class="detail"><?php echo $parking_status[$result['parking_status']]; ?></p>
                  </div>
               </div>
               
               <?php if($result['parking_type']=='charging_point'){ ?>
               <div class="row">
                  <div class="col-sm-4 form-group">
                     <label class="control-label">Vehicle</label>
                     <p class="detail"><?php $vehicle = getrow(VEHICLE, $result['vehicle_id']); echo $vehicle->license_plate.' / '.$vehicle->vin_number; ?></p>
                  </div>
               </div>
               <?php } ?>
               
               <div class="row">
                  <div class="col-sm-12">
                     <a href="<?php echo base_url().STATIONURL.'/'.$_station_id.'/parking'; ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
                  </div>
               </div>
					
				</div>
			</div>
		</div>
		<?php $this->load->view('station/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
	</div>
</div>