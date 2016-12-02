<?php
/* Column trong IMPORT excel */

$insert_unique_without_update = array(
$this->prefix.'loading_list_final|loading_list_final_id' => array( /* Import to: table --> primary key */
    '_condition_' => function($mapping = ''){ /* Condition to import */
        $detail = $this->db->where('trip_plan_id', $this->input->post('import_trip_plan'))->where('deleted', 0)->get($this->prefix.'loading_list_final')->row();
        if(!empty($detail)){
            return FALSE;
        } else return TRUE;
    },
    '_exist_' => function($mapping = ''){ /* Return id if exist item */
        $detail = $this->db->where('trip_plan_id', $this->input->post('import_trip_plan'))->where('deleted', 0)->get($this->prefix.'loading_list_final')->row();
        if(!empty($detail)){
            return $detail->loading_list_final_id;
        } else return 0;
    },
    'trip_plan_id' => $this->input->post('import_trip_plan'),
    'created_date' => date('Y-m-d H:i:s'),
    'user_id' => $this->session->userdata('admin_id'),
    $this->prefix.'loading_list_final_detail|loading_list_final_id|container_id' => array( /* Ref table to import: table --> ref key --> exist key  */
        '_condition_' => function($mapping = ''){ /* Condition to import */
            $name = $mapping[0];
            $detail = $this->db->where('name', $name)->where('deleted', 0)->get($this->prefix.'container')->row();
            if(!empty($detail)){
                $container_id = $detail->container_id;
                $detail = $this->db->where('container_id', $container_id)->where('deleted', 0)->get($this->prefix.'loading_list_final_detail')->row();
                if(!empty($detail)){
                	$loading_list_final_id = $detail->loading_list_final_id;
                	$detail = $this->db->where('loading_list_final_id', $loading_list_final_id)->where('deleted', 0)->get($this->prefix.'loading_list_final')->row();
                	if(empty($detail)) return TRUE;
                	else return FALSE;
                } 
                else return TRUE;
            } else return FALSE;
        },
        'container_id' => function($mapping = ''){
            $name = $mapping[0];
            $detail = $this->db->where('name', $name)->where('deleted', 0)->get($this->prefix.'container')->row();
            if(!empty($detail)){
                return $detail->container_id;
            } else return 0;
        },
        'seal_number' => $mapping[3],
        'booking_number' => $mapping[1],
        'bill_number' => $mapping[2],
        'container_type_id' => function($mapping = ''){
            $name = $mapping[0];
            $detail = $this->db->where('name', $name)->where('deleted', 0)->get($this->prefix.'container')->row();
            if(!empty($detail)){
                return $detail->container_type_id;
            } else return 0;
        },
        'product_name' => $mapping[5],
        'customer_id_from' => function($mapping = ''){
            $name = $mapping[7];
            $detail = $this->db->where('name', $name)->where('deleted', 0)->get($this->prefix.'customer')->row();
            if(!empty($detail)){
                return $detail->customer_id;
            } else{
            	$this->db->insert($this->prefix.'customer', array('name' => $name));
            	return $this->db->insert_id();
        	}
        },
        'customer_id_to' => function($mapping = ''){
            $name = $mapping[8];
            $detail = $this->db->where('name', $name)->where('deleted', 0)->get($this->prefix.'customer')->row();
            if(!empty($detail)){
                return $detail->customer_id;
            } else{
            	$this->db->insert($this->prefix.'customer', array('name' => $name));
            	return $this->db->insert_id();
        	}
        },
        'customer_id_notification' => function($mapping = ''){
            $name = $mapping[9];
            $detail = $this->db->where('name', $name)->where('deleted', 0)->get($this->prefix.'customer')->row();
            if(!empty($detail)){
                return $detail->customer_id;
            } else{
            	$this->db->insert($this->prefix.'customer', array('name' => $name));
            	return $this->db->insert_id();
        	}
        },
        'weight' => $mapping[6],
        'delivery_type' => 0,
        'note' =>  $mapping[10]
        )
    )
);