<?php $this->load->view('users/templates/header'); ?>
<style>
	p.detail{
	color: #26556e;
	margin:0;
	}
	table.details{
		width: 100%;
	}
	table.details th, td{
		border: 1px solid #e5e5e5;
		padding: 5px 10px;
	}
	table.details tr.table-title td{
		font-size: 20px;
		border: none;
		border-bottom: 2px solid #000;
	}
	tr.current{
		background: #eeffe9;
	}
   tr.expired {
      background: #fff;
   }
	.document-img{
		width: 75px;
		height: 75px;
		object-fit: contain;
	}
   h4.table-heading{
      border-radius: 3px 3px 0 0;
      background: #e5e5e5;
      margin: 0;
      padding: 10px;
   }
</style>
<?php if(count($result)>0) $result = $result->result_array(); ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					
					<table id="datatable" class="table table-striped table-bordered display" style="width:100%">
						<thead>
							<tr>
								<th>Offer</th>
                        <th>Subscription Date</th>
                        <th>Subscription Holder</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Required Docs Status</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Offer</th>
                        <th>Subscription Date</th>
                        <th>Subscription Holder</th>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Required Docs Status</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<?php $this->load->view('users/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
      <script>				
         
		$(document).ready(function(){
			
			var dataTable = $('#datatable').DataTable({
				"pageLength": 10,
				"lengthMenu": [
					[ 10, 25, 100, -1 ],
					[ '10', '25', '100', 'All' ]
				],
				"order": [[ 1, "desc" ]],
				"columnDefs": [
					{ "orderable": false, "targets": [2, -1] }
				],
				"processing": true,
				"serverSide": true,
				"ajax":{
					url : "<?php echo base_url().USERURL.'/'.$_user_id.'/subscription/get'; ?>",
					type: "post",
					error: function(){
						$(".datatable-error").html("");
						$("#datatable").append('<tbody class="datatable-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
						$("#datatable_processing").css("display","none");
					}
				}
			});

		});
      
		function change_status(id, status){
			posturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/subscription/status'; ?>";
			redirecturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/subscription'; ?>";
			update_status(id, status, posturl, redirecturl);
		}
      
      function delete_record(id){
         posturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/subscription/remove'; ?>";
			redirecturl = "<?php echo base_url().USERURL.'/'.$_user_id.'/subscription'; ?>";
         remove_draft(id, posturl, redirecturl);
      }
      </script>
	</div>
</div>