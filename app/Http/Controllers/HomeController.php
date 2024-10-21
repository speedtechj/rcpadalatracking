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
