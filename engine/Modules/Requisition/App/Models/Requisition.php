<?php

namespace Modules\Requisition\App\Models;

use App\User;
use App\SubgroupInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requisition extends Model
{
    use HasFactory;

    protected $table = 'requisition';
    protected $guarded = ['id'];

    protected $casts = [
        'vendor_product_map' => 'array',
    ];

    public function generalServices()
    {
        return $this->hasMany(RequisitionGeneralService::class, 'requisition_number', 'requisition_number');
    }

    public function printings()
    {
        return $this->hasMany(RequisitionPrinting::class, 'requisition_number', 'requisition_number');
    }

    public function tableStationeries()
    {
        return $this->hasMany(RequisitionNonPrinting::class, 'requisition_number', 'requisition_number');
    }

    public function requAttachments()
    {
        return $this->hasMany(RequisitionAttachment::class, 'requisition_number', 'requisition_number');
    }

    public function requComments()
    {
        return $this->hasMany(RequisitionComments::class, 'requisition_number', 'requisition_number');
    }

    public function printingRequHistories()
    {
        return $this->hasMany(RequisitionPrintingHistory::class, 'requisition_number', 'requisition_number');
    }

    public function serviceRequHistories()
    {
        return $this->hasMany(RequisitionSupportServiceHistory::class, 'requisition_number', 'requisition_number');
    }

    public function tableRequHistories()
    {
        return $this->hasMany(RequisitionTableHistory::class, 'requisition_number', 'requisition_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getVendorsAttribute()
    {
        return Vendor::whereIn('id', explode(',', $this->vendors_id))->get();
    }

    public function subgroup()
    {
        return $this->belongsTo(SubgroupInfo::class, 'source_sub_group_info_id');
    }


    //base report query for requisition report
    public static function baseReportQuery()
    {
        return self::select(
            'requisition.*',
            'users.user_id as username',
            'sg1.name as source_subgroup_name',
            'sg2.name as subgroup_name',
            'requisition_region.name as region_name',
            DB::raw("FROM_UNIXTIME(requisition.date,'%Y-%m-%d & %h:%i:%s %p') AS date_UNXTIME"),
            DB::raw("
                CASE
                    WHEN requisition.unit_id = 25 THEN 'Requisition Initiator'
                    WHEN requisition.unit_id = 53 THEN 'Requisition Admin Maker'
                    WHEN requisition.unit_id = 54 THEN 'Requisition Admin Checker'
                    ELSE ''
                END AS unit
            "),
            DB::raw("GROUP_CONCAT(DISTINCT vendors.name SEPARATOR ', ') AS vendor_name"),

            // General Service Products
            DB::raw("GROUP_CONCAT(DISTINCT gs_products.product_name SEPARATOR ', ') AS gs_product_names"),

            // Non-Printing (Table Stationery) - এখানে Total Price এবং গ্র্যান্ড টোটালের জন্য শুধু Sum যোগ করা হয়েছে
            DB::raw("
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        np_products.product_name,
                        ' (Qty: ', COALESCE(requisition_non_printing.approve_qty, 0),
                        ', Price: ', COALESCE(np_products.unit_price, 0),
                        ', Total: ', (COALESCE(requisition_non_printing.approve_qty, 0) * COALESCE(np_products.unit_price, 0)), ')'
                    ) SEPARATOR ' | '
                ) AS ts_product_details
            "),
            DB::raw("SUM(COALESCE(requisition_non_printing.approve_qty, 0) * COALESCE(np_products.unit_price, 0)) AS ts_grand_total"),

            // Printing Stationery - এখানেও একইভাবে সেট করা হয়েছে
            DB::raw("
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        p_products.product_name,
                        ' (Qty: ', COALESCE(requisition_printing.approve_qty, 0),
                        ', Price: ', COALESCE(p_products.unit_price, 0),
                        ', Total: ', (COALESCE(requisition_printing.approve_qty, 0) * COALESCE(p_products.unit_price, 0)), ')'
                    ) SEPARATOR ' | '
                ) AS ps_product_names
            "),
            DB::raw("SUM(COALESCE(requisition_printing.approve_qty, 0) * COALESCE(p_products.unit_price, 0)) AS ps_grand_total")
        )
            ->leftJoin('subgroup_info as sg1', 'requisition.source_sub_group_info_id', '=', 'sg1.id')
            ->leftJoin('subgroup_info as sg2', 'requisition.sub_group_info_id', '=', 'sg2.id')
            ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id')
            ->leftJoin('users', 'requisition.user_id', '=', 'users.id')

            ->leftJoin('requisition_general_service', 'requisition.requisition_number', '=', 'requisition_general_service.requisition_number')
            ->leftJoin('requisition_items as gs_products', 'requisition_general_service.product_id', '=', 'gs_products.id')

            // এখানে আপনার requisition_non_printing এবং requisition_items (np_products) যুক্ত আছে
            ->leftJoin('requisition_non_printing', 'requisition.requisition_number', '=', 'requisition_non_printing.requisition_number')
            ->leftJoin('requisition_items as np_products', 'requisition_non_printing.product_id', '=', 'np_products.id')

            ->leftJoin('requisition_printing', 'requisition.requisition_number', '=', 'requisition_printing.requisition_number')
            ->leftJoin('requisition_items as p_products', 'requisition_printing.product_id', '=', 'p_products.id')

            ->leftJoin('vendors', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(vendors.id, REPLACE(requisition.vendors_id, ' ', ''))"), ">", DB::raw("0"));
            })
            ->groupBy('requisition.id');
    }

    public static function baseHistoryReportQuery()
    {
        $historyUnion = DB::table('requisition_printing_histories')
            ->select('requisition_number', 'comment', 'action', 'time')
            ->unionAll(DB::table('requisition_table_histories')->select('requisition_number', 'comment', 'action', 'time'))
            ->unionAll(DB::table('requisition_support_service_histories')->select('requisition_number', 'comment', 'action', 'time'));

        $historySub = DB::table(DB::raw("({$historyUnion->toSql()}) as history"))
            ->mergeBindings($historyUnion)
            ->select(
                'requisition_number',
                DB::raw('GROUP_CONCAT(comment SEPARATOR " | ") as comment'),
                DB::raw('GROUP_CONCAT(action SEPARATOR " | ") as action'),
                DB::raw('GROUP_CONCAT(FROM_UNIXTIME(time, "%Y-%m-%d %h:%i:%s %p") SEPARATOR " | ") as time')
            )
            ->groupBy('requisition_number');

        $itemsSub = DB::table('requisition_general_service')
            ->select('requisition_number', DB::raw('GROUP_CONCAT(requisition_items.product_name) as gs_product_names'))
            ->join('requisition_items', 'requisition_general_service.product_id', '=', 'requisition_items.id')
            ->groupBy('requisition_number');

        $npItemsSub = DB::table('requisition_non_printing')
            ->select('requisition_number', DB::raw('GROUP_CONCAT(requisition_items.product_name) as ts_product_names'))
            ->join('requisition_items', 'requisition_non_printing.product_id', '=', 'requisition_items.id')
            ->groupBy('requisition_number');

        $pItemsSub = DB::table('requisition_printing')
            ->select('requisition_number', DB::raw('GROUP_CONCAT(requisition_items.product_name) as ps_product_names'))
            ->join('requisition_items', 'requisition_printing.product_id', '=', 'requisition_items.id')
            ->groupBy('requisition_number');

        return self::select(
            'requisition.*',
            'users.user_id as user_id',
            'sg1.name as source_subgroup_name',
            'sg2.name as subgroup_name',
            'requisition_region.name as region_name',
            DB::raw("FROM_UNIXTIME(requisition.date,'%Y-%m-%d %h:%i:%s %p') AS date_UNXTIME"),
            'history.comment',
            'history.action',
            'history.time',
            'gs_sub.gs_product_names',
            'np_sub.ts_product_names',
            'p_sub.ps_product_names'
        )
        ->leftJoin('subgroup_info as sg1', 'requisition.source_sub_group_info_id', '=', 'sg1.id')
        ->leftJoin('subgroup_info as sg2', 'requisition.sub_group_info_id', '=', 'sg2.id')
        ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id')
        ->leftJoin('users', 'requisition.user_id', '=', 'users.id')
        ->leftJoinSub($historySub, 'history', function ($join) {
            $join->on('requisition.requisition_number', '=', 'history.requisition_number');
        })
        ->leftJoinSub($itemsSub, 'gs_sub', function ($join) {
            $join->on('requisition.requisition_number', '=', 'gs_sub.requisition_number');
        })
        ->leftJoinSub($npItemsSub, 'np_sub', function ($join) {
            $join->on('requisition.requisition_number', '=', 'np_sub.requisition_number');
        })
        ->leftJoinSub($pItemsSub, 'p_sub', function ($join) {
            $join->on('requisition.requisition_number', '=', 'p_sub.requisition_number');
        });
    }

    public static function baseProductQtyReportQuery()
    {
        // ১. requisition_non_printing সাব-কোয়েরি (date যোগ করা হয়েছে)
        $nonPrintingQuery = DB::table('requisition_non_printing as rnp')
            ->join('requisition as r', 'r.requisition_number', '=', 'rnp.requisition_number')
            ->join('requisition_items as ri', 'ri.id', '=', 'rnp.product_id')
            ->select(
                'rnp.product_id',
                'ri.product_name',
                'ri.unit_price',
                'rnp.approve_qty',
                'r.date' // ইউনিক্স টাইমস্ট্যাম্প কলাম
            );

        // ২. requisition_printing সাব-কোয়েরি (date যোগ করা হয়েছে)
        $printingQuery = DB::table('requisition_printing as rp')
            ->join('requisition as r', 'r.requisition_number', '=', 'rp.requisition_number')
            ->join('requisition_items as ri', 'ri.id', '=', 'rp.product_id')
            ->select(
                'rp.product_id',
                'ri.product_name',
                'ri.unit_price',
                'rp.approve_qty',
                'r.date' // ইউনিক্স টাইমস্ট্যাম্প কলাম
            );

        // ৩. UNION ALL কম্বাইন
        $combinedQuery = $nonPrintingQuery->unionAll($printingQuery);

        // ৪. ফাইনাল কুয়েরি বিল্ডার
        return DB::table(DB::raw("({$combinedQuery->toSql()}) as combined_items"))
            ->mergeBindings($combinedQuery)
            ->select(
                'combined_items.product_name',
                'combined_items.unit_price',
                'combined_items.product_id', // কন্ডিশনের জন্য এটিও সিলেক্টে রাখা ভালো
                DB::raw('SUM(combined_items.approve_qty) AS total_approved_qty'),
                DB::raw('SUM(combined_items.approve_qty * combined_items.unit_price) AS Total_Requisition_Amount')
            )
            ->groupBy('combined_items.product_id', 'combined_items.product_name', 'combined_items.unit_price');
    }


    public static function baseProductQuantityReportQuery()
    {
        // Each "ticket" type unioned together with a label
        $gs = DB::table('requisition_general_service')
            ->select(
                'requisition_number',
                'product_id',
                'quantity',
                'approve_qty',
                DB::raw("'General Service' as ticket_type")
            );

        $np = DB::table('requisition_non_printing')
            ->select(
                'requisition_number',
                'product_id',
                'quantity',
                'approve_qty',
                DB::raw("'Non-Printing Stationery' as ticket_type")
            );

        $pr = DB::table('requisition_printing')
            ->select(
                'requisition_number',
                'product_id',
                'quantity',
                'approve_qty',
                DB::raw("'Printing Stationery' as ticket_type")
            );

        $itemsUnion = $gs->unionAll($np)->unionAll($pr);
        $itemsSub = DB::query()->fromSub($itemsUnion, 'items_union');

        return self::select(
            'requisition.*',
            'users.user_id as username',
            'sg1.name as source_subgroup_name',
            'sg2.name as subgroup_name',
            'requisition_region.name as region_name',
            DB::raw("FROM_UNIXTIME(requisition.date,'%Y-%m-%d & %h:%i:%s %p') AS date_UNXTIME"),
            DB::raw("
                CASE
                    WHEN requisition.unit_id = 25 THEN 'Requisition Initiator'
                    WHEN requisition.unit_id = 53 THEN 'Requisition Admin Maker'
                    WHEN requisition.unit_id = 54 THEN 'Requisition Admin Checker'
                    ELSE ''
                END AS unit
            "),
            'items_union.ticket_type',
            'requisition_items.product_code',
            'requisition_items.product_name',
            'items_union.quantity as requested_quantity',
            'items_union.approve_qty as approved_quantity',
            'requisition_items.unit_price',
            DB::raw('(items_union.approve_qty * requisition_items.unit_price) as total_requisition_amount')
        )
            ->leftJoinSub($itemsSub, 'items_union', function ($join) {
                $join->on('requisition.requisition_number', '=', 'items_union.requisition_number');
            })
            ->leftJoin('requisition_items', 'items_union.product_id', '=', 'requisition_items.id')
            ->leftJoin('subgroup_info as sg1', 'requisition.source_sub_group_info_id', '=', 'sg1.id')
            ->leftJoin('subgroup_info as sg2', 'requisition.sub_group_info_id', '=', 'sg2.id')
            ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id')
            ->leftJoin('users', 'requisition.user_id', '=', 'users.id');
    }

    /*public static function baseHistoryReportQuery()
    {
        $printing = DB::table('requisition_printing_histories')
            ->select(
                'requisition_number',
                'comment',
                'action',
                'time'
            );

        $table = DB::table('requisition_table_histories')
            ->select(
                'requisition_number',
                'comment',
                'action',
                'time'
            );

        $support = DB::table('requisition_support_service_histories')
            ->select(
                'requisition_number',
                'comment',
                'action',
                'time'
            );

        $historyUnion = $printing->unionAll($table)->unionAll($support);

        $historySub = DB::query()->fromSub($historyUnion, 'history');

        return self::select(
            'requisition.*',
            'users.user_id as user_id',
            'sg1.name as source_subgroup_name',
            'sg2.name as subgroup_name',
            'requisition_region.name as region_name',
            DB::raw("FROM_UNIXTIME(requisition.date,'%Y-%m-%d & %h:%i:%s %p') AS date_UNXTIME"),
            DB::raw("
                CASE
                    WHEN requisition.unit_id = 25 THEN 'Requisition Initiator'
                    WHEN requisition.unit_id = 53 THEN 'Requisition Admin Maker'
                    WHEN requisition.unit_id = 54 THEN 'Requisition Admin Checker'
                    ELSE ''
                END AS unit
            "),
            'history.comment',
            'history.action',
            DB::raw("FROM_UNIXTIME(history.time,'%Y-%m-%d & %h:%i:%s %p') AS time"),
        )
            ->leftJoin('subgroup_info as sg1', 'requisition.source_sub_group_info_id', '=', 'sg1.id')
            ->leftJoin('subgroup_info as sg2', 'requisition.sub_group_info_id', '=', 'sg2.id')
            ->leftJoin('requisition_region', 'requisition_region.id', '=', 'requisition.region_id')
            ->leftJoin('users', 'requisition.user_id', '=', 'users.id')

            ->leftJoinSub($historySub, 'history', function($join) {
                $join->on('requisition.requisition_number', '=', 'history.requisition_number');
            })
            ->leftJoin('vendors', function($join){
                $join->whereRaw('FIND_IN_SET(vendors.id, requisition.vendors_id)');
            });
    }*/
}
