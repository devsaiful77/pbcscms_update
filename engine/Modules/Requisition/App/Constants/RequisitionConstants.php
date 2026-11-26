<?php

namespace Modules\Requisition\App\Constants;

class RequisitionConstants
{
    public const SUPPORTSERVICEITEM = "supportService";
    public const PRINTINGITEM = "printingStationery";
    public const NONPRINTINGITEM = "tableStationery";
    public const REPAIRITEM = "repair";
    public const NEWASSETITEM = "newAsset";

    public const REQ_CANCEL = 0;
    public const REQ_PENDING = 1;
    public const REQ_DRAFT = 2;
    public const REQ_WIP = 3;
    public const REQ_HOLD = 4;
    public const REQ_APPROVE = 10;
    public const REQ_CLOSE = 11;

    //general support service requisition types
    public const REPAIR_MAINTENANCE = 1;
    public const NEW_ASSET = 2;
}
