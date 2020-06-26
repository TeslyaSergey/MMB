
@if($setting['form-method'] =='native')
<div class="box box-primary">
	<div class="box-header with-border">
			<div class="box-header-tools pull-right " >
				<a href="javascript:void(0)" class="collapse-close pull-right btn btn-xs btn-default" onclick="ajaxViewClose('#{{ $pageModule }}')"><i class="fa fa fa-times"></i></a>
			</div>
	</div>
	<div class="box-body">
@endif
			{!! Form::open(array('url'=>'expenses/save/'.\App\Library\SiteHelpers::encryptID($row['id']), 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ','id'=> 'expensesFormAjax')) !!}
			<div class="col-md-12">
						<fieldset><legend> {{ Lang::get('core.expenses') }}</legend>
				{!! Form::hidden('id', $row['id']) !!}
										<div class="form-group  ">
												<label for="category" class=" control-label col-md-4 text-left"> {{__('core.category')}} <span class="asterix"> * </span></label>
												<div class="col-md-4">
														<select name="category" rows="5" id="category"
																		class="select2" required=""
																		tabindex="-1" aria-hidden="true">
																<option value="">-- Please Select --</option>
																@foreach($expenseitems as $category)
																		<option {{$category->id==$row['category']?'selected':''}} value="{{$category->id}}">{{$category->name}}</option>
																@endforeach
														</select>
												</div>
										</div>
										<div class="form-group  ">
												<label for="category" class=" control-label col-md-4 text-left"> {{__('core.staffname')}} <span class="asterix"> * </span></label>
												<div class="col-md-4">
														<select name="staff" rows="5" id="staff"
																		class="select2" required=""
																		tabindex="-1" aria-hidden="true">
																<option value="">-- Please Select --</option>
																@foreach($staffs as $staff)
																		<option {{$staff->staffID==$row['staff']?'selected':''}} value="{{$staff->staffID}}">{{$staff->name}}</option>
																@endforeach
														</select>
												</div>
										</div>
										<div class="form-group  " >
										<label for="amount" class=" control-label col-md-4 text-left"> {{ Lang::get('core.amount') }} <span class="asterix"> * </span></label>
										<div class="col-md-3">
										  <input  type='text' name='amount' id='amount' value='{{ $row['amount'] }}'
						required     class='form-control ' />
										 </div>
										 	<div class="col-md-3">
											 	<select name='currency' rows='5' id='currency' class='select2 ' required  >
													<option value="">-- Please Select --</option>
													@foreach($currencies as $currency)
															<option {{$currency->currencyID==$row['currency']?'selected':''}} value="{{$currency->currencyID}}">{{$currency->currency_sym." ".$currency->symbol}}</option>
													@endforeach
												</select>
											</div>
									  </div>
										<div class="form-group  " >
										<label for="Payment Type" class=" control-label col-md-4 text-left"> {{ Lang::get('core.paymenttype') }}<span class="asterix"> * </span></label>
										<div class="col-md-3">
										  <select name='payment_type' rows='5' id='payment_type' class='select2 ' required  >
												<option value="">-- Please Select --</option>
												@foreach($paymenttypes as $paymenttype)
														<option {{$paymenttype->paymenttypeID==$row['payment_type']?'selected':''}} value="{{$paymenttype->paymenttypeID}}">{{$paymenttype->payment_type}}</option>
												@endforeach
											</select>
										 </div>
										 <div class="col-md-3">

										 </div>
									  </div>
										<div class="form-group  ">
						            <label for="Depart Date"
						                   class=" control-label col-md-4 text-left">{{__('core.date')}} <spanclass="asterix"> * </span></label>
						            <div class="col-md-4">
						                <div class="input-group m-b" style="width:150px !important;">
						                    <input class="form-control date" style="width:150px !important;" name="expense_date" type="text" value="{{$row['expense_date']}}">
						                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						                </div>
						            </div>
						        </div>
										<div class="form-group  status">
												<label for="Status"
															 class=" control-label col-md-4 text-left"> {{ __('core.received') }}</label>
															 <div class="col-md-6">
			 												  <?php $received = explode(",",$row['received']); ?>
			 							 <label class='checked checkbox-inline'>
			 							<input type='checkbox' name='received' value ='1'   class=''
			 							@if(in_array('1',$received))checked @endif
			 							 /></label>
			 												 </div>
			 												 <div class="col-md-2">

			 												 </div>
										</div>
										<div class="form-group  " >
										<label for="note" class=" control-label col-md-4 text-left"> {{Lang::get('core.notes')}} </label>
										<div class="col-md-6">
										  <textarea name='note' rows='5' id='note' class='form-control '
				           >{{ $row['note'] }}</textarea>
										 </div>
										 <div class="col-md-2">

										 </div>
									  </div>
										<div class="form-group  " >
										<label for="attached" class=" control-label col-md-4 text-left"> {{ Lang::get('core.attached') }} </label>
										<div class="col-md-6">
											<div class="attachedUpl">
											 	<input  type='file' name='attached' accept="application/pdf"/>
											</div>
										 </div>
										 <div class="col-md-2">

										 </div>
									  </div>
								</fieldset>
			</div>




			<div style="clear:both"></div>

			<div class="form-group">
				<label class="col-sm-4 text-right">&nbsp;</label>
				<div class="col-sm-8">
					<button type="submit" class="btn btn-success btn-sm ">  {{ Lang::get('core.sb_save') }} </button>
					<button type="button" onclick="ajaxViewClose('#{{ $pageModule }}')" class="btn btn-danger btn-sm">  {{ Lang::get('core.sb_cancel') }} </button>
				</div>
			</div>
			{!! Form::close() !!}


@if($setting['form-method'] =='native')
	</div>
</div>
@endif


</div>

<script type="text/javascript">
$(document).ready(function() {

	$('.editor').summernote();

	$('.tips').tooltip();
	$(".select2").select2({ width:"100%" , dropdownParent: $('#mmb-modal-content')});
		$('.date').datetimepicker({format: 'yyyy-mm-dd', autoclose:true , minView:2 , startView:2 , todayBtn:true });
	$('.datetime').datetimepicker({format: 'yyyy-mm-dd hh:ii:ss'});
	$('input[type="checkbox"],input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-red',
		radioClass: 'iradio_square-red',
	});
		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("expenses/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

	var form = $('#expensesFormAjax');
	form.parsley();
	form.submit(function(){

		if(form.parsley('isValid') == true){
			var options = {
				dataType:      'json',
				beforeSubmit :  showRequest,
				success:       showResponse
			}
			$(this).ajaxSubmit(options);
			return false;

		} else {
			return false;
		}

	});

});

function showRequest()
{
	$('.ajaxLoading').show();
}
function showResponse(data)  {

	if(data.status == 'success')
	{
		ajaxViewClose('#{{ $pageModule }}');
		ajaxFilter('#{{ $pageModule }}','{{ $pageUrl }}/data');
		notyMessage(data.message);
		$('#mmb-modal').modal('hide');
	} else {
		notyMessageError(data.message);
		$('.ajaxLoading').hide();
		return false;
	}
}

</script>
<script>
    $("input[name='amount']").TouchSpin();
</script>
