<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IssueConfig extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = "issue_config";
    protected $primaryKey = 'id';
    protected $fillable =[
        'issue_id',
        'label_name',
        'field_type',
        'field_name',
        'api_key',
        'options',
        'placeholder',
        'maximum_length',
        'minimum_length',
        'fixed_length',
        'is_required',
        'fieldset_group_id'

    ];

    public function fieldsetGroup()
    {
        return $this->belongsTo(issueFieldsetGroup::class,'fieldset_group_id');
    }
}
