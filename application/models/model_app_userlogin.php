<?php
	class model_app_userlogin extends CI_Model {
		
		public function __construct() {
			parent::__construct();
			$this->load->database();
		}
		
		/**
		 * Verify user credentials
		 * @param string $username
		 * @param string $password
		 * @return array|false User data if valid, false otherwise
		 */
		function verifyLogin($username, $password) {
			$query = $this->db->get_where('user_app', array('username' => $username));
			
			if ($query->num_rows() > 0) {
				$user = $query->row_array();
				
				// Check if password is hashed (starts with $2y$ or similar) or plain text
				if (password_verify($password, $user['password'])) {
					// Password is hashed and matches
					return $this->appendLinkedEntity($user);
				} elseif ($user['password'] === $password) {
					// Password is plain text and matches (for backward compatibility)
					return $this->appendLinkedEntity($user);
				}
			}
			
			return false;
		}
		
		/**
		 * Get user by username
		 * @param string $username
		 * @return array|false User data or false
		 */
		function getUserByUsername($username) {
			$query = $this->db->get_where('user_app', array('username' => $username));
			return $query->num_rows() > 0 ? $query->row_array() : false;
		}
		
		/**
		 * Update user password
		 * @param string $newPassword
		 * @param int $userId
		 * @return bool
		 */
		function re_psw($newPassword, $userId = null) {
			if ($userId === null) {
				$userId = $this->session->userdata('id_user');
			}
			
			// Hash the password
			$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
			
			$this->db->where('id', $userId);
			return $this->db->update('user_app', array('password' => $hashedPassword));
		}
		
		/**
		 * Log user login to user_applogin table
		 * @param int $userId
		 * @param string $ipAddress
		 * @return bool
		 */
		function logLogin($userId, $ipAddress = null) {
			if ($ipAddress === null) {
				$ipAddress = $this->input->ip_address();
			}
			
			$data = array(
				'user_id' => $userId,
				'login_time' => date('Y-m-d H:i:s'),
				'ip_address' => $ipAddress
			);
			
			return $this->db->insert('user_applogin', $data);
		}

		/**
		 * Determine linked agen/customer entity metadata for a user
		 *
		 * @param array $user
		 * @return array
		 */
		private function appendLinkedEntity($user) {
			$metadata = [
				'linked_entity_id'   => null,
				'linked_entity_type' => null,
				'filter_user_column' => null,
			];

			if (empty($user['role']) || empty($user['username'])) {
				return array_merge($user, $metadata);
			}

			$role = (string) $user['role'];
			$username = $user['username'];
			$table = null;
			$type = null;
			$filterColumn = null;
			$prelinkedId = null;

			if ($role === '101') {
				$table = 'mst_agen';
				$type = 'agen';
				$filterColumn = 'agen_id';
				if (!empty($user['agen_id'])) {
					$prelinkedId = $user['agen_id'];
				}
			} elseif ($role === '102' || $role === '103') {
				$table = 'customer';
				$type = 'customer';
				$filterColumn = 'customer_id';
				if (!empty($user['customer_id'])) {
					$prelinkedId = $user['customer_id'];
				}
			}

			if ($table) {
				if ($prelinkedId) {
					$metadata['linked_entity_id'] = (int) $prelinkedId;
					$metadata['linked_entity_type'] = $type;
					$metadata['filter_user_column'] = $filterColumn;
				} else {
					$linkedRow = $this->db
						->select('id')
						->from($table)
						->where('nama', $username)
						->limit(1)
						->get()
						->row_array();

					if ($linkedRow && isset($linkedRow['id'])) {
						$metadata['linked_entity_id'] = $linkedRow['id'];
						$metadata['linked_entity_type'] = $type;
						$metadata['filter_user_column'] = $filterColumn;
					}
				}
			}

			return array_merge($user, $metadata);
		}
	}
?>