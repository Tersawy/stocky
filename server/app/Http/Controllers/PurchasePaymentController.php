<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Models\Purchase;
use Illuminate\Http\Request;
use App\Models\PurchasePayment;
use App\Requests\PurchasePaymentRequest;

class PurchasePaymentController extends Controller
{
  public function index(Request $req, $purchaseId)
  {
    $req->merge(['purchaseId' => $purchaseId]);

    $req->validate(['purchaseId' => ['required', 'numeric', 'exists:purchases,id']]);

    $payments = PurchasePayment::select(['id', 'reference', 'amount', 'payment_method', 'date'])->where('purchase_id', $purchaseId)->get();

    return $this->success($payments);
  }


  public function create(Request $req, $purchaseId)
  {
    $attr = PurchasePaymentRequest::validationCreate($req);

    $purchase = Purchase::find($purchaseId);

    if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

    if ($purchase->payment_status == Constants::PAYMENT_STATUS_PAID) {
      return $this->error('This purchase invoice has been paid !', 422);
    }

    $payment = PurchasePayment::create($attr);

    $payment->reference = "INV/PR_" . (1110 + $payment->id);

    $payment->save();

    $purchase->paid += $payment->amount;

    $purchase->payment_status = $purchase->new_payment_status;

    $purchase->save();

    return $this->success([], 'The Payment has been added successfully');
  }


  public function update(Request $req, $purchaseId, $id)
  {
    $attr = PurchasePaymentRequest::validationUpdate($req);

    $purchase = Purchase::find($purchaseId);

    if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

    $payment = PurchasePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $purchase->paid = $purchase->paid - $payment->amount + $attr['amount'];

    $purchase->payment_status = $purchase->new_payment_status;

    $payment->fill($attr);

    $payment->save();

    $purchase->save();

    return $this->success([], 'The Payment has been updated successfully');
  }


  public function remove(Request $req, $purchaseId, $id)
  {
    PurchasePaymentRequest::validationRemove($req);

    $purchase = Purchase::find($purchaseId);

    if (!$purchase) return $this->error('The purchase invoice is not found to add payment', 404);

    $payment = PurchasePayment::find($id);

    if (!$payment) return $this->error('This payment is not found', 404);

    $purchase->paid -= $payment->amount;

    $purchase->payment_status = $purchase->new_payment_status;

    $payment->delete();

    $purchase->save();

    return $this->success([], 'The Payment has been deleted successfully');
  }
}
