<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Manage_store extends CI_Controller {

	var $userDataInfo;
	function __construct() {
		parent::__construct();
		$this->load->helper('custom_header'); /* custom helper */
		$this->load->helper('custom_array');
		$this->load->helper('custom_login');
		$this->load->helper('custom_key_encode_helper');
		$this->load->helper('form');
		$this->load->helper('html');
		if(!check_login('admin')){
			redirect('admin_login', 'location');
		}
	}

	public function index(){
		// CSS File needed
		add_css("../js/plugins/responsive-tables/responsive-tables.css");
		add_css("../js/plugins/datatables/DT_bootstrap.css");
		// JS files Needed
		add_js('plugins/datatables/jquery.dataTables.js');
		add_js("plugins/datatables/DT_bootstrap.js");
		add_js("plugins/responsive-tables/responsive-tables.js");
		$this->load->model('store_model');
		$data['page'] = 'manage_store/index';
		$data['layout'] = 'adminLayout';
		$where_arr['status'] = 1;
		$where_arr_or['status'] = 38;
		$data['store_detail'] = $this->store_model->listStore($where_arr, $order_arr = NULL, $limit = NULL, $offset = NULL, $where_arr_or);
		//echo $this->db->last_query();
		if($data['store_detail'] === false){
			$data['store_detail'] = array();
		}
		$data['success'] = $this->session->flashdata('success');
	    $data['success'] = isset($data['success']) ? $data['success'] : null;
		$this->load->view('layout', $data);
	}

	public function delete(){
		if(!$this->session->userdata('user_logged_in')) {
			redirect('admin/login');
		}
		$get = $this->uri->uri_to_assoc();
		if(!(isset($get["id"]) && isset($get["key"]))){
			redirect('admin_dashboard/index');
		}
		if($get["key"] != urlEncoded($get["id"], 'store')){
			redirect('admin_dashboard/index');
		}
		$this->load->model('store_model');
		$UpdateData = array('status'=>'6');
		if($this->store_model->update_store($UpdateData, $get["id"]) === true){
			$this->session->set_flashdata('success', 'Store Deleted successfully');
		}
		redirect('manage_store/index');
	}

	public function edit(){
		/* ********************** check user session ********************** */
		if(!$this->session->userdata('user_logged_in')) {
			redirect('admin/login');
		}
		$get = $this->uri->uri_to_assoc();
		if(!(isset($get["id"]) && isset($get["key"]))){
			redirect('admin_dashboard/index');
		}
		if($get["key"] != urlEncoded($get["id"], 'store')){
			redirect('admin_dashboard/index');
		}
		$this->load->model('store_model');
		$where_arr = array('store_id'=> $get['id']);
		$arr_data = $this->store_model->listStore($where_arr, NULL, 1, 0);
		$data['saved_data'] = $arr_data;
		$user_logged_detail = $this->session->userdata('user_logged_in');
		$this->load->helper('custom_header'); /* custom helper to add css and js */
		//$this->load->model('admin_model');
		$data['page'] = 'manage_store/add_edit';
		$data['layout'] = 'adminLayout';
		$data['error'] = 0;
		$edit_id = $get["id"];
		$data["edit_id"] = $edit_id;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('store_name', 'Store name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('address_line_1', 'Address1', 'trim|required|xss_clean');
		$this->form_validation->set_rules('address_line_2', 'Address2', 'trim|required|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
		$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('zip_code', 'Zipcode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('contact_no', 'Contact Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('hold_period', 'Hold Period', 'trim|required|xss_clean');
		$this->form_validation->set_rules('prefix', 'prefix', 'trim|required|xss_clean');
		$this->form_validation->set_rules('username', 'username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'password', 'trim|xss_clean');
		$this->form_validation->set_rules('leadsonline', 'leadsonline', 'trim|required|xss_clean');
		$this->form_validation->set_rules('register_required', 'Register Required', 'trim|required|xss_clean');
		$password = $this->input->post('password');
		if($this->input->post('submit') == 'Save'){
			$this->load->model('store_model');
			if($this->form_validation->run() == TRUE){
				$created_date = date('Y-m-d H:i:s');
				 $storeData = array(
					'store_name'  =>  $this->input->post('store_name'),
					'address_line_1	'  =>  $this->input->post('address_line_1'),
					'address_line_2'  =>  $this->input->post('address_line_2'),
					'state' => $this->input->post('state'),
					'city' => $this->input->post('city'),
					'zip_code' => $this->input->post('zip_code'),
					'contact_no'  =>  $this->input->post('contact_no'),
					'hold_period' => $this->input->post('hold_period'),
					'prefix'  =>  $this->input->post('prefix'),
					'email'  =>  $this->input->post('email'),
					'register_required' => $this->input->post('register_required'),
					'cash_mgt' => $this->input->post('cash_mgt'),
					'username' => $this->input->post('username'),					
					'leadsonline_storeid' => $this->input->post('leadsonline'),
					'created_by'  =>  $user_logged_detail["id"],
				);	
				if(!empty($password)) $storeData['password'] = md5($this->input->post('password'));
			//echo $this->db->last_query(); die;
				if($this->store_model->update_store($storeData, $edit_id) === true){
					$this->session->set_flashdata('success', 'Store Updated successfully');
					redirect('manage_store/index');
				}
			}
			else{
				$this->session->set_flashdata('error', 'Store didn\'t update successfully');
				redirect('manage_store/index');
			}
		}
		$this->load->view('layout', $data);
		/* ************** fetch config upload directory ************* */
        /* ************** END fetch config upload directory ********* */
	}

		public function add(){
		/* ********************** check user session ********************** */
		if(!$this->session->userdata('user_logged_in')) {
			redirect('admin/login');
		}
		$user_logged_detail = $this->session->userdata('user_logged_in');
		$this->load->helper('custom_header'); /* custom helper to add css and js */
		//$this->load->model('admin_model');
		$data['page'] = 'manage_store/add_edit';
		$data['layout'] = 'adminLayout';
		$data['error'] = 0;
		$this->load->library('form_validation');
		$this->form_validation->set_rules('store_name', 'Store name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('address_line_1', 'Address1', 'trim|required|xss_clean');
		$this->form_validation->set_rules('address_line_2', 'Address2', 'trim|required|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
		$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('zip_code', 'Zipcode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('hold_period', 'Hold Period', 'trim|required|xss_clean');
		$this->form_validation->set_rules('contact_no', 'Contact Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('prefix', 'prefix', 'trim|required|xss_clean');
		$this->form_validation->set_rules('username', 'username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'password', 'trim|required|xss_clean');
		$this->form_validation->set_rules('leadsonline', 'leadsonline', 'trim|required|xss_clean');
		$this->form_validation->set_rules('register_required', 'Register Required', 'trim|required|xss_clean');
		
		if($this->input->post('submit') == 'Save'){
			$this->load->model('store_model');
			if($this->form_validation->run() == TRUE){
				$created_date = date('Y-m-d H:i:s');
				 $storeData = array(
					'store_name'  =>  $this->input->post('store_name'),
					'address_line_1	'  =>  $this->input->post('address_line_1'),
					'address_line_2'  =>  $this->input->post('address_line_2'),
					'state' => $this->input->post('state'),
					'city' => $this->input->post('city'),
					'zip_code' => $this->input->post('zip_code'),
					'contact_no'  =>  $this->input->post('contact_no'),
					'hold_period' => $this->input->post('hold_period'),
					'prefix'  =>  $this->input->post('prefix'),
					'email'  =>  $this->input->post('email'),
					'register_required' => $this->input->post('register_required'),
					'created_date'  =>  $created_date,
					'status'  =>  1,
					'cash_mgt' => $this->input->post('cash_mgt'),
					'username' => $this->input->post('username'),
					'password' => md5($this->input->post('password')),
					'leadsonline_storeid' => $this->input->post('leadsonline'),
					'created_by'  =>  $user_logged_detail["id"],
				);	
				if($this->store_model->add_store($storeData) === true){

					/******************************************* Code 25-05-2014 *****************************************************/
					$store_id = $this->store_model->record_insert_id;
					// add Employee Counter
					$emp_counter = array('store_id'=> $store_id, 'counter_no'=>1, 'type'=>7);
					$this->store_model->add_counter($emp_counter);
					// add purchase agreement
					$purchase_arr = array('store_id'=> $store_id, 'name'=> "Purchase Agreement Content",'content'=>'<table style="width:700px; background-color:#F5F0E2; height:100%; padding-left:30px; padding-right:30px;">
    <tbody>
        <tr>
            <td style="text-align:center; background-color:#395076; color:#FFF; height:20px;"><strong>Purchase Agreement</strong></td>
        </tr>
        <tr>
            <td>
            <table style="width:700px;">
                <tbody>
                    <tr>
                        <td style="width:200px;">&nbsp;</td>
                        <td style="width:350px;">{emp_email}</td>
                        <td style="width:350px;">
                        <table>
                            <tbody>
                                <tr>
                                    <td>{store_name}</td>
                                </tr>
                                <tr>
                                    <td>{store_add1}</td>
                                </tr>
                                <tr>
                                    <td>{store_add2}</td>
                                </tr>
                            </tbody>
                        </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            </td>
        </tr>
        <tr>
            <td>
            <table style="width:700px;">
                <tbody>
                    <tr>
                        <td style="width:350px; text-align:left;">Paid Cash To : {emp_name}<br />
                        Purchase Id : {purchase_id}<br />
                        Amount Paid : <strong>{buy_price}</strong></td>
                    </tr>
                    <tr>
                        <td style="text-align:right; width:350px;"><strong>Date : </strong> {buy_date}</td>
                    </tr>
                </tbody>
            </table>
            </td>
        </tr>
        <tr>
            <td style="height:100px; vertical-align:top;">{device_detail}</td>
        </tr>
        <tr>
            <td style="text-align:left; font-size:16px;">PURCHASE AGREEMENT</td>
        </tr>
        <tr>
            <td>
            <p style="word-spacing:3px; line-height:300%">I agree to sell the devices listed above. at the prices. listed above. <strong><u>I am the              rightful owner of all of the equipment listed</u></strong> above and <strong><u>none of the              devices listed above are lost or stolen</u></strong>. I understand that Tech Ban, LLC works              with the law enforcement to prevent the spread of stolen merchandise and that if the property             listed above is later found to be lost or stolen, my information will be provided by Tech Bank             to law enforcement. I understant that upon execution of this aggreement.I will not be able             to get my device back or any of the information on it. I also understand that while Tech Bank             will makes its best efforts to remove all of the data on my device. they do not guarantee              data descruction. If any of the devices listed above are found to be lost or stolen. Tech Bank.           	reserve the right for a full refund of the amount listed above.</p>
            </td>
        </tr>
        <tr>
            <td style="height:50px;">&nbsp;</td>
        </tr>
        <tr>
            <td>IN WITNESS WHEREOF, the seller has signed this Purchase Aggreement as of the day             and year written below</td>
        </tr>
        <tr>
            <td>Seller Signature :{signature}</td>
        </tr>
        <tr>
            <td>Printed Name: {seller_first_name} {seller_last_name} </td>
        </tr>
        <tr>
            <td>Date: {buy_date}</td>
        </tr>
    </tbody>
</table>',);
					//$purchase_arr = array('store_id'=> $store_id, 'name'=> "Purchase Agreement Content");
					$this->store_model->add_purchase_aggreement($purchase_arr);
					$purchase_arr = array('store_id'=> $store_id, 'name'=> "Purchase Email Content",'content'=>'<p style="text-align: left;"><strong>Dear {seller_first_name} {seller_last_name},<br />
</strong></p>
<p style="text-align: left;">We are sending you our aggrement for purchase id- {purchase_id}<br /> Thanks <br /> Regards <br /> {emp_name}</p>');
					$this->store_model->add_purchase_aggreement($purchase_arr);
					//Add manager Counter
					$manager_counter = array('store_id'=> $store_id, 'counter_no'=>2, 'type'=>8);
					$this->store_model->add_counter($manager_counter);
					/*********************************************End of code*************************************************************/
					
					$this->session->set_flashdata('success', 'Store Added successfully');
				}
				redirect('manage_store/index');
			}
			else{
				$entered_object =  new stdClass;
				foreach($_POST as $key => $val){
					$entered_object->$key = $val;
				}
				$data['saved_data'] = $entered_object;
				$this->session->set_flashdata('error', 'Store didn\'t add successfully');
			}
		}

		/* ************** fetch config upload directory ************* */
        /* ************** END fetch config upload directory ********* */
		$user_logged_in = $this->session->userdata('user_logged_in');
		$this->load->view('layout', $data);
	}
	
}
