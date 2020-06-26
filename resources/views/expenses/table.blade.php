<?php usort($tableGrid, "\App\Library\SiteHelpers::_sort"); ?> <div class="col-md-12">
<div class="box box-primary">
	<div class="box-header with-border">

		@include( 'mmb/toolbar')
	</div>
	<div class="box-body">



	 {!! (isset($search_map) ? $search_map : '') !!}

	 <?php echo Form::open(array('url'=>'expenses/delete/', 'class'=>'form-horizontal' ,'id' =>'MmbTable'  ,'data-parsley-validate'=>'' )) ;?>
<div class="table-responsive" style="min-height:300px; padding-bottom:60px; border: none !important">
	@if(count($rowData)>=1)
    <table class="table table-bordered table-striped " id="{{ $pageModule }}Table">
        <thead>
			<tr>
				<th width="20"> No </th>
				<th width="30"> <input type="checkbox" class="checkall" /></th>
				@if($setting['view-method']=='expand')
				<th width="30" style="width: 30px;">  </th>
				@endif
				<th width="50"><?php echo Lang::get('core.btn_action') ;?></th>
				<th width="70">{{Lang::get('core.staffname')}}</th>
				<th>{{Lang::get('core.amount')}}</th>
				<th width="90">{{Lang::get('core.paymenttype')}}</th>
				<th width="70">{{Lang::get('core.date')}}</th>
				<th>{{Lang::get('core.category')}}</th>
				<th>{{Lang::get('core.notes')}}</th>
				<th width="50">{{Lang::get('core.attached')}}</th>
				<th width="30">{{Lang::get('core.received')}}</th>
			  </tr>
        </thead>

        <tbody>
        	@if($access['is_add'] =='1' && $setting['inline']=='true')
			<tr id="form-0" >
				<td> # </td>
				<td> </td>
				@if($setting['view-method']=='expand') <td> </td> @endif
				<td >
					<button onclick="saved('form-0')" class="btn btn-success btn-xs" type="button"><i class="fa fa-play-circle"></i></button>
				</td>
				@foreach ($tableGrid as $t)
					@if($t['view'] =='1')
					<?php $limited = isset($t['limited']) ? $t['limited'] :''; ?>
						@if(\App\Library\SiteHelpers::filterColumn($limited ))
						<td data-form="{{ $t['field'] }}" data-form-type="{{ \App\Library\AjaxHelpers::inlineFormType($t['field'],$tableForm)}}">
							{!! \App\Library\SiteHelpers::transForm($t['field'] , $tableForm) !!}
						</td>
						@endif
					@endif
				@endforeach

			  </tr>
			  @endif

           		<?php $i = 0; foreach ($rowData as $row) :
           			  $id = $row->id;
           		?>
                <tr class="editable" id="form-{{ $row->id }}">
					<td class="number"> <?php echo ++$i;?>  </td>
					<td ><input type="checkbox" class="ids" name="ids[]" value="<?php echo $row->id ;?>" />  </td>
					@if($setting['view-method']=='expand')
					<td><a href="javascript:void(0)" class="expandable" rel="#row-{{ $row->id }}" data-url="{{ url('expenses/show/'.$id) }}"><i class="fa fa-plus " ></i></a></td>
					@endif
				 <td data-values="action" data-key="<?php echo $row->id ;?>"  >
					{!! \App\Library\AjaxHelpers::buttonAction('expenses',$access,$id ,$setting) !!}
					{!! \App\Library\AjaxHelpers::buttonActionInline($row->id,'id') !!}

				</td>
					 <?php foreach ($tableGrid as $field) :
					 	if($field['view'] =='1') :
							$value = \App\Library\SiteHelpers::formatRows($row->{$field['field']}, $field , $row);
						 	?>
						 	<?php $limited = isset($field['limited']) ? $field['limited'] :''; ?>
						 	@if(\App\Library\SiteHelpers::filterColumn($limited) && strtolower($field['field']) != 'currency')
								 <td align="<?php echo $field['align'];?>" data-values="{{ $row->{$field['field']} }}" data-field="{{ $field['field'] }}" data-format="{{ htmlentities($value) }}">
									 @if($field['field'] == 'category')
 										@foreach($expenseitems as $a)
 												<?php
 													if($row->{$field['field']} == $a->id){
 														echo "<span>".$a->name??''."</span>";
 													}
 												?>
 										@endforeach
									@elseif(strtolower($field['field']) == 'attached')
 											@if($row->{$field['field']} != '' && $row->{$field['field']} != Null)
 													<a href="{{asset('storage').'/files/'.($row->{$field['field']})}}" class="text-red" target="_blank"><i class="fa fa-file-pdf-o fa-2x"></i></a>
 											@endif
									@elseif(strtolower($field['field']) == 'amount')
											<span>{{number_format($row->amount, 2, ',', '.')}}
											@foreach($currencies as $a)
												<?php
													if($row->currency == $a->currencyID){
														echo "<span>".$a->symbol??''."</span>";
													}
												?>
											@endforeach
										</span>
									@elseif(strtolower($field['field']) == 'staff')
											@foreach($staffs as $a)
												<?php
													if($row->{$field['field']} == $a->staffID){
														echo "<span>".$a->name??''."</span>";
													}
												?>
											@endforeach
									@elseif(strtolower($field['field']) == 'expense_date')
											<span>{{date('d-m-Y', strtotime($row->{$field['field']}))}}</span>
									@elseif(strtolower($field['field']) == 'staff')
											@foreach($staffs as $a)
												<?php
													if($row->{$field['field']} == $a->staffID){
														echo "<span>".$a->name??''."</span>";
													}
												?>
											@endforeach
									@elseif(strtolower($field['field']) == 'payment_type')
											@foreach($paymenttypes as $a)
												<?php
													if($row->{$field['field']} == $a->paymenttypeID){
														echo "<span>".$a->payment_type??''."</span>";
													}
												?>
											@endforeach
 									@elseif(strtolower($field['field']) == 'status')
 											@if($row->{$field['field']} == '2')
 													<span class="label label-warning">{{ __('core.fr_pending') }}</span>
 											@elseif($row->{$field['field']} == '1')
 													<span class="label label-success">{{ __('core.confirmed') }}</span>
 											@elseif($row->{$field['field']} == '0')
 													<span class="label label-danger">{{ __('core.cancelled') }}</span>
 											@endif
									@elseif(strtolower($field['field']) == 'received')
 											<span>{!! $row->received == 1 ? '<i class="fa fa-fw fa-2x fa-thumbs-up text-green"></i>' : '<i class="fa fa-fw fa-2x fa-times-circle text-red"></i>'  !!}</span>
 									@else
 										 {!! $value !!}
 									@endif
								 </td>
							@endif
						 <?php endif;
						endforeach;
					  ?>
                </tr>
                @if($setting['view-method']=='expand')
                <tr style="display:none" class="expanded" id="row-{{ $row->id }}">
                	<td class="number"></td>
                	<td></td>
                	<td></td>
                	<td colspan="{{ $colspan}}" class="data"></td>
                	<td></td>
                </tr>
                @endif
            <?php endforeach;?>

        </tbody>

    </table>
	@else

	<div style="margin:100px 0; text-align:center;">

		<p> {{ Lang::get('core.norecord') }} </p>
	</div>

	@endif

	</div>
	<?php echo Form::close() ;?>
	</div>
</div>

	</div>	 	                  			<div style="clear: both;"></div>  	@if($setting['inline'] =='true') @include('mmb.module.utility.inlinegrid') @endif
<script>
$(document).ready(function() {
	$('.tips').tooltip();
	$('input[type="checkbox"],input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-red',
		radioClass: 'iradio_square-red',
	});
	$('#{{ $pageModule }}Table .checkall').on('ifChecked',function(){
		$('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('check');
	});
	$('#{{ $pageModule }}Table .checkall').on('ifUnchecked',function(){
		$('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('uncheck');
	});

	$('#{{ $pageModule }}Paginate .pagination li a').click(function() {
		var url = $(this).attr('href');
		reloadData('#{{ $pageModule }}',url);
		return false ;
	});

	<?php if($setting['view-method'] =='expand') :
			echo \App\Library\AjaxHelpers::htmlExpandGrid();
		endif;
	 ?>
	 $('#{{ $pageModule }}Table').DataTable({
		 "paging": true,
		 "lengthChange": false,
		 "searching": true,
		 "ordering": false,
		 "info": false,
		 "autoWidth": false
	 });
});
</script>
<style>
.table th { text-align: none !important;  }
.table th.right { text-align:right !important;}
.table th.center { text-align:center !important;}

</style>
