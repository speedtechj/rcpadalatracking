<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Trackdata;
use App\Models\Trackdetail;
use App\Models\Trackstatedetail;
use App\Models\Invoicestatus;
use App\Models\Trackstatus;
// use Illumitate\Support\Facades\DB;

use DB;

class HomeController extends Controller
{
  public function index()
  {

    return view('welcome');
  }

  public function show(Request $request)
  {

    $invoice = $request->invoice;

    $recordctn = DB::connection('mysql2')->table('dropofftransactions')
      ->where('reference', $invoice)
      ->orWhere('invoice', $invoice)
      ->count();

    if ($recordctn == 1) {
      $olddata = DB::connection('mysql2')->table('dropofftransactions')
        ->join('assignbatches', 'assignbatches.reference', '=', 'dropofftransactions.reference')
        ->where('dropofftransactions.reference', $invoice)
        ->orWhere('dropofftransactions.invoice', $invoice)
        ->first();
    } else {

      $olddata = DB::connection('mysql2')->table('pickuptransactions')
        ->join('assignbatches', 'assignbatches.reference', '=', 'pickuptransactions.reference')
        ->where('pickuptransactions.reference', $invoice)
        ->orWhere('pickuptransactions.invoice', $invoice)
        ->first();
    }
    if ($olddata) {
      $batchstatus = DB::connection('mysql2')->table('trackstatuses')
        ->where('batchnumber', $olddata->batch_no_year)
        ->get();

      $detailstatus = DB::connection('mysql2')->table('trackstatdetails')
        ->where('trackstat_ref', $olddata->reference)
        ->get();
     


       return view('batchstatus', ['batchstatus' => $batchstatus, 'invoice' => $invoice, 'detailstatus' => $detailstatus]);
    } else {
      $newdata = DB::connection('mysql')->table('invoicestatuses')
        ->join('trackstatuses', 'trackstatuses.id', '=', 'invoicestatuses.trackstatus_id')
        ->where('invoicestatuses.generated_invoice', $invoice)
        ->orwhere('invoicestatuses.manual_invoice', $invoice)
        ->get();
        if($newdata->count() <= 0){
          return view('invoice_not_found', ['invoice1' => $invoice]);
        }
          
        return view('newdata', ['invoice' => $newdata, 'invoice1' => $invoice]);
    }


    

  }
}
