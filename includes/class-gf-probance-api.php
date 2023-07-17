<?php

/**
 * Gravity Forms Probance API Library.
 *
 * @since     1.0.0
 * @package   GravityForms
 * @author    Arnaud Flament
 */
class GF_Probance_API {

	/**
	 * Probance account login.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $login Probance account login.
	 */
	protected $username;

	/**
	 * Probance account password.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $password Probance account password.
	 */
	protected $password;

	/**
	 * Probance webservice url.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $webservice_url Probance webservice url.
	 */
	protected $webservice_url = 'https://lesthermesmarins.my-probance.one/rt/api/resource/client/lesthermesmarins_lesthermesmarins/';

	/**
	 * Initialize API library.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $username (default: '') Probance username.
	 * @param string $password  (default: '') Probance password.
	 *
	 */
	public function __construct( $username = '', $password = '' ) {

		// Assign API key to object.
		$this->username = $username;
		$this->password  = $password;

	}

	/**
	 * Get current account details.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @uses   GF_Probance_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function account_details() {

		return $this->process_request();

	}
    /**
	 * Get a specific Probance list member.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $email_address Email address.
	 *
	 * @uses   GF_Probance_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function get_member_if_exist($email_address) {

		return $this->process_request( 'search?email='. $email_address );
	}

	/**
	 * Add or update a Probance list member.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param array $params Params.
	 *
	 * @uses   GF_Probance_API::process_request()
	 * @throws Exception
	 *
	 * @return array
	 */
	public function update_list_member( $params, $action ) {
        error_log('PARAMS :');
        error_log(print_r($params, true));
//		$response = $this->process_request( 'Contacts', 'Merge', $params );
		$response = $this->process_request( $action, 'POST', $params );

		return $response;

	}

	/**
	 * Process Probance API request.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param string $service    Request path.
	 * @param string $method     Request method. Defaults to GET.
	 * @param array  $data       Request data.
	 * @param string $return_key Array key from response to return. Defaults to null (return full response).
	 *
	 * @throws Exception if API request returns an error, exception is thrown.
	 *
	 * @return array
	 */
	private function process_request( $service = '', $method = 'GET', $data = array(), $return_key = null ) {

		// If username is not set, throw exception.
		if ( rgblank( $this->username ) ) {
			throw new Exception( 'login must be defined to process an API request.' );
		}

		// If password is not set, throw exception.
		if ( rgblank( $this->password) ) {
			throw new Exception( 'password must be defined to process an API request.' );
		}

		if ($service == 'update'|| $service == 'search')
        {
            $request_url = $this->webservice_url . $service .'?email=' . $data['email'];

        } else
         {
            // Build base request URL.
            $request_url = $this->webservice_url . $service;

        }

//         Set credentials for Basic Authentication
        $credentials = base64_encode("$this->username:$this->password");
		$headers = array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "Basic {$credentials}",
        );
        error_log(print_r($request_url, true));
        error_log('%%%% METHOD %%% :');
        error_log(print_r($method, true));

		if ($method != 'GET') {
			           error_log('DATA POST');
			           error_log(print_r($data, true));
						$json_data = json_encode($data);
			           error_log('$json_data');
			           error_log($json_data);
			
						$response = wp_remote_request(
							$request_url,
							array(
								'method'  => $method,
								'headers' => $headers,
								'body'    => $json_data,
								'sslverify' => apply_filters('https_local_ssl_verify', false),
								'timeout' => apply_filters('http_request_timeout', 30),
							));
					} else {
						// For 'GET' need to pass the method in the header
						$method = array('method' => $method);
						$headers = array_merge($headers , $method);
						// Build base request arguments.
						$args = array(
							'headers' => $headers,
							'sslverify' => apply_filters('https_local_ssl_verify', false),
							'timeout' => apply_filters('http_request_timeout', 30),
						);
			           error_log('ARGS IN GET');
			           error_log(print_r($args, true));
						$response = wp_remote_request($request_url, $args);
					}


		// If request was not successful, throw exception.
        // TODO changer l'Exception
		if ( is_wp_error( $response ) ) {
			throw new GF_Probance_Exception( $response->get_error_message() );
		}

		// Decode response body.
		$response['body'] = json_decode( $response['body'], true );
		// Get the response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code != 200 ) {

			// If status code is set, throw exception.
			if ( isset( $response['body']['ErrorCode'] ) ) {
               // TODO changer l'Exception
				// Initialize exception.
				$exception = new GF_Probance_Exception( $response['body']['ErrorCode'], $response_code );
				$exception->setDetail( $response['body']['ErrorMessage'] );
				$exception->setErrors( $response['body']['ErrorMessage'] );
				throw $exception;

			}

			throw new GF_Probance_Exception( wp_remote_retrieve_response_message( $response ), $response_code );

		}

//		if($response['body']['status'] != 'OK'){
//		if($response['Success'] !== true){

//			$exception = new GF_Probance_Exception( $response['body']['ErrorCode'], $response_code );
//			$exception->setDetail( $response['body']['ErrorMessage'] );
//			$exception->setErrors( $response['body']['ErrorMessage'] );
//			throw $exception;
//		}
        // TODO voir les exeptions
		// Remove links from response.
//		unset( $response['body']['_links'] );

		// If a return key is defined and array item exists, return it.
		if ( ! empty( $return_key ) && isset( $response['body'][ $return_key ] ) ) {
			return $response['body'][ $return_key ];
		}

//		return $response['body'];
		return $response;

	}

}

/**
 * Gravity Forms Probance Exception.
 *
 * @since     1.0.0
 * @package   GravityForms
 * @author    Nicolas Mollet
 */
class GF_Probance_Exception extends Exception {

	/**
	 * Additional details about the exception.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string $detail Additional details about the exception.
	 */
	protected $detail;

	/**
	 * Exception error messages.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $errors Exception error messages.
	 */
	protected $errors;

	/**
	 * Get additional details about the exception.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string|null
	 */
	public function getDetail() {

		return $this->detail;

	}

	/**
	 * Get exception error messages.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array|null
	 */
	public function getErrors() {

		return $this->errors;

	}

	/**
	 * Determine if exception has additional details.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function hasDetail() {

		return ! empty( $this->detail );

	}

	/**
	 * Determine if exception has error messages.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function hasErrors() {

		return ! empty( $this->errors );

	}

	/**
	 * Set exception details.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $detail Additional details about the exception.
	 */
	public function setDetail( $detail ) {

		$this->detail = $detail;

	}

	/**
	 * Set exception error messages.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $detail Additional error messages about the exception.
	 */
	public function setErrors( $errors ) {

		$this->errors = $errors;

	}

}
