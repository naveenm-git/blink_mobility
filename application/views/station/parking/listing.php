<?php $this->load->view('station/templates/header'); ?>
<style>
	.light-color{
      width: 100%;
      padding: 5px 10px;
      margin: 0;
      color: #fff;
      font-weight: 500;
   }
   .text-success, .text-danger{
      font-weight: 500;
   }
</style>
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<div class="dropdown js__drop_down">
						<a class="btn btn-xs btn-default" href="<?php echo base_url(STATIONURL.'/'.$_station_id.'/parking/add'); ?>" title="Add New Parking"><i class="fa fa-plus">&nbsp;&nbsp;</i> Add Parking</a>
					</div>
					
               <div class="filter-panel-cover">
                  <button class="filter-accordion">Filter Data &nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                  <div class="filter-panel">
                        <div class="row">
                           <div class="form-group">
                              <div class="col-sm-3">
                                 <label for="parking_type">Type</label>
                                 <select id="parking_type" class="form-control filters">
                                    <option value="">Select Parking Type</option>
                                    <?php foreach($parking_type as $key => $value){ ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-3">
                                 <label for="light_color">Light Color</label>
                                 <select id="light_color" class="form-control filters">
                                    <option value="">Select light color</option>
                                    <?php foreach($light_color as $key => $value){ ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-3">
                                 <label for="cable_status">Cable Status</label>
                                 <select id="cable_status" class="form-control filters">
                                    <option value="">Select cable status</option>
                                    <?php foreach($cable_status as $key => $value){ ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-3">
                                 <label for="parking_status">Parking Status</label>
                                 <select id="parking_status" class="form-control filters">
                                    <option value="">Select parking status</option>
                                    <?php foreach($parking_status as $key => $value){ ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <div class="col-sm-2">
                                 <label>&nbsp;</label>
                                 <p class="form-control" id="clear-filter">Show All</p>
                              </div>
                           </div>
                        </div>
                  </div>
               </div>
               
					<table id="datatable" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th>Title</th>
								<th>Type</th>
								<th>Order</th>
								<th>Light Color</th>
								<th>Cable Status</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Title</th>
								<th>Type</th>
								<th>Order</th>
								<th>Light Color</th>
								<th>Cable Status</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</tfoot>
					</table>
					
				</div>
			</div>
		</div>
		<?php $this->load->view('station/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
      <script>
         $(document).ready(function(){
            
            var dataTable = $('#datatable').DataTable({
               "pageLength": 10,
               "lengthMenu": [
                  [ 10, 25, 100, -1 ], [ '10', '25', '100', 'All' ]
               ],
               "order": [[ 2, "asc" ]],
               "columnDefs": [
                  { "orderable": false, "targets": [1, 3, 4, 5, 6] }
               ],
               "processing": true,
               "serverSide": true,
               "ajax":{
                  url : "<?php echo base_url().STATIONURL.'/'.$_station_id.'/parking/get'; ?>",
                  type: "post",
                  error: function(){
                     $(".datatable-error").html("");
                     $("#datatable").append('<tbody class="datatable-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
                     $("#datatable_processing").css("display","none");
                  }
               }
            });
            
            $('#clear-filter').click(function(){
               $('#parking_type, #light_color, #cable_status, #parking_status').val('');
               dataTable.column(1).search($(this).val());
               dataTable.column(3).search($(this).val());
               dataTable.column(4).search($(this).val());
               dataTable.column(5).search($(this).val());
               dataTable.draw();
            });
            
            dataTable.columns(1).each(function ( colIdx ){
               $('#parking_type').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            
            dataTable.columns(3).each(function ( colIdx ){
               $('#light_color').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            
            dataTable.columns(4).each(function ( colIdx ){
               $('#cable_status').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
            
            dataTable.columns(5).each(function ( colIdx ){
               $('#parking_status').on('change', function () {
                  dataTable .column(colIdx) .search($(this).val()) .draw();
               });
            });
         });
         
         function change_status(id, status){
            posturl = "<?php echo base_url().'admin/parking/change_status'; ?>";
            redirecturl = "<?php echo base_url().'admin/parking-list'; ?>";
            update_status(id, status, posturl, redirecturl);
         }
         
         function delete_record(id){
            posturl = "<?php echo base_url().'admin/parking/remove'; ?>";
            redirecturl = "<?php echo base_url().'admin/parking-list'; ?>";
            remove_draft(id, posturl, redirecturl);
         }
      </script>
	</div>
</div>