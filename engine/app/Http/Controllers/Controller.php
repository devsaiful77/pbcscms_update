<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $roleId;

    public function __construct()
    {


    }

    function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function generatePDF($param) {
        $title_for_layout  = (!empty($param['title_for_layout'])) ? $param['title_for_layout'] : '';
        $pdfName = (!empty($param['pdfName'])) ? $param['pdfName'] : '';
        $pdf = PDF::loadView($param['loadView'], compact('param','title_for_layout'));
        $pdf->download($pdfName);
    }
}
