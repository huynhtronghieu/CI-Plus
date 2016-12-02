<?php
/* Column trong LIST show - filter */

// --------------------------- Column with textbox filter ------------------------------ //
array(  'db' => 'expired_date',  
		'dt' => 2,
		'html' => '<th> Ngày hết hạn </th>',
		'filter' => '<td>
                <input type="text" class="form-control form-filter input-sm" name="expired_date"> </td>', /* HTML in filter */
		'filter_column' => TRUE, /* Show filter */
        'formatter' => function( $d, $row ) { /* Format before show */
            return format_get_date($d);
        }
	),

// --------------------------- Column with textbox filter on join table ------------------------------ //
array(  'db' => 'customer_id',  
		'dt' => 5,
		'html' => '<th> Công ty nhận lệnh </th>',
		'filter' => '<td>
                <input type="text" class="form-control form-filter input-sm" name="customer_id"> </td>', /* HTML in filter */
		'filter_column' => TRUE, /* Show filter */
		'search' => 'name', /* Search in field of other table */
		'search_table' => $this->prefix.'customer', /* Join with other table */
		'search_table_alias' => 'c', /* Join (alias) with other table */
		'search_join' => 'customer_id', /* Join (condition) with other table */
		'formatter' => function( $d, $row ) { /* Format before show */
			$detail = $this->customerm->get_item_by_id($d);
			if(!empty($detail)) return $detail['name'];
			else return '';
		}
	),

// --------------------------- Column with hard code select filter ------------------------------ //
array(  'db' => 'is_orgin',  
        'dt' => 8,
        'html' => '<th> Lấy lệnh gốc </th>',
        'filter' => '<td>
                <select name="is_orgin" class="form-control form-filter input-sm">
                    <option value="">Chọn...</option>
                    <option value="1">Có</option>
                    <option value="0">Không</option>
                </select></td>', /* HTML in filter */
        'filter_column' => TRUE, /* Show filter */
        'formatter' => function( $d, $row ) { /* Format before show */
            if($d==1) return '<div style="text-align:center;"><i class="fa fa-check"></i></div>';
            else if($d==0) return '';
            else return '';
        }
    ),

// --------------------------- Column with dynamic select filter ------------------------------ //
array(  'db' => 'depot_id',  
		'dt' => 4,
		'html' => '<th> Nơi lấy container </th>',
		'filter' => function (){ /* HTML in filter */
			$list = $this->depotm->get_items('name ASC');
			$option = array();
			foreach($list->result_array() as $row){
				$option[] = '<option value="'.$row['depot_id'].'">'.$row['name'].'</option>';
			}
                	return '<td>
                <select name="depot_id" class="form-control form-filter input-sm">
                    <option value="">Chọn...</option>
                    '.implode('', $option).'
                </select></td>';
                },
		'filter_column' => TRUE, /* Show filter */
		'formatter' => function( $d, $row ) { /* Format before show */
			$detail = $this->depotm->get_item_by_id($d);
			if(!empty($detail)) return $detail['name'];
			else return '';
		}
	),

// --------------------------- Column with select filter on join table ------------------------------ //
array(  'db' => 'depot_id_from',  
		'table' => $this->prefix.'trip_plan',
		'table_alias' => 'tp',
		'dt' => 7,
		'html' => '<th> Cảng đi </th>',
		'filter' => function (){ /* HTML in filter */
			$list = $this->depotm->get_items('name ASC');
			$option = array();
			foreach($list->result_array() as $row){
				$option[] = '<option value="'.$row['name'].'">'.$row['name'].'</option>';
			}
                	return '<td>
                <select name="depot_id_from" class="form-control form-filter input-sm">
                    <option value="">Chọn...</option>
                    '.implode('', $option).'
                </select></td>';
                },
		'filter_column' => TRUE, /* Show filter */
		'search' => 'name', /* Search in field of other table */
		'search_table' => $this->prefix.'depot', /* Join with other table */
		'search_table_alias' => 'd1', /* Join (alias) with other table */
		'search_join' => 'depot_id', /* Join (condition) with other table */
		'formatter' => function( $d, $row ) { /* Format before show */
			$trip_plan_detail = $this->tripplanm->get_item_by_id($row['trip_plan_id']);
			if(!empty($trip_plan_detail)){
				$detail = $this->depotm->get_item_by_id($trip_plan_detail['depot_id_from']);
				if(!empty($detail)) return $detail['name'];
				else return '';
			} else return '';
		}
	),

// --------------------------- Column with custom format show by dynamic column ------------------------------ //
array(  'db' => $this->primary_key, 
			'dt' => $dt,
			'html' => '<th> '.$row->name.' </th>', /* HTML in filter */
			'addition_data' => 'container_type_id,'.$row->container_type_id, /* Addition data for format before show: key --> value */
			'formatter' => function( $d, $row, $addition_data ) { /* Format before show */
				$this->db->join($this->prefix.'container', $this->prefix.'container_log.container_id='.$this->prefix.'container.container_id');
				$this->db->where($this->prefix.'container.container_type_id', $addition_data['container_type_id']);
				$this->db->where($this->prefix.'container_log.container_log_id IN (SELECT MAX('.$this->prefix.'container_log.container_log_id) FROM '.$this->prefix.'container_log GROUP BY '.$this->prefix.'container_log.container_id)');
				$this->db->having($this->prefix.'container.depot_id', $d);
				$this->db->having($this->prefix.'container_log.trip_plan_id', 0);
				$this->db->having($this->prefix.'container_log.status', 2);
				$this->db->having($this->prefix.'container.is_damage', 0);
				$not_damage_empty = $this->db->get($this->prefix.'container_log')->num_rows();

                $this->db->join($this->prefix.'container', $this->prefix.'container_log.container_id='.$this->prefix.'container.container_id');
                $this->db->where($this->prefix.'container.container_type_id', $addition_data['container_type_id']);
                $this->db->where($this->prefix.'container_log.container_log_id IN (SELECT MAX('.$this->prefix.'container_log.container_log_id) FROM '.$this->prefix.'container_log GROUP BY '.$this->prefix.'container_log.container_id)');
                $this->db->having($this->prefix.'container.depot_id', $d);
                $this->db->having($this->prefix.'container_log.trip_plan_id', 0);
                $this->db->having($this->prefix.'container_log.status', 1);
                $this->db->having($this->prefix.'container.is_damage', 0);
                $not_damage_full = $this->db->get($this->prefix.'container_log')->num_rows();

				$this->db->join($this->prefix.'container', $this->prefix.'container_log.container_id='.$this->prefix.'container.container_id');
				$this->db->where($this->prefix.'container.container_type_id', $addition_data['container_type_id']);
				$this->db->where($this->prefix.'container_log.container_log_id IN (SELECT MAX('.$this->prefix.'container_log.container_log_id) FROM '.$this->prefix.'container_log GROUP BY '.$this->prefix.'container_log.container_id)');
				$this->db->having($this->prefix.'container.depot_id', $d);
				$this->db->having($this->prefix.'container_log.trip_plan_id', 0);
				$this->db->having($this->prefix.'container.is_damage', 1);
				$damage = $this->db->get($this->prefix.'container_log')->num_rows();

				return '
                <span style="color:green;"><span style="display: inline-block; width: 120px;">Empty:</span> '.((empty($not_damage_empty))?'<span style="color:black;">-</span>':'<b>'.$not_damage_empty.'</b>').'</b></span>
                <br/>
                <span style="color:blue;"><span style="display: inline-block; width: 120px;">Full:</span> '.((empty($not_damage_full))?'<span style="color:black;">-</span>':'<b>'.$not_damage_full.'</b>').'</span>
                <br/>
                <span style="color:red;"><span style="display: inline-block; width: 120px;">Sửa chữa:</span> '.((empty($damage))?'<span style="color:black;">-</span>':'<b>'.$damage.'</b>').'</b></span>';
			}
		),
