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
<?php if(count($result)>0) $result = $result->result_array()[0]; ?>
<div id="wrapper">
	<div class="main-content">
		<div class="row small-spacing">
			<div class="col-md-12 col-xs-12">
				<div class="box-content bordered default">
					<h4 class="box-title"><?php echo $heading; ?></h4>
					<div class="dropdown js__drop_down">
						<a class="btn btn-xs btn-default" href="<?php echo base_url('users/'.$_user_id.'/documents/add'); ?>" title="Add Customer Document"><i class="fa fa-plus">&nbsp;&nbsp;</i> Add Document</a>
					</div>
					
					<div class="row">
						<div class="col-sm-12">
                     <h4 class="table-heading">Documents To Validate</h4>
							<table class="details">
								<?php if(isset($result['documents_to_verify']) && !empty($result['documents_to_verify'])){ ?>
								<tr>
									<th>Document Type</th>
									<th>Created At</th>
									<th>First Page</th>
									<th class="text-center">Action</th>
								</tr>
								<?php foreach($result['documents_to_verify'] as $doctype => $doc){ ?>
								<tr>
									<td><?php echo ucwords(str_replace('_',' ',$doctype)); ?></td>
									<td><?php echo date('M d,Y h:m:s A', MongoEPOCH($doc['created_at'])); ?></td>
									<td><img src="<?php echo base_url(). 'uploads/'.(($doctype=='license')?$doc['front']:$doc['value']); ?>" class="img-responsive document-img"/></td>
									<td class="text-center">
                              <a class="btn btn-xs btn-success" href="<?php echo base_url().USERURL.'/'.$_user_id.'/documents/edit/'.$doctype.'?q=documents_to_verify'; ?>"><i class="fa fa-pencil"></i> Edit</a>
                              &nbsp;
                              <a class="btn btn-xs btn-default" href="<?php echo base_url().USERURL.'/'.$_user_id.'/documents/view/'.$doctype.'?q=documents_to_verify'; ?>"><i class="fa fa-eye"></i> View</a>
                           </td>
								</tr>
								<?php } ?>
								<?php } else { ?>
								<tr>
									<td colspan="3">No document to validate.</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</div>
					
					<div class="row margin-top-20">
						<div class="col-sm-12">
                     <h4 class="table-heading">Validated Documents</h4>
							<table class="details">
								<?php if($result['verified_documents']){ ?>
								<tr>
									<th>Document Type</th>
									<th>Created At</th>
									<th>Validated At</th>
									<th>Validated By</th>
									<th>First Page</th>
									<th class="text-center">Action</th>
								</tr>
								<?php foreach($result['verified_documents'] as $doctype => $doc){ ?>
								<tr>
									<td><?php echo ucwords(str_replace('_',' ',$doctype)); ?></td>
									<td><?php echo date('M d,Y h:m:s A', MongoEPOCH($doc['created_at'])); ?></td>
									<td><?php echo date('M d,Y h:m:s A', MongoEPOCH($doc['modified_at'])); ?></td>
									<td><?php echo isset($doc['verified_by'])?getrow(USERS, $doc['verified_by']):$siteTitle; ?></td>
									<td><img src="<?php echo base_url(). 'uploads/'.(($doctype=='license')?$doc['front']:$doc['value']); ?>" class="img-responsive document-img"/></td>
                           <td class="text-center">
                              <a class="btn btn-xs btn-success" href="<?php echo base_url().USERURL.'/'.$_user_id.'/documents/edit/'.$doctype.'?q=verified_documents'; ?>"><i class="fa fa-pencil"></i> Edit</a>
                              &nbsp;
                              <a class="btn btn-xs btn-default" href="<?php echo base_url().USERURL.'/'.$_user_id.'/documents/view/'.$doctype.'?q=verified_documents'; ?>"><i class="fa fa-eye"></i> View</a>
                           </td>
								</tr>
								<?php } ?>
								<?php } else { ?>
								<tr>
									<td>No document validated.</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</div>
					
					<div class="row margin-top-20">
						<div class="col-sm-12">
                     <h4 class="table-heading">Rejected Documents</h4>
							<table class="details">
								<?php if($result['rejected_documents']){ ?>
								<tr>
									<th>Document Type</th>
									<th>Created At</th>
									<th>Rejected At</th>
									<th>Rejected By</th>
									<th>First Page</th>
									<th class="text-center">Action</th>
								</tr>
								<?php foreach($result['rejected_documents'] as $doctype => $doc){ ?>
								<tr>
									<td><?php echo ucwords(str_replace('_',' ',$doctype)); ?></td>
									<td><?php echo date('M d,Y h:m:s A', MongoEPOCH($doc['created_at'])); ?></td>
									<td><?php echo date('M d,Y h:m:s A', MongoEPOCH($doc['modified_at'])); ?></td>
									<td><?php echo isset($doc['verified_by'])?getrow(USERS, $doc['verified_by']):$siteTitle; ?></td>
									<td><img src="<?php echo base_url(). 'uploads/'.(($doctype=='license')?$doc['front']:$doc['value']); ?>" class="img-responsive document-img"/></td>
                           <td class="text-center">
                              <a class="btn btn-xs btn-success" href="<?php echo base_url().USERURL.'/'.$_user_id.'/documents/edit/'.$doctype.'?q=rejected_documents'; ?>"><i class="fa fa-pencil"></i> Edit</a>
                              &nbsp;
                              <a class="btn btn-xs btn-default" href="<?php echo base_url().USERURL.'/'.$_user_id.'/documents/view/'.$doctype.'?q=rejected_documents'; ?>"><i class="fa fa-eye"></i> View</a>
                           </td>
								</tr>
								<?php } ?>
								<?php } else { ?>
								<tr>
									<td>No rejected document.</td>
								</tr>
								<?php } ?>
							</table>
						</div>
					</div>
					
				</div>
			</div>
		</div>
		<?php $this->load->view('users/templates/footer'); ?>
		<script src="<?php echo base_url('assets/js/support.js'); ?>"></script>
	</div>
</div>