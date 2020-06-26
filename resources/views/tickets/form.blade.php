@if($setting['form-method'] =='native')
<div class="box box-primary">
	<div class="box-header with-border">
			<div class="box-header-tools pull-right " >
				<a href="javascript:void(0)" class="collapse-close pull-right btn btn-xs btn-default" onclick="ajaxViewClose('#{{ $pageModule }}')"><i class="fa fa fa-times"></i></a>
			</div>
	</div>

	<div class="box-body">
@endif
{!! Form::open(array('url'=>'tickets/save/'.\App\Library\SiteHelpers::encryptID($row['ticketID']), 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ','id'=> 'ticketsFormAjax')) !!}
    <div class="col-md-12">
			{!! Form::hidden('ticketID', $row['ticketID']) !!}
        <fieldset>
            <legend>  {{ Lang::get('core.tickets') }}</legend>
        <div class="form-group  ">
            <label for="Ticket" class=" control-label col-md-4 text-left">{{__('core.tickets')}} <span class="asterix"> * </span></label>
            <div class="col-md-6">
                <select name="airlinesID[]"  rows="5" id="editAirlinesID"
                        class="select2   parsley-validated" required=""
                        tabindex="-1" aria-hidden="true">
                    @php
                        $airlineIds = json_decode($row['airlinesID']);
                    @endphp
                    @foreach($airlines as $airline)
                        <option {{ $airlineIds?(in_array($airline->airlineID,$airlineIds)?'selected':''):''}} value="{{$airline->airlineID}}">{{$airline->airline}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group  return">
            <label for="Round Trip"
                   class=" control-label col-md-4 text-left"> {{__('core.roundtrip')}}<span
                        class="asterix"> * </span></label>
            <div class="col-md-7">
                <label class='radio radio-inline'>
                    <input type='radio' name='returnn' value='1' required
                           @if($row['returnn'] == '1') checked="checked" @endif > {{__('core.yes')}}
                </label>
                <label class='radio radio-inline'>
                    <input type='radio' name='returnn' value='0' required
                           @if($row['returnn'] == '0') checked="checked" @endif > {{__('core.no')}}
                </label><br>
            </div>
        </div>
        <div class="form-group  ">
            <label for="Class" class=" control-label col-md-4 text-left"> {{__('core.class')}} <span
                        class="asterix"> * </span></label>
            <div class="col-md-8">

                <label class='radio radio-inline'>
                    <input type='radio' name='class' value='1' required
                           @if($row['class'] == '1') checked="checked" @endif > {{__('core.economy')}}
                </label><br>
                <label class='radio radio-inline'>
                    <input type='radio' name='class' value='2' required
                           @if($row['class'] == '2') checked="checked" @endif > {{__('core.premiumeconomy')}}
                </label><br>
                <label class='radio radio-inline'>
                    <input type='radio' name='class' value='3' required
                           @if($row['class'] == '3') checked="checked" @endif > {{__('core.business')}}
                </label><br>
                <label class='radio radio-inline'>
                    <input type='radio' name='class' value='4' required
                           @if($row['class'] == '4') checked="checked" @endif > {{__('core.first')}}
                </label>
            </div>
        </div>
        <div class="form-group  ">
            <label for="Departure Airport" class=" control-label col-md-4 text-left"> {{__('core.departureairport')}} <span class="asterix"> * </span></label>
            <div class="col-md-7">
                <select name="depairportID" rows="5" id="depairportID"
                        class="select2" required=""
                        tabindex="-1" aria-hidden="true">
                    <option value="">-- Please Select --</option>
                    @foreach($airports as $airport)
                        <option {{$airport->airportID==$row['depairportID']?'selected':''}} value="{{$airport->airportID}}">{{$airport->airport_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group  ">
            <label for="Depart Date"
                   class=" control-label col-md-4 text-left">{{__('core.departure')}} <spanclass="asterix"> * </span></label>
            <div class="col-md-4">
                <div class="input-group m-b" style="width:150px !important;">
                    <input class="form-control datetime" style="width:150px !important;" name="departing" type="text" value="{{$row['departing']}}">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" name="arrFlightNO" id="arrFlightNO"  required=""
                       class="form-control  parsley-validated" placeholder="Flight No" value="{{$row['arrFlightNO']}}">
            </div>
        </div>
        <div class="form-group  ">
            <label for="Arrival Airport" class=" control-label col-md-4 text-left"> Arrival Airport
                <span class="asterix"> * </span></label>
            <div class="col-md-7">
                <select name="arrairportID" rows="5" id="arrairportID"
                        class="select2" required=""
                        tabindex="-1" aria-hidden="true">
                    <option value="">-- Please Select --</option>
                    @foreach($airports as $airport)
                        <option {{$airport->airportID==$row['arrairportID']?'selected':''}} value="{{$airport->airportID}}">{{$airport->airport_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group  returndate" style="display: block;">
            <label for="Return Date"
                   class=" control-label col-md-4 text-left"> {{__('core.return')}} </label>
            <div class="col-md-4">
                <div class="input-group m-b" style="width:150px !important;">
                    <input class="form-control datetime" style="width:150px !important;"
                           name="returning" type="text" value="{{$row['returning']}}">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="col-md-3">
                <input type="text" name="depFlightNO" id="depFlightNO" value="{{$row['depFlightNO']}}" required=""
                       class="form-control  parsley-validated" placeholder="Flight No">
            </div>
        </div>

        <div class="form-group ">
            <label for="price"
                   class=" control-label col-md-4 text-left"> {{__('core.price')}} </label>
            <div class="col-md-7">
                <input type="number" name="price" id="price" value="{{$row['price']}}" required="" class="form-control  parsley-validated" placeholder="{{__('core.price')}}">
            </div>
        </div>

        <div class="form-group ">
            <label for="seats"
                   class=" control-label col-md-4 text-left"> {{__('core.seats')}} </label>
            <div class="col-md-7">
                <input class="form-control" name="seats" type="number" value="{{$row['seats']}}">
            </div>

        </div>
        <div class="form-group ">
            <label for="available_seats"
                   class=" control-label col-md-4 text-left"> {{__('core.seatsavailable')}} </label>
            <div class="col-md-7">
                <input type="number" name="available_seats" id="available_seats" value="{{$row['available_seats']}}"
                       class="form-control  parsley-validated">
            </div>

        </div>


        <div class="form-group  status">
            <label for="Status"
                   class=" control-label col-md-4 text-left"> {{ __('core.status') }}</label>
            <div class="col-md-8">
                <label class='radio radio-inline'>
                    <input type='radio' name='status' value='2' required
                           @if($airline['status'] == '2') checked="checked" @endif > {{ __('core.fr_pending') }}
                </label>
                <label class='radio radio-inline'>
                    <input type='radio' name='status' value='1' required
                           @if($airline['status'] == '1') checked="checked" @endif > {{ __('core.confirmed') }}
                </label>
                <label class='radio radio-inline'>
                    <input type='radio' name='status' value='0' required
                           @if($airline['status'] == '0') checked="checked" @endif > {{ __('core.cancelled') }}
                </label>
            </div>
        </div>
            </fieldset>
    </div>
    <div style="clear:both"></div>

    <div class="form-group">
        <label class="col-sm-4 text-right">&nbsp;</label>
        <div class="col-sm-8">
            <button type="submit" id="storeBtn"
                    class="btn btn-success btn-sm "> {{__('core.sb_save') }} </button>
            <button type="button" data-dismiss="modal"
                    class="btn btn-danger btn-sm"> {{ __('core.sb_cancel') }}
            </button>
        </div>
    </div>
</form>

<!-- <script>

</script> -->
<script type="text/javascript">
$(document).ready(function() {
  $('#editAirlinesID').select2();
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
			var removeUrl = '{{ url("tickets/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

	var form = $('#ticketsFormAjax');
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
