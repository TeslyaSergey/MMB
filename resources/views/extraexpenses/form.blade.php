
@if($setting['form-method'] =='native')
<div class="box box-primary">
	<div class="box-header with-border">
			<div class="box-header-tools pull-right " >
				<a href="javascript:void(0)" class="collapse-close pull-right btn btn-xs btn-default" onclick="ajaxViewClose('#{{ $pageModule }}')"><i class="fa fa fa-times"></i></a>
			</div>
	</div>

	<div class="box-body">
@endif
			{!! Form::open(array('url'=>'extraexpenses/save/'.\App\Library\SiteHelpers::encryptID($row['expenseID']), 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ','id'=> 'extraexpensesFormAjax')) !!}
			<div class="col-md-12">
						<fieldset><legend> {{ Lang::get('core.extraexpenses') }}</legend>
				{!! Form::hidden('expenseID', $row['expenseID']) !!}
										<div class="form-group">
											<label for="Formula" class=" control-label col-md-4 text-left"> For </label>
											<div class="col-md-6">
												<label class='radio radio-inline'>
												<input type='radio' name='formula' class="formula" id="formula" value ='0' required @if($row['formula'] == '0') checked="checked" @endif > {{ Lang::get('core.simple') }} </label>
												<label class='radio radio-inline'>
												<input type='radio' name='formula' class="formula" id="formula" value ='1' required @if($row['formula'] == '1') checked="checked" @endif > {{ Lang::get('core.package') }} </label>
											</div>
											<div class="col-md-2">
											</div>
										</div>

									  
									  <div class="" id="tour_part" style="display:none;">

										</div>
										
										<div class="" id="package_part" style="display:none;">

											<div class="form-group  " >
											<label for="TourcategoriesID" class=" control-label col-md-4 text-left">{{ Lang::get('core.packagecategory') }}</label>
											<div class="col-md-4">
												<select name='tourcategoriesID' rows='5' id='packagecategoriesID' class='select2'>
												</select>
											 </div>
											 <div class="col-md-2">
											 </div>
											</div>
											<div class="form-group  " >
											<label for="TourID" class=" control-label col-md-4 text-left">{{ Lang::get('core.package') }}</label>
											<div class="col-md-4">
												<select name='packageID' rows='5' id='packageID'  class='select2'>
												</select>
											 </div>
											</div>
											
										</div>
										<div class="form-group  " >
													<label for="Extra Expenses" class=" control-label col-md-4 text-left"> {{ Lang::get('core.extraexpense') }} <span class="asterix"> * </span></label>
													<div class="col-md-6">
													<input  type='text' name='extra_expenses' id='extra_expenses' value='{{ $row['extra_expenses'] }}' required     class='form-control ' />
													</div>
													<div class="col-md-2">

													</div>
												</div>
												<div class="form-group  " >
													<label for="Currency" class=" control-label col-md-4 text-left"> {{ Lang::get('core.staffname') }} <span class="asterix"> * </span></label>
													<div class="col-md-3">
													<select name="staff" rows="5" id="staff"
																		class="select2" required=""
																		tabindex="-1" aria-hidden="true">
																<option value="">-- Please Select --</option>
																@foreach($staffs as $staff)
																		<option {{$staff->staffID==$row['staff']?'selected':''}} value="{{$staff->staffID}}">{{$staff->name}}</option>
																@endforeach
														</select>
													</div>
													<div class="col-md-2">
			
													</div>
												</div>

												<div class="form-group  " >
													<label for="Cost" class=" control-label col-md-4 text-left"> {{ Lang::get('core.amount') }} <span class="asterix"> * </span></label>
													<div class="col-md-3">
													<input  type='text' name='cost' id='cost' value='{{ $row['cost'] }}' required class='form-control ' />
													</div>
													<div class="col-md-3">
													<select name='currencyID' rows='5' id='currencyID' class='select2 ' required  ></select>
													</div>
												</div>

												<div class="form-group  " >
													<label for="Currency" class=" control-label col-md-4 text-left"> {{ Lang::get('core.paymenttype') }} <span class="asterix"> * </span></label>
													<div class="col-md-3">
													<select name='paymenttypeID' rows='5' id='payment_type' class='select2 ' required  ></select>
													</div>
													<div class="col-md-2">
			
													</div>
												</div>
												<div class="form-group" >
													<label for="Start Date" class="control-label col-md-4 text-left"> {{ Lang::get('core.date') }} <span class="asterix"> * </span></label>
														<div class="col-md-2">
															<div class="input-group" style="width:150px !important;" id="dpd1">
															{!! Form::text('data', $row['data'], array('class'=>'form-control date', 'required'=>'required', 'autocomplete'=>'off')) !!}
															<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
															</div>
														</div>
												</div>

												<div class="form-group" >
													<label for="Remarks" class=" control-label col-md-4 text-left"> {{ Lang::get('core.notes') }}*</label>
													<div class="col-md-6">
													<textarea name='remarks' rows='5' id='remarks' class='form-control' required>{{ $row['remarks'] }}</textarea>
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

		$("#currencyID").jCombo("{!! url('extraexpenses/comboselect?filter=def_currency:currencyID:currency_sym|symbol&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["currencyID"] }}' });
	@if($row['formula']==1)
	$("#tour_part").hide();
	$("#package_part").show();
	@else
		$("#tour_part").show();
		$("#package_part").hide();
	@endif

	$("#packagecategoriesID").jCombo("{!! url('booktour/comboselect?filter=def_tour_categories:tourcategoriesID:tourcategoryname&limit=WHERE:status:=:1') !!}",
	{  selected_value : '{{ $row["tourcategoriesID"] }}' });
	$("#payment_type").jCombo("{!! url('tours/comboselect?filter=def_payment_types:paymenttypeID:payment_type&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["paymenttypeID"] }}' });
	$("#packageID").jCombo("{!! url('booktour/comboselect?filter=packages:packageID:package_name&limit=WHERE:status:=:1') !!}&parent=tourcategoriesID:",
		{  parent: '#packagecategoriesID', selected_value : '{{ $row["packageID"] }}' });
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
			var removeUrl = '{{ url("extraexpenses/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

	var form = $('#extraexpensesFormAjax');
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
$(".formula").on("ifChecked", function(){
	if($(this).val()==1){
		$("#tour_part").hide();
		$("#package_part").show();

	}else{
		$("#package_part").hide();
			$("#tour_part").show();
		}
	});
</script>
<script>
    $("input[name='cost']").TouchSpin();
</script>
