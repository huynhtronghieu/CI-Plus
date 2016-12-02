<?php
/* Column trong FORM new - edit */

// --------------------------- Hidden / Hidden Global ------------------------------ //
array(
	'type' => 'hidden',
	'name' => $this->primary_key,
	'column_pos' => 1
	),
array(
	'type' => 'hidden', // hidden_global
	'name' => 'created_date',
	'save_table' => $this->table,
	'value' => date('Y-m-d H:i:s'), /* Default value */
	'column_pos' => 1,
	'formatter' => function( $d, $row ) { /* Format value before save */
		$serialize_data = $this->convert_to_array($row, 'table_serialize');
		$sof_detail = $serialize_data['sof_detail'];
		foreach($sof_detail as $value){
			if($value['code']=='FWE') return $value['date'];
		}
		return $d;
	}
	),

// --------------------------- Textbox / Password / Email / Datetime / Datetimefull ------------------------------ //
array(
	'type' => 'textbox', // password // email
	'name' => 'number',
	'value' => function(){ /* Default value */
		$detail = $this->get_items('number DESC')->row_array();
		if(empty($detail)) $result = str_pad(1, 4, "0", STR_PAD_LEFT);
		else $result = str_pad(intval($detail['number'])+1, 4, "0", STR_PAD_LEFT);
		return $result;
		},
	'save_table' => $this->table,
	'placeholder' => 'Lệnh số',
	'class' => 'required',
	'column_pos' => 1
	),
array(
	'type' => 'datetime', // datetimefull
	'name' => 'expired_date',
	'save_table' => $this->table,
	'value' => date('Y-m-d H:i:s'), /* Default value */
	'placeholder' => 'Lệnh có giá trị hết ngày',
	'class' => 'required',
	'column_pos' => 1
	),

// --------------------------- Textarea ------------------------------ //
array(
	'type' => 'textarea',
	'name' => 'content',
	'save_table' => $this->table,
	'placeholder' => 'Nội dung email',
	'class' => 'required wysihtml5', /* Class editor */
	'attr' => array('rows'=>15),
	'column_pos' => 1
	),

// --------------------------- Radio / Checkbox ------------------------------ //
array(
    'type' => 'radio', // checkbox
    'option_local' => array( /* Hard code option */
        '1' => 'Hư',
        '0' => 'Bình thường'
        ),
    'name' => 'is_damage',
    'formatter' => function($d, $row){ /* Format value before save */
        $detail = $this->db->where('container_id', $row['container_id'])->where('deleted', 0)->order_by('container_log_id DESC')->limit(1,0)->get($this->prefix.'container_log')->row_array();
        if(!empty($detail)){
            $detail['is_damage'] = $d;
            $detail['created_date'] = date('Y-m-d H:i:s');
            $detail['note'] = 'Cập nhật hư hại.';
            unset($detail['container_log_id']);
            $this->db->insert($this->prefix.'container_log', $detail);
        }
        return serialize($d);
    },
    'save_table' => $this->table,
    'placeholder' => 'Tình trạng',
    'class' => 'required',
    'column_pos' => 1
    ),

// --------------------------- Radio Chain ------------------------------ //
array(
	'type' => 'radio',
	'option_local' => array(
		'0' => 'Chính tôi|hidden', /* Not show any element when choose this */
		'1' => 'Cha mẹ|hidden',
		'2' => 'Người thân|hidden',
		'3' => 'Giáo viên|hidden',
		'4' => 'Khác|relationship_note', /* Show element when choose this */
		),
	'name' => 'relationship',
	'save_table' => $this->table,
	'placeholder' => 'Quan hệ với người liên hệ',
	'class' => 'required',
	'column_pos' => 4
	),
array(
	'type' => 'textbox',
	'name' => 'relationship_note', /* Element will show */
	'save_table' => $this->table,
	'placeholder' => 'Quan hệ khác',
	'column_pos' => 4
	),

// --------------------------- Select ------------------------------ //
array(
	'type' => 'select', // selectmultiple
	'option_db' => array(
		'table' => $this->prefix.'depot',
		'key' => 'depot_id',
		'value' => 'name',
		'condition' => 'type = 1', /* Codition option */
		),
	'name' => 'port_id',
	'save_table' => $this->table,
	'placeholder' => 'Trực thuộc',
	'class' => 'required',
	'column_pos' => 1
	),
array(
	'type' => 'select',
	'option_local' => array( /* Hard code option */
		'1' => 'CY-CY',
		'2' => 'CY-DOOR',
		'3' => 'DOOR-CY',
		'4' => 'DOOR-DOOR'
		),
	'name' => 'port_id',
	'save_table' => $this->table,
	'placeholder' => 'Trực thuộc',
	'column_pos' => 1
	),

// --------------------------- Select Chain ------------------------------ //
array(
	'type' => 'select',
	'option_db' => array(
		'table' => $this->prefix.'city',
		'key' => 'city_id',
		'value' => 'name',
		),
	'name' => 'city_id_school',
	'select_ref_name' => 'district_id_school', /* Chain by element */
	'save_table' => $this->table,
	'placeholder' => 'Thành phố',
	'class' => 'required',
	'check_exist_and' => 'Thông tin thí sinh này đã tồn tại trong hệ thống!',
	'column_pos' => 2
	),
array(
	'type' => 'select',
	'option_db' => array(
		'table' => $this->prefix.'district',
		'key' => 'district_id',
		'value' => 'name',
		),
	'name' => 'district_id_school',
	'select_ref_id' => 'city_id', /* Chain to element by id */
	'save_table' => $this->table,
	'placeholder' => 'Quận / huyện',
	'class' => 'required',
	'check_exist_and' => 'Thông tin thí sinh này đã tồn tại trong hệ thống!',
	'column_pos' => 2
	),

// --------------------------- Select 2 ------------------------------ //
array(
    'type' => 'select2',
    'name' => 'booking_request_id',
    'save_table' => $this->table,
    'placeholder' => 'Tìm phiếu yêu cầu',
    'class' => 'required',
    'option_local' => array(
        'name' => 'number', /* Show in element */
        'helper' => 'bookingcommandm!get_container_by_request', /* Show helper: Model --> Function to get helper html */
        'search' => 'number', /* Search autocomplete in field */
        'table' => $this->prefix.'booking_request',
        'url' => get_slug('admin/booking_request'),
        ),
    'column_pos' => 1
    ),
array(
	'type' => 'select2',
	'name' => 'trip_plan_id',
	'save_table' => $this->table,
	'placeholder' => 'Dự kiến xuất tàu',
	'class' => 'required',
	'option_local' => array(
		'name' => 'name|ship_id,ship,ship_id,name|depot_id_from,depot,depot_id,name|depot_id_to,depot,depot_id,name', /* Show in element */
		'helper' => 'Ngày xuất cảng đi,etb_date|Tàu,ship_id,ship,ship_id,name|Cảng đi,depot_id_from,depot,depot_id,name|Cảng đến,depot_id_to,depot,depot_id,name', /* Show helper */
		'search' => 'name|ship_id,ship,ship_id,name|depot_id_from,depot,depot_id,name|depot_id_to,depot,depot_id,name',
		'table' => $this->prefix.'trip_plan', /* Search autocomplete in field */
		'url' => get_slug('admin/trip_plan'),
		),
	'column_pos' => 2
	),

// --------------------------- Select Autocomplate ------------------------------ //
array(
	'type' => 'select2_tag',
	'name' => 'school_name',
	'save_table' => $this->table,
	'placeholder' => 'Trường',
    'option_local' => array(
        'name' => 'school_name', /* Search autocomplete in field */
        'table' => $this->table,
        'url' => get_slug('admin/student'),
        ),
	'class' => 'required',
	'check_exist_and' => 'Thông tin thí sinh này đã tồn tại trong hệ thống!',
	'column_pos' => 2
	),

// --------------------------- Table From Other Table ------------------------------ //
array(
	'type' => 'table_database',
	'table_db' => array( /* Field on other table */
		array(
			'type' => 'hidden',
			'placeholder'=>'Loại container',
			'name'=>'container_type_id',
			),
		array(
			'type' => 'textbox',
			'placeholder'=>'Ghi chú',
			'name'=>'note',
			),
		array(
			'type' => 'select',
			'option_db' => array(
				'table' => $this->prefix.'container',
				'key' => 'container_id',
				'value' => 'name',
				),
			'name' => 'container_id',
			'placeholder' => 'Container lên tàu',
			'class' => 'autocomplete'
			),
		array(
			'type' => 'select',
			'option_local' => array(
				'1' => 'CY-CY',
				'2' => 'CY-DOOR',
				'3' => 'DOOR-CY',
				'4' => 'DOOR-DOOR'
				),
			'name' => 'delivery_type',
			'save_table' => $this->table,
			'placeholder' => 'Phương thức vận chuyển',
			),
		array(
			'type' => 'select2',
			'name' => 'customer_id_from',
			'ref_name' => 'customer_id',
			'placeholder' => 'Khách hàng <br/> (gửi)',
			'option_local' => array(
				'name' => 'name',
				'search' => 'name',
				'table' => $this->prefix.'customer',
				'url' => get_slug('admin/define_customer'),
				),
			),
		),
	'name' => 'loading_list_final_id', /* Ref id to other table */
    'save_table' => $this->prefix.'loading_list_final_detail', /* Save to other table */
	'placeholder' => 'Thông tin container',
	'formatter'=>function($column, $key, $row){ /* Format value before save */
		if($column=='container_type_id'){
			foreach($row as $k=>$value){
				$column_key = explode('_x_', $k);
				$column_key_post = explode('_x_', $key);
				if($column_key[count($column_key)-1]!=$column_key_post[count($column_key_post)-1]) continue;
				if(strpos($k,'container_id')!==FALSE){ // Customize
					$detail = $this->containerm->get_item_by_id($value);
					if(!empty($detail)) return $detail['container_type_id'];
					return 0;
				}
			}
		}
		return $row[$key];
	},
	'column_pos' => 1
	),
// --------------------------- Table From Hard Code ------------------------------ //
array(
	'type' => 'table_serialize',
	'table_local' => array( /* Hard code field */
		array(
			'type' => 'textbox',
			'placeholder'=>'Nội dung',
			'name'=>'content',
			'attr'=>array('readonly'=>'readonly'),
			),
		array(
			'type' => 'datetimefull',
			'placeholder'=>'Thời điểm',
			'name'=>'date',
			),
		),
	'name' => 'sof_detail',
	'value'=>array( /* Default value */
		array('content'=>'Đến trạm hoa tiêu', 'code'=>'ETA'),
		array('content'=>'Đón hoa tiêu', 'code'=>'POB'),
		),
	'save_table' => $this->table,
	'placeholder' => 'Statement of facts',
	'column_pos' => 3
	),


