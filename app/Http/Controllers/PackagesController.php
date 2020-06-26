<?php namespace App\Http\Controllers;

use App\Http\Controllers\controller;
use App\Models\Package;
use App\Models\Tours;
use App\Models\Tourfeature;
use App\Models\Roomfeature;
use App\Models\Roomtypes;
use App\Models\Ticket;
use App\Models\Airlines;
use App\Models\Airports;
use App\Models\Countries;
use App\Models\Cities;
use App\Models\Vehicletypes;
use App\Models\Hotels;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect ;
// use SiteHelpers;
use App\Library\FormHelpers;
use App\Library\SiteHelpers;
use App\Library\GeneralStatuss;
use App\Library\InvoiceStatus;
use Carbon\Carbon;



class PackagesController extends Controller {

	protected $layout = "layouts.main";
	protected $data = array();
	public $module = 'packages';
	static $per_page	= '10';
	public function __construct()
	{
		$this->model = new Package();
		$this->info = $this->model->makeInfo( $this->module);
		// var_dump($this->info);exit;

		$this->access = $this->model->validAccess($this->info['id']);
		$this->data = array(
			'pageTitle'	=> 	$this->info['title'],
			'pageNote'	=>  $this->info['note'],
			'pageModule'=> 'packages',
			'pageUrl'	=>  url('packages'),
			'return'	=> self::returnUrl()
		);
		\App::setLocale(CNF_LANG);
		if (defined('CNF_MULTILANG') && CNF_MULTILANG == '1') {

		$lang = (\Session::get('lang') != "" ? \Session::get('lang') : CNF_LANG);
		\App::setLocale($lang);
		}
	}

	public function getIndex( Request $request )
	{
		if($this->access['is_view'] ==0)
			return Redirect::to('dashboard')
				->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');

		$sort = (!is_null($request->input('sort')) ? $request->input('sort') : 'packageID');
		$order = (!is_null($request->input('order')) ? $request->input('order') : 'asc');
		// End Filter sort and order for query
		// Filter Search for query
		$filter = '';
		if(!is_null($request->input('search')))
		{
			$search = 	$this->buildSearch('maps');
			$filter = $search['param'];
			$this->data['search_map'] = $search['maps'];
		}
    $today = date("Y-m-d");
    $running_tours = \DB::table('packages')
            ->where('start','<=',$today)
            ->where('end','>=',$today)
            ->where('status',1)
            ->count();
    $upcoming_tours = \DB::table('packages')
          ->where('start','>',$today)
            ->where('status',1)
            ->count();
    $old_tours = \DB::table('packages')
            ->where('end','<',$today)
            ->where('status',1)
            ->count();
    $cancelled_tours = \DB::table('packages')
            ->where('status',2)
            ->count();
		$page = $request->input('page', 1);
		$params = array(
			'page'		=> $page ,
			// 'limit'		=> (!is_null($request->input('rows')) ? filter_var($request->input('rows'),FILTER_VALIDATE_INT) : static::$per_page ) ,
			'limit'		=> 50 ,
			'sort'		=> $sort ,
			'order'		=> $order,
			'params'	=> $filter,
			'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
		);
		$results = $this->model->getRows( $params );

		$page = $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false ? $page : 1;
		$pagination = new Paginator($results['rows'], $results['total'], $params['limit']);
		$pagination->setPath('packages');
        $this->data['running_tours']        = $running_tours;
		$this->data['upcoming_tours']       = $upcoming_tours;
		$this->data['old_tours']            = $old_tours;
		$this->data['cancelled_tours']      = $cancelled_tours;
		$this->data['today']                 = $today;
		$this->data['tours']                 = Tours::all();
		$this->data['rowData']		= $results['rows'];
		$this->data['pagination']	= $pagination;
		$this->data['pager'] 		= $this->injectPaginate();

		$this->data['i'] = ($page * $params['limit'])- $params['limit'];
		$this->data['tableGrid'] 	= $this->info['config']['grid'];
		$this->data['tableForm'] 	= $this->info['config']['forms'];
		$this->data['colspan'] 		= \App\Library\SiteHelpers::viewColSpan($this->info['config']['grid']);
		$this->data['access']		= $this->access;
		$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
		$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());

		return view('packages.index',$this->data);
	}

	function getUpdate(Request $request, $id = null)
	{
		if($id =='')
		{
			if($this->access['is_add'] ==0 )
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
		}
		if($id !='')
		{
			if($this->access['is_edit'] ==0 )
			return Redirect::to('dashboard')->with('messagetext',\Lang::get('core.note_restric'))->with('msgstatus','error');
		}
		$row = $this->model->find($id);
		if($row)
		{
			$this->data['row'] =  $row;
			$this->data['roomfeatures'] = Roomfeature::where("packageID", $id)->get();
		} else {
			$this->data['row'] = $this->model->getColumnTable('packages');
			$this->data['roomfeatures'] = Roomfeature::where("packageID", $id)->get();
			$this->data['row']['cost_count'] = 1;
		}
		$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['forms']);
		$this->data['tickets'] = Ticket::all();
		// $this->data['rowData']	= Ticket::all();
		$current_date	= date('Y-m-d H:i:s');
		// var_dump($current_date);
		$this->data['rowData']= \DB::table('tickets')
		->where('returning', '>', $current_date)
		->orWhere('returning', '=', NULL)
		->get();
		$this->data['roomTypes'] = Roomtypes::all();
		$this->data['airlines'] = Airlines::all();
		$this->data['airports'] = Airports::all();
		$this->data['parts'] = Tourfeature::where("packageID", $id)->get();
		$this->data['id'] = $id;
		return view('packages.form',$this->data);
	}

	public function getShow( Request $request, $id = null)
	{
		// $room_feature = \DB::insert('insert into room_features (roomtypeID, cost,seat,cost_count,packageID) values (1, 2,4,5,7)');
		// exit;
		if($this->access['is_detail'] ==0)
		return Redirect::to('dashboard')
			->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');

		$row = $this->model->getRow($id);
		if($row == NULL){
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.norecord'))->with('msgstatus','error');
		}
		$roomfeatures = Roomfeature::where('packageID', $id)->get();
		$cost_price = 0;
		foreach ($roomfeatures as $roomfeature) {
			$cost_price += $roomfeature->cost * $roomfeature->seat;
		}
		$cost_sum = 0;
		$cost = $row->cost;
		$currencyID = $row->currencyID;
		$cost = explode(",",$cost);
		// var_dump($cost);exit;
		for($i=0;$i<count($cost);$i++){

			$cost_sum += (int)$cost[$i];
		}
		// var_dump($cost_sum);
		$capacity = $row->total_capacity;
		$turnover = (int)$cost_sum * (int)$capacity;
		$earning = $turnover - (int)$cost_sum;
		if($row)
		{
			$this->data['currency'] = \DB::table('def_currency')
            ->where('currencyID', $currencyID)
			->value('symbol');
			$this->data['row'] =  $row;
			$this->data['fields'] 		=  \App\Library\SiteHelpers::fieldLang($this->info['config']['grid']);
			$this->data['id'] = $id;
			$this->data['turnover'] = $turnover;
			$this->data['earning'] = $earning;
			$this->data['cost_price'] = $cost_price;
			$this->data['access']		= $this->access;
			$this->data['subgrid']	= (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
			$this->data['fields'] =  \App\Library\AjaxHelpers::fieldLang($this->info['config']['grid']);
			$this->data['prevnext'] = $this->model->prevNext($id);

        $bookinglist = \DB::table('bookings')
            ->leftJoin('book_tour', 'bookings.bookingsID', '=', 'book_tour.bookingID')
            ->where('packageID', '=', $id)
            ->where('status','=',1)
			->orderBy('bookingsID','ASC')
			->groupBy('travellerID')
			->get();

		$bookinglist_count = \DB::table('bookings')
            ->leftJoin('book_tour', 'bookings.bookingsID', '=', 'book_tour.bookingID')
            ->where('packageID', '=', $id)
            ->where('status','=',1)
			->orderBy('bookingsID','ASC')
			->groupBy('travellerID')
            ->count();
		$expenselist = \DB::table('def_extra_expenses')
		->where([['packageID', '=', $id],['formula', '=', '1']])
		->get();
		$bkList = array();
		$first = 0;
		foreach($bookinglist as $bl)
		{
			$bkList[] = array(
				'travellers'	    =>$bl->travellerID ,
				// 'remarks'	        =>$bl->remarks ,
			);
			++$first;
		}
		$eList = array();
		$first = 0;
		foreach($expenselist as $el)
		{
			$eList[] = array(
				'expenseID'	    =>$el->expenseID ,
				// 'remarks'	        =>$bl->remarks ,
			);
			++$first;
		}
		$visa_List = array();
		$visaList = \DB::table('visaapplications')
		->where('formula', '=', '1')
		->where('packageID', '=', $id)
		->get();
		
		$first = 0;
		foreach($visaList as $visal)
		{
			$visa_List[] = array(
				'applicationID'	    =>$visal->applicationID ,
				// 'remarks'	        =>$bl->remarks ,
			);
			++$first;
		}
		$this->data['bkList']  = $bkList;
		$this->data['eList']  = $eList;
		$this->data['visa_List']  = $visa_List;
		// var_dump($this->data['visa_List']);exit;

        $room_single = \DB::table('book_room')
            ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
            ->where('packageID', '=', $id)
            ->where('roomtype','=',1)
            ->where('book_room.status','=',1)
            ->count();
        $room_double = \DB::table('book_room')
            ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
            ->where('packageID', '=', $id)
            ->where('roomtype','=',2)
            ->where('book_room.status','=',1)
            ->count();
        $room_triple = \DB::table('book_room')
            ->leftJoin('book_tour', 'book_room.bookingID', '=', 'book_tour.bookingID')
            ->where('packageID', '=', $id)
            ->where('roomtype','=',3)
            ->where('book_room.status','=',1)
			->count();
			$totals = $row->total_capacity;
        $total= $room_single+($room_double*2)+($room_triple*3) ;


				$this->data['room_single']          = $room_single;
				$this->data['room_double']          = $room_double;
				$this->data['room_triple']          = (int)$totals - (int)$total;
				$this->data['total']                = $total;

             if(!is_null($request->input('bookinglist')))
			{
				$html = view('packages.pdfbookinglist', $this->data)->render();
				return \PDF::load($html)->filename('BookingList-'.$id.'.pdf')->show();
			}

             if(!is_null($request->input('passportlist')))
			{
				$html = view('packages.pdfpassportlist', $this->data)->render();
				return \PDF::load($html)->filename('PassportList-'.$id.'.pdf')->show();
			}

             if(!is_null($request->input('emergencylist')))
			{
				$html = view('packages.pdfemergencylist', $this->data)->render();
				return \PDF::load($html, $size = 'A4', $orientation = 'landscape')->filename('PassportList-'.$id.'.pdf')->show();
			}


            return view('packages.view',$this->data);

		} else {
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.norecord'))->with('msgstatus','error');
		}
	}

	function postCopy( Request $request)
	{
	    foreach(\DB::select("SHOW COLUMNS FROM packages ") as $column)
        {
			if( $column->Field != 'packageID')
				$columns[] = $column->Field;
        }

		if(count($request->input('ids')) >=1)
		{
			$toCopy = implode(",",$request->input('ids'));
			$sql = "INSERT INTO packages (".implode(",", $columns).") ";
			$sql .= " SELECT ".implode(",", $columns)." FROM packages WHERE packageID IN (".$toCopy.")";
			\DB::insert($sql);
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');
		} else {
			return Redirect::to('packages')->with('messagetext',\Lang::get('core.note_selectrow'))->with('msgstatus','error');
		}

	}
	function image($request)
	{
		return $request->file('tourimage');
	}
	function postSave( Request $request)
	{
		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('tb_packages');
			if(!is_null($request->file('tourimage')))
			{
				$image = $this->image_upload($request);
				$data['packageimage'] = $image;
			}
			if(!is_null($request->file('gallery')))
			{
				$multi_image = $this->multi_image_upload($request);
				$data['gallery'] = $multi_image;
			}
			$data['currencyID'] = $request->input('currencyID');
			$data['flight'] = json_encode($request->input('flight'));
			$data['status'] = $request->input('status');
			$data['package_name'] = $request->input('package_name');
			if($request->input('inclusions') !== NULL){
					$data['inclusions'] = implode(",", $request->input('inclusions'));
			}
			if($request->input('similartours') !== NULL){
					$data['similarpackage'] = implode(",", $request->input('similartours'));
			}
			if($request->input('payment_options') !== NULL){
			$data['payment_options'] = implode(",", $request->input('payment_options'));
			}
			$data['remarks'] = $request->input('remarks');
			$data['policyandterms'] = $request->input('policyandterms');
			$id = $this->model->insertRow($data , $request->input('packageID'));

			if($id){
				//---tour features
				$part_count = $request->input('parts');
				$countryIDs = $request->input('countryID');
				$cityIDs = $request->input('cityID');
				$part_starts = $request->input('part_start');
				$part_ends = $request->input('part_end');
				$vehicleIDs = $request->input('vehicleID');
				$hotelIDs = $request->input('hotelID');
				$tour_feature_ids = $request->input('tour_feature_id');
				$tourfeature = new Tourfeature();
				for($i=0;$i<$part_count;$i++){
					$tour_feature_id = $tour_feature_ids[$i];
					$data_part['countryID'] = $countryIDs[$i];
					$data_part['cityID'] = $cityIDs[$i];
					$data_part['part_start'] = $part_starts[$i];
					$data_part['part_end'] = $part_ends[$i];
					$data_part['vehicleID'] = $vehicleIDs[$i];
					$data_part['hotelID'] = $hotelIDs[$i];
					$data_part['packageID'] = $id;
					$tourfeature->insertRow($data_part, $tour_feature_id);
				}
				for($i=$part_count;$i<5;$i++){
					$tour_feature_id = $tour_feature_ids[$i];
					if($tour_feature_id > 0)
						$tourfeature->find($tour_feature_id)->delete();
				}

				//---room room_features
				$counter = $request->input('counter');
				$room_typeIDs = $request->input('room_typeID');
				$costs = $request->input('cost');
				$seats = $request->input('seat');
				$room_feature_ids = $request->input('room_feature_id');
				$roomfeature = new Roomfeature();
				for($i=0;$i<$counter;$i++){
					$room_feature_id = $room_feature_ids[$i];
					$data_room_feature['roomtypeID'] = $room_typeIDs[$i];
					$data_room_feature['cost'] = $costs[$i];
					$data_room_feature['seat'] = $seats[$i];
					$data_room_feature['packageID'] = $id;
					$roomfeature->insertRow($data_room_feature, $room_feature_id);
				}
				for($i=$counter;$i<5;$i++){
					$room_feature_id = $room_feature_ids[$i];
					if($room_feature_id > 0)
						$roomfeature->find($room_feature_id)->delete();
				}
			}
			if(!is_null($request->input('apply')))
			{
				$return = 'packages/update/'.$id.'?return='.self::returnUrl();
			} else {
				$return = 'packages?return='.self::returnUrl();
			}
			// Insert logs into database
			if($request->input('packageID') =='')
			{
				\App\Library\SiteHelpers::auditTrail( $request , 'New Data with ID '.$id.' Has been Inserted !');
			} else {
				\App\Library\SiteHelpers::auditTrail($request ,'Data with ID '.$id.' Has been Updated !');
			}
			return Redirect::to($return)->with('messagetext',\Lang::get('core.note_success'))->with('msgstatus','success');
		} else {
			return Redirect::to('packages/update/'. $request->input('packageID'))->with('messagetext',\Lang::get('core.note_error'))->with('msgstatus','error')
			->withErrors($validator)->withInput();
		}
	}

	public function postDelete( Request $request)
	{

		if($this->access['is_remove'] ==0)
			return Redirect::to('dashboard')
				->with('messagetext', \Lang::get('core.note_restric'))->with('msgstatus','error');
		// delete multipe rows
		if(count($request->input('ids')) >=1)
		{
			$this->model->destroy($request->input('ids'));

			\App\Library\SiteHelpers::auditTrail( $request , "ID : ".implode(",",$request->input('ids'))."  , Has Been Removed Successfully");
			// redirect
			return Redirect::to('packages')
        		->with('messagetext', \Lang::get('core.note_success_delete'))->with('msgstatus','success');

		} else {
			return Redirect::to('packages')
        		->with('messagetext',\Lang::get('core.note_noitemdeleted'))->with('msgstatus','error');
		}

	}

	public static function display( )
	{
		$mode  = isset($_GET['view']) ? 'view' : 'default' ;
		$model  = new Packages();
		$info = $model::makeInfo('packages');

		$data = array(
			'pageTitle'	=> 	$info['title'],
			'pageNote'	=>  $info['note']

		);

		if($mode == 'view')
		{
			$id = $_GET['view'];
			$row = $model::getRow($id);
			if($row)
			{
				$data['row'] =  $row;
				$data['fields'] 		=  \App\Library\SiteHelpers::fieldLang($info['config']['grid']);
				$data['id'] = $id;
				return view('packages.public.view',$data);
			}

		} else {

			$page = isset($_GET['page']) ? $_GET['page'] : 1;
			$params = array(
				'page'		=> $page ,
				'limit'		=>  (isset($_GET['rows']) ? filter_var($_GET['rows'],FILTER_VALIDATE_INT) : 10 ) ,
				'sort'		=> 'packageID' ,
				'order'		=> 'asc',
				'params'	=> '',
				'global'	=> 1
			);

			$result = $model::getRows( $params );
			$data['tableGrid'] 	= $info['config']['grid'];
			$data['rowData'] 	= $result['rows'];

			$page = $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false ? $page : 1;
			$pagination = new Paginator($result['rows'], $result['total'], $params['limit']);
			$pagination->setPath('');
			$data['i']			= ($page * $params['limit'])- $params['limit'];
			$data['pagination'] = $pagination;
			return view('packages.public.index',$data);
		}
	}

	function postSavepublic(Request $request)
	{

		$rules = $this->validateForm();
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = $this->validatePost('packages');
			 $this->model->insertRow($data , $request->input('packageID'));
			return  Redirect::back()->with('messagetext','<p class="alert alert-success">'.\Lang::get('core.note_success').'</p>')->with('msgstatus','success');
		} else {

			return  Redirect::back()->with('messagetext','<p class="alert alert-danger">'.\Lang::get('core.note_error').'</p>')->with('msgstatus','error')
			->withErrors($validator)->withInput();

		}
	}

  static public function travelersDetail( $traveler = '')
	{
		$travelersDetail='';
		if($traveler !='')
		{
			$nameandsurname = \DB::table('travellers')
			->where('travellerID',$traveler)->value('nameandsurname');
			$InvTotal = \DB::table('invoice')
				->where('travellerID', '=', $traveler)
				->value('InvTotal');
			$currency_id = \DB::table('invoice')
				->where('travellerID', '=', $traveler)
				->value('currency');
			$currency = \DB::table('def_currency')
				->where('currencyID', '=', $currency_id)
				->value('currency_sym');
			$currency_sym = \DB::table('def_currency')
			->where('currencyID', '=', $currency_id)
			->value('symbol');
			$invoice_total = \DB::table('invoice')
			->where('travellerID', '=', $traveler)
			->get();
			$InvID = \DB::table('invoice')
			->where('travellerID', '=', $traveler)
			->value('invoiceID');
			$payment=\DB::table('invoice_payments')->where('invoiceID', $InvID )->sum('amount');

			$invoice_status = InvoiceStatus::payments($payment, $InvTotal);
			$payments=  \DB::table('invoice_payments')->where('travellerID',$traveler)->sum('amount');
			$travelersDetail .= "<div class='col-md-3'><a href='".url('travellers/show')."/".$traveler."'>".$nameandsurname."</a></div><div class='col-md-3'>".$currency_sym." ".$InvTotal."</div><div class='col-md-3'>".$currency_sym." ".$payments."</div><div class='col-md-3'>".InvoiceStatus::package_payments($payment, $InvTotal)."</div>";
		}
		return $travelersDetail;
	}

	static public function travelersDetail_visa($id, $traveler = '')
	{
	// var_dump("-----------");exit;


		$visaDetail = '';
			$visas = \DB::table('visaapplications')
			->where('applicationID',$id)->get();
			foreach($visas as $visa){
				$travellers = VisaapplicationController::visaApplicants($visa->travellersID);
				$applicationdate = SiteHelpers::TarihFormat($visa->applicationdate);
				$status = GeneralStatuss::Visa($visa->status);
				$processtime = $visa->processintime;

			$visaDetail .= "<div class='col-md-3'>".$travellers."</div><div class='col-md-3'>".$applicationdate."</div>
			<div class='col-md-3'>".$processtime."  Days</div>
			<div class='col-md-3'>".$status."</div>";
			}
		return $visaDetail;
	}

	static public function travelersDetail_expense($id, $traveler = '')
	{
		$expenseDetail = '';
			$expense = \DB::table('def_extra_expenses')
			->where('expenseID',$id)->get();
			foreach($expense as $ex){
				$expenseID = $ex->expenseID;
				$extra_expenses = $ex->extra_expenses;
				$cost = $ex->cost;
				$currencyID = $ex->currencyID;
				$status = $ex->status;
				$formula = $ex->formula;
				$tourcategoriesID = $ex->tourcategoriesID;
				$packageID = $ex->packageID;
				$data = $ex->data;
				$remarks = $ex->remarks;
				$paymenttypeID = $ex->paymenttypeID;
				$attached = $ex->attached;
				$staff = $ex->staff;

				$staff = \DB::table('staffs')
				->where('staffID',$staff)->value('name');
				$def_payment_types = \DB::table('def_payment_types')
				->where('paymenttypeID',$paymenttypeID)->value('payment_type');
				$def_currency = \DB::table('def_currency')
				->where('currencyID',$currencyID)->value('symbol');
				$category = '';
				if($formula == '1'){
					$category = "Package";
				}else{
					$category = "Simple";

				}
			$expenseDetail .= "<div class='col-md-2'>".$staff."</div><div class='col-md-1'>".$def_currency." ".$cost."</div><div class='col-md-1'>".$def_payment_types."</div>
			<div class='col-md-2'>".$data."</div><div class='col-md-2'>".$extra_expenses."</div><div class='col-md-1'>".$category."</div>
			<div class='col-md-2'>".$remarks."</div><div class='col-md-1'><a href='".asset('storage')."/files/".$attached."' class='text-red' target='_blank'><i class='fa fa-file-pdf-o fa-2x'></i></a></div>";
			}
		return $expenseDetail;
	}
	static public function travelersDetail_paid( $traveler = '')
	{
		$travelersDetail='';
		if($traveler !='')
		{
					$nameandsurname = \DB::table('travellers')
					->where('travellerID',$traveler)->value('nameandsurname');
					$InvTotal = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('InvTotal');
					$currency_id = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('currency');
					$currency = \DB::table('def_currency')
						->where('currencyID', '=', $currency_id)
						->value('currency_sym');
					$currency_sym = \DB::table('def_currency')
					->where('currencyID', '=', $currency_id)
					->value('symbol');
					$invoice_total = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->get();
					$InvID = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->value('invoiceID');
					$payment=\DB::table('invoice_payments')->where('invoiceID', $InvID )->sum('amount');

					$invoice_status = InvoiceStatus::payments($payment, $InvTotal);
					$payments=  \DB::table('invoice_payments')->where('travellerID',$traveler)->sum('amount');
					$travelersDetail = $InvTotal;
		}
		return $travelersDetail;
	}

	static public function travelersDetail_unpaid( $traveler = '')
	{
		$travelersDetail='';
		if($traveler !='')
		{

					$nameandsurname = \DB::table('travellers')
					->where('travellerID',$traveler)->value('nameandsurname');
					$InvTotal = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('InvTotal');
					$currency_id = \DB::table('invoice')
						->where('travellerID', '=', $traveler)
						->value('currency');
					$currency = \DB::table('def_currency')
						->where('currencyID', '=', $currency_id)
						->value('currency_sym');
					$currency_sym = \DB::table('def_currency')
					->where('currencyID', '=', $currency_id)
					->value('symbol');
					$invoice_total = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->get();
					$InvID = \DB::table('invoice')
					->where('travellerID', '=', $traveler)
					->value('invoiceID');
					$payment=\DB::table('invoice_payments')->where('invoiceID', $InvID )->sum('amount');

					$invoice_status = InvoiceStatus::payments($payment, $InvTotal);
					$payments=  \DB::table('invoice_payments')->where('travellerID',$traveler)->sum('amount');
					$travelersDetail =$payments;
		}
		return $travelersDetail;
	}


  static public function travelersDetailpdf( $traveler = '')
	{
		$travelersDetail='';
		if($traveler !='')
		{
			$sqltrv = \DB::table('travellers')->whereIn('travellerID',explode(',',$traveler))->get();

            foreach ($sqltrv as $v2) {

				$travelersDetail .= "<tr><td style='border:0px;'> ".$v2->nameandsurname."</td><td style='width:5%;'> ".SiteHelpers::formatLookUp($v2->countryID,'countryID','1:def_country:countryID:country_code')."</td></tr>";
			}
		}
		return $travelersDetail;
	}

  static public function travelersDetailpassport( $travelerpass = '')
	{
		$travelersDetailpassport='';
		if($travelerpass !='')
		{
			$sqltrvpass = \DB::table('travellers')->whereIn('travellerID',explode(',',$travelerpass))->get();

            foreach ($sqltrvpass as $v3) {

				$travelersDetailpassport .= "<tr>
                <td style='width:20%'> ".$v3->nameandsurname."</td>
                <td style='width:15%'> ".$v3->passportno."</td>
                <td style='width:20%'> ".SiteHelpers::formatLookUp($v3->passportcountry,'countryID','1:def_country:countryID:country_name')."</td>
                <td style='width:15%'> ".SiteHelpers::TarihFormat($v3->dateofbirth)."</td>
                <td style='width:15%'> ".SiteHelpers::TarihFormat($v3->passportissue)."</td>
                <td style='width:15%'> ".SiteHelpers::TarihFormat($v3->passportexpiry)."</td>
                </tr>";
			}
		}
		return $travelersDetailpassport;
	}


  static public function travelersDetailemergency( $traveleremr = '')
	{
		$travelersDetailemergency='';
		if($traveleremr !='')
		{
			$sqltrvemr = \DB::table('travellers')->whereIn('travellerID',explode(',',$traveleremr))->get();

            foreach ($sqltrvemr as $v4) {

				$travelersDetailemergency .= "<tr>
        <td> ".$v4->nameandsurname."</td>
        <td> ".$v4->emergencycontactname."</td>
        <td> ".$v4->emergencycontactemail."</td>
        <td> ".$v4->emergencycontanphone."</td>
        <td> ".$v4->insurancecompany."</td>
        <td> ".$v4->insurancepolicyno."</td>
        <td> ".$v4->insurancecompanyphone."</td>
        <td> ".$v4->bedconfiguration."</td>
        <td> ".$v4->dietaryrequirements."</td>
        </tr>";
			}
		}
		return $travelersDetailemergency;
	}



}
