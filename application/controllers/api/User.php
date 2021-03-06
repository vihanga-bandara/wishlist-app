<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 1/20/2019
 * Time: 9:36 PM
 */

use Restserver\Libraries\REST_Controller;

require APPPATH . '/libraries/REST_Controller.php';


class User extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model("UserModel");
	}

	/**
	 * Register a new user
	 * -------------------------
	 * @param: name
	 * @param: email
	 * @param: password
	 * @param: listName
	 * @param: listDescription
	 * -------------------------
	 * @method : POST
	 * @url : api/user/register
	 */
	public function register_user_post()
	{
		header("Access-Control-Allow-Origin: *");

		# XSS Filtering (Security)
		$_POST = json_decode(file_get_contents("php://input"), true);
		$_POST = $this->security->xss_clean($_POST);

		//Validation
		$this->form_validation->set_rules('user_name', 'Name of User', 'trim|required|is_unique[users_tbl.user_name]|alpha_numeric|max_length[20]',
			array('is_unique' => 'Username already exists. Please Enter a different Username')
		);
		$this->form_validation->set_rules('user_email', 'Email', 'trim|required|valid_email|is_unique[users_tbl.user_email]|max_length[80]',
			array('is_unique' => 'Email already exists, Please Enter a different Email')
		);
		$this->form_validation->set_rules('user_password', 'Password', 'trim|required|max_length[80]');
		$this->form_validation->set_rules('user_list_name', 'List Name', 'trim|max_length[80]');
		$this->form_validation->set_rules('user_list_description', 'List Description', 'trim|max_length[150]');

		if ($this->form_validation->run() == FALSE)
		{
			$message = array(
				"status" => false,
				"error" => $this->form_validation->error_array(),
				"message" => $this->validation_errors()
			);
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST);
		} else
		{
			$register_data = array(
				"user_name" => $this->post("user_name", TRUE),
				"user_email" => $this->post("user_email", TRUE),
				"user_password" => hash("sha256", $this->input->post("user_password", TRUE)),
				"user_list_name" => $this->post("user_list_name", TRUE),
				"user_list_description" => $this->post("user_list_description", TRUE),
			);
			$data = $this->UserModel->registerUser($register_data);
			if (!empty($data) && ($data > 0))
			{
				$message = array(
					"status" => true,
					"message" => "Successfully registered user"
				);
				$this->response($message, REST_Controller::HTTP_CREATED);
			} else
			{
				$message = array(
					"status" => false,
					"error" => $this->form_validation->error_array(),
					"message" => "Unable to complete registration, try again later"
				);
				$this->response($message, REST_Controller::HTTP_NOT_FOUND);
			}
		}

	}

	/**
	 * User Login
	 * -------------------------
	 * @param: name
	 * @param: password
	 * -------------------------
	 * @method : POST
	 * @url : api/user/login
	 */
	public function login_user_post()
	{
		header("Access-Control-Allow-Origin: *");

		# XSS Filtering (Security)
		$_POST = json_decode(file_get_contents("php://input"), true);
		$_POST = $this->security->xss_clean($_POST);
		$this->load->library('form_validation');

		//Validation
		$this->form_validation->set_rules('user_name', 'Name of User', 'trim|required');
		$this->form_validation->set_rules('user_password', 'Password', 'trim|required|max_length[80]');

		if ($this->form_validation->run() == FALSE)
		{
			$message = array(
				"status" => false,
				"error" => $this->form_validation->error_array(),
				"message" => $this->validation_errors()
			);
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST);
		} else
		{
			$user_name = $this->post("user_name", TRUE);
			$user_password = $this->post("user_password", TRUE);

			$login_data = $this->UserModel->loginUser($user_name, $user_password);
			if (!empty($login_data) AND $login_data != false)
			{
				$loginData = array(
					"user_id" => $login_data->user_id,
					"user_name" => $login_data->user_name,
					"user_email" => $login_data->user_email,
					"user_list_name" => $login_data->user_list_name,
					"user_list_description" => $login_data->user_list_description
				);
				$message = array(
					"id" => $login_data->user_id,
					"user_id" => $login_data->user_id,
					"user_list_name" => $login_data->user_list_name,
					"user_list_description" => $login_data->user_list_description,
					"status" => true,
					"data" => $loginData,
					"message" => "User successfully logged in"
				);
				$this->session->set_userdata($loginData);
				$this->UserModel->activateUser($login_data->user_id);
				$this->response($message, REST_Controller::HTTP_OK);
			} else
			{
				//Login Error
				$message = array(
					"status" => false,
					"error" => $this->form_validation->error_array(),
					"message" => "Error when logging in, try again later"
				);
				$this->response($message, REST_Controller::HTTP_NOT_FOUND);
			}
		}

	}


	/**
	 * Create new list
	 * -------------------------
	 * @param: user_list_name
	 * @param: user_list_description
	 * -------------------------
	 * @method : PUT
	 * @url : api/user/update
	 */
	public function create_list_put()
	{
		if ($this->input->server("REQUEST_METHOD") == "PUT")
		{
			$last = $this->uri->total_segments();
			$user_id = $this->uri->segment($last);

			$user_list_name = $this->put("user_list_name", TRUE);
			$user_list_description = $this->put("user_list_description", TRUE);

			$response = $this->UserModel->updateUser($user_list_name, $user_list_description, $user_id);
			if ($response)
			{
				$responseData = array(
					"user_list_name" => $user_list_name,
					"user_list_description" => $user_list_description,
				);
				$message = array(
					"status" => true,
					"data" => $responseData,
					"message" => "Your List has been created"
				);
				$this->response($message, REST_Controller::HTTP_OK);
			} else
			{
				$message = array(
					"status" => false,
					"error" => $this->form_validation->error_array(),
					"message" => "Error when creating list"
				);
				$this->response($message, REST_Controller::HTTP_BAD_REQUEST);
			}
		}
	}

	/**
	 * Get user details
	 * -------------------------
	 * @method : GET
	 * @url : api/user/{id}
	 *
	 */
	public function single_user_get()
	{
		$last = $this->uri->total_segments();
		$user_id = $this->uri->segment($last);
		$userData = $this->UserModel->getSingleUserData($user_id);
		if (($userData != null) && !empty($userData))
		{
			$this->response($userData);
		}
	}

	/**
	 * User Logout
	 * -------------------------
	 * -------------------------
	 * @method : POST
	 * @url : api/user/logout
	 */
	public function logout_post()
	{
		$user_id = $this->session->user_id;
		if ($user_id != null)
		{
			$this->session->sess_destroy();
			$this->UserModel->deactivateUser($user_id);
			$message = array(
				"status" => true,
				"user_id" => $user_id,
				"message" => "User successfully logged out"
			);
			$this->response($message, REST_Controller::HTTP_OK);
		} else
		{
			$message = array(
				"status" => false,
				"error" => $this->form_validation->error_array(),
				"message" => "Error Logging out"
			);
			$this->response($message, REST_Controller::HTTP_BAD_REQUEST);
		}


	}
}
