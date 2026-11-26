<?php

namespace Modules\Requisition\App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Requisition\App\Http\Requests\StockRequest;
use Modules\Requisition\App\Models\Stock;
use Modules\Requisition\App\Models\StockHistory;
use Modules\Requisition\App\Models\RequisitionItem;
use Modules\Requisition\App\Models\Vendor;

class StockController extends Controller
{
    public function index($type = null)
    {
        $requisitionItems = RequisitionItem::select(
            'requisition_items.id',
            'requisition_items.product_name',
            'requisition_items.image',
            'requisition_items.type',
            'requisition_items.status',
            'stock.low_stock_alert_qty'
        )
        ->leftJoin('stock', 'stock.requisition_item_id', '=', 'requisition_items.id')
        ->where('requisition_items.type', 'printingStationery')
        ->get();
        $vendors = Vendor::select('id', 'name')->where('status', 1)->get();
        return view('requisition::stock.create', compact('requisitionItems', 'vendors', 'type'));
    }

    public function quantityStore(StockRequest $request, $type = null)
    {
        foreach ($request->item_ids as $itemId) {
            $quantity = intval($request->quantities[$itemId] ?? 0);
            $low_stock_alert_qty = intval($request->low_stock_alert_qty[$itemId] ?? 0);

            if ($quantity <= 0 || $low_stock_alert_qty <= 0) {
                continue;
            }

            $stock = Stock::firstOrNew(['requisition_item_id' => $itemId]);
            $stock->quantity += $quantity;
            $stock->low_stock_alert_qty = $low_stock_alert_qty;
            $stock->save();

            $uniqueNumber = 'REQ-' . date('ymd') . rand(10, 99);

            $history = new StockHistory();
            $history->requisition_item_id = $itemId;
            $history->quantity = $quantity;
            $history->vendor_id = $request->vendor_id;
            $history->challan_no = $request->challan_no ?? $uniqueNumber;
            $history->create_id = Auth::user()->user_id;
            $history->created_at = Carbon::now();
            $history->save();
        }

        flash('Stock created successfully.', 'success');
        return redirect()->route('requisition.stock.list', ['type' => $type]);
    }

    public function list($type = null)
    {
        $tblData = array();
        $lists = Stock::with('requItem.unit')
            ->select('id', 'requisition_item_id', 'quantity', 'low_stock_alert_qty')
            ->get();

        if (!empty($lists)) {
            $tblData = $lists->toArray();
        }

        return view('requisition::stock.lists', compact('lists', 'tblData', 'type'));
    }

    public function history()
    {

        $tblData = array();
        $lists = StockHistory::with(['requItem'])
            ->select('id', 'requisition_item_id', 'quantity', 'create_id', 'challan_no', 'created_at')
            ->get();

        if (!empty($lists)) {
            $tblData = $lists->toArray();
        }
        return view('requisition::stock.history', compact('lists', 'tblData'));
    }
}
