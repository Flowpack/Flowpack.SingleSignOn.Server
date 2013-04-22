<?php
namespace Flowpack\SingleSignOn\Server\Http;

/*                                                                               *
 * This script belongs to the TYPO3 Flow package "Flowpack.SingleSignOn.Server". *
 *                                                                               */

use TYPO3\Flow\Annotations as FLOW3;

use TYPO3\Flow\Http\Request,
	TYPO3\Flow\Http\Response;

/**
 * A multi-exec capable cURL request engine
 */
class MultiCurlEngine implements \TYPO3\Flow\Http\Client\RequestEngineInterface {

	/**
	 * Timeout in seconds (CURLOPT_TIMEOUT)
	 * @var integer
	 */
	protected $timeout = 5;

	/**
	 * Sends a single HTTP request
	 *
	 * @param \TYPO3\Flow\Http\Request $request
	 * @return \TYPO3\Flow\Http\Response
	 * @throws \TYPO3\Flow\Http\Exception
	 */
	public function sendRequest(\TYPO3\Flow\Http\Request $request) {
		$responses = $this->sendRequests(array($request));
		if (isset($responses[0])) {
			return $responses[0];
		} else {
			return NULL;
		}
	}

	/**
	 * Sends multiple request in parallel
	 *
	 * @param array $requests Array of \TYPO3\Flow\Http\Request
	 * @return array Array of \TYPO3\Flow\Http\Response or \TYPO3\Flow\Http\Exception if an exception occured during a request
	 */
	public function sendRequests(array $requests) {
		if (!extension_loaded('curl')) {
			throw new \TYPO3\Flow\Http\Exception('CurlEngine requires the PHP CURL extension to be installed and loaded.', 1346319808);
		}

		$multiCurl = curl_multi_init();

		$curlHandles = array();
		foreach ($requests as $index => $request) {
			$requestUri = $request->getUri();
			$curlHandle = curl_init((string)$requestUri);

			$options = array(
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_HEADER => TRUE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_FRESH_CONNECT => TRUE,
				CURLOPT_FORBID_REUSE => TRUE,
				CURLOPT_TIMEOUT => $this->timeout,
			);
			curl_setopt_array($curlHandle, $options);

				// Send an empty Expect header in order to avoid chunked data transfer (which we can't handle yet).
				// If we don't set this, cURL will set "Expect: 100-continue" for requests larger than 1024 bytes.
			curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Expect:'));

			switch ($request->getMethod()) {
				case 'GET' :
					if ($request->getContent()) {
							// workaround because else the request would implicitly fall into POST:
						curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'GET');
						curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $request->getContent());
					}
				break;
				case 'POST' :
					curl_setopt($curlHandle, CURLOPT_POST, TRUE);

					$body = $request->getContent() !== '' ? $request->getContent() : http_build_query($request->getArguments());
					curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $body);
				break;
				case 'PUT' :
					curl_setopt($curlHandle, CURLOPT_PUT, TRUE);
					if ($request->getContent() !== '') {
						$inFileHandler = fopen('php://temp', 'r+');
						fwrite($inFileHandler, $request->getContent());
						rewind($inFileHandler);
						curl_setopt_array($curlHandle, array(
							CURLOPT_INFILE => $inFileHandler,
							CURLOPT_INFILESIZE => strlen($request->getContent()),
						));
					}
				break;
				default:
					curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $request->getMethod());
			}

			$preparedHeaders = array();
			foreach ($request->getHeaders()->getAll() as $fieldName => $values) {
				foreach ($values as $value) {
					$preparedHeaders[] = $fieldName . ': ' . $value;
				}
			}
			curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $preparedHeaders);

			if ($requestUri->getPort() !== NULL) {
				curl_setopt($curlHandle, CURLOPT_PORT, $requestUri->getPort());
			}

			$curlHandles[$index] = $curlHandle;

			curl_multi_add_handle($multiCurl, $curlHandle);
		}

		$active = 0;
		// execute handles
		do {
			$mrc = curl_multi_exec($multiCurl, $active);
		} while ($mrc === CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc === CURLM_OK) {
			if (curl_multi_select($multiCurl) !== -1) {
				do {
					$mrc = curl_multi_exec($multiCurl, $active);
				} while ($mrc === CURLM_CALL_MULTI_PERFORM);
			}
		}

		$responses = array();
		foreach ($curlHandles as $index => $curlHandle) {
			$error = curl_error($curlHandle);
			if ($error !== '') {
				$responses[$index] = new CurlEngineException('cURL reported error code ' . curl_errno($curlHandle) . ' with message "' . curl_error($curlHandle) . '". Last requested URL was "' . curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL) . '".', 1355496033);
			} else {
				$curlResult = curl_multi_getcontent($curlHandle);
				$response = Response::createFromRaw($curlResult);
				if ($response->getStatusCode() === 100) {
					$response = Response::createFromRaw($response->getContent(), $response);
				}
				$responses[$index] = $response;
			}
			curl_multi_remove_handle($multiCurl, $curlHandle);
		}

		curl_multi_close($multiCurl);

		return $responses;
	}

	/**
	 * @param int $timeout
	 */
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
	}

}
?>