<?php $this->load->view('users/templates/header'); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugin/lightview/css/lightview/lightview.css">
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
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
               <?php 
                  if(isset($result['verified_documents']) && isset($result['verified_documents'][$document])){
                     $created_at = $result['verified_documents'][$document]['created_at'];
                     $modified_at = $result['verified_documents'][$document]['modified_at'];
                     $status = '<button class="btn btn-xs btn-success">Verified</button>';
                     if($document=='license'){
                        $imgArr = [$result['verified_documents'][$document]['front'], $result['verified_documents'][$document]['back']];
                     } else {
                        $imgArr = [$result['verified_documents'][$document]['value']];
                     }
                  } else if(isset($result['rejected_documents']) && isset($result['rejected_documents'][$document])){ 
                     $created_at = $result['rejected_documents'][$document]['created_at'];
                     $modified_at = $result['rejected_documents'][$document]['modified_at'];
                     $status = '<button class="btn btn-xs btn-danger">Rejected</button>';
                     if($document=='license'){
                        $imgArr = [$result['rejected_documents'][$document]['front'], $result['rejected_documents'][$document]['back']];
                     } else {
                        $imgArr = [$result['rejected_documents'][$document]['value']];
                     }
                  } else {
                     $created_at = $result['documents_to_verify'][$document]['created_at'];
                     $modified_at = $result['documents_to_verify'][$document]['modified_at'];
                     $status = '<button class="btn btn-xs btn-warning">Not Verified</button>';
                     if($document=='license'){
                        $imgArr = [$result['documents_to_verify'][$document]['front'], $result['documents_to_verify'][$document]['back']];
                     } else {
                        $imgArr = [$result['documents_to_verify'][$document]['value']];
                     }
                  }
                  $tr = "<tr><th>Status</th><td>:</td><td>".$status."</td></tr>";
                  $tr .= "<tr><th>Created At</th><td>:</td><td>".date('m/d/Y h:i:s A', MongoEPOCH($created_at))."</td></tr>";
                  $tr .= "<tr><th>Modified At</th><td>:</td><td>".date('m/d/Y h:i:s A', MongoEPOCH($modified_at))."</td></tr>";
                  $tr .= "<tr><th>Validated By</th><td>:</td><td>".(isset($doc['verified_by'])?getrow(USERS, $doc['verified_by']):$siteTitle)."</td></tr>";
               ?>   
					<h4 class="box-title">
                  <?php echo $heading; ?>
                  <div class="dropdown js__drop_down">
                     <a class="btn btn-xs btn-default" href="<?php echo base_url('users/'.$_user_id.'/documents/edit/'.$document); ?>" title="Add Customer Document"><i class="fa fa-pencil">&nbsp;&nbsp;</i> Edit</a>
                  </div>
					</h4>
               
					<div class="row">
						<div class="col-sm-12">
							<table class="details">
								<tr class="table-title">
									<td colspan="2">Documents To Validate</td>
								</tr>
								<tr>
                           <td style="width: 35%;border-right: 1px solid #e5e5e5;">
                              <table class="inner-table">
                                 <?php echo $tr; ?>
                              </table>
                           </td>
									<td>
                              <?php $totimg = count($imgArr); $i=1; ?>
                              <?php if($totimg > 0){ ?>
                              <?php foreach($imgArr as $img){ ?>
                              <div class="row col-sm-12 form-group text-center">
                                 <div class="js__isotope_item massage beauty spa" data-lightview-group="group">
                                    <a href="<?php echo base_url().'uploads/'.$img; ?>" class="item-gallery lightview" data-lightview-group="group">
                                       <img class="gallery-grid" src="<?php echo base_url().'uploads/'.$img; ?>" alt="<?php echo $result['name']; ?>">
                                       <h2 class="title">View</h2>
                                    </a>
                                 </div>
                              </div>
                              <?php } ?>
                              <?php } else { ?>
                              <p><i>No documents found.</i></p>
                              <?php } ?>
                           </td>
								</tr>
							</table>
						</div>
					</div>
               
               <div class="row">
                  <div class="col-sm-12">
                     <a href="<?php echo base_url().USERURL.'/'.$_user_id.'/documents'; ?>" class="btn btn-xs btn-default"><i class="fa fa-mail-reply">&nbsp;&nbsp;</i>Back</a>
                  </div>
               </div>
					
				</div>
			</div>
		</div>
		<?php $this->load->view('users/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
		<script src="<?php echo base_url(); ?>assets/plugin/lightview/js/lightview/lightview.js"></script>
	</div>
</div>