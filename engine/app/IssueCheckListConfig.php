<?php
/**
 * Created by Tanay Kumar Roy<tanayroy12@gmail.com>.
 * User: Tanay
 * Date: 7/13/2020
 * Time: 12:54 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IssueCheckListConfig extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "issue_check_list_config";
    protected $primaryKey = 'id';
    protected $fillable =[
        'issue_id',
        'label_name',
        'field_type',
        'field_name',
        'options',
        'placeholder',
        'maximum_length',
        'is_required',

    ];
}
