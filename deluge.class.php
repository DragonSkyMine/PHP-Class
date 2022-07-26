<?php
/**
* class Deluge
* url / password of deluge web
* Do not forget to use the function close() after using any other function
*/
class DELUGE
{
	const DOWNLOAD_PATH = "/data/Torrents"; // !!! TO CHANGE !!!
	private $ch;
	private $url;
	private $request_id;
	public $last_http_transaction;

	function __construct($host, $password)	{
		$this->url = $host . (substr($host, -1) == "/" ? "" : "/") . "json";
		$this->request_id = 0;
		$this->ch = curl_init($this->url);
		$curl_options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			CURLOPT_HTTPHEADER => array("Accept: application/json", "Content-Type: application/json"),
			CURLOPT_ENCODING => "",
			CURLOPT_COOKIEJAR  => "",
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLINFO_HEADER_OUT => true
		);
		curl_setopt_array($this->ch, $curl_options);

		try {
			$response = $this->makeRequest("auth.login", array($password));
			if (gettype($response) != 'boolean' || $response != true) {
				throw new Exception("Login failed");
			}
			else {
				$response = $this->makeRequest("auth.check_session", array());
				if (gettype($response) != 'boolean' || $response != true) {
					throw new Exception("Web api is not connected to a daemon");
				}
			}
		}
		catch (Exception $e) {
			throw new Exception("Failed to initiate deluge api: " . $e->getMessage());
		}
	}

	function close() {
		curl_close($this->ch);
	}

	/////////////////////////////
	//
	//core functions
	//
	//parsed from https://web.archive.org/web/20150423162855/http://deluge-torrent.org:80/docs/master/core/rpc.html
	//
	/////////////////////////////

	//Adds a torrent file to the session.
	//Parameters:
	//filename (string) – the filename of the torrent
	//filedump (string) – a base64 encoded string of the torrent file contents
	//options (dict) – the options to apply to the torrent on add
	public function addTorrentFile($filename, $filedump, $options) {
		return $this->makeRequest("core.add_torrent_file", array($filename, $filedump, $options));
	}

	//Adds a torrent from a magnet link.
	//Parameters:
	//uri (string) – the magnet link
	//options (dict) – the options to apply to the torrent on add
	public function addTorrentMagnet($uri, $options) {
		return $this->makeRequest("core.add_torrent_magnet", array($uri, $options));
	}

	//Adds a torrent from a url. Deluge will attempt to fetch the torrentfrom url prior to adding it to the session.
	//Parameters:
	//url (string) – the url pointing to the torrent file
	//options (dict) – the options to apply to the torrent on add
	//headers (dict) – any optional headers to send
	public function addTorrentUrl($url, $options, $headers) {
		return $this->makeRequest("core.add_torrent_url", array($url, $options, $headers));
	}

	public function connectPeer($torrentId, $ip, $port) {
		return $this->makeRequest("core.connect_peer", array($torrentId, $ip, $port));
	}

	public function createTorrent($path, $tracker, $pieceLength, $comment, $target, $webseeds, $private, $createdBy, $trackers, $addToSession) {
		return $this->makeRequest("core.create_torrent", array($path, $tracker, $pieceLength, $comment, $target, $webseeds, $private, $createdBy, $trackers, $addToSession));
	}

	public function disablePlugin($plugin) {
		return $this->makeRequest("core.disable_plugin", array($plugin));
	}

	public function enablePlugin($plugin) {
		return $this->makeRequest("core.enable_plugin", array($plugin));
	}

	public function forceReannounce($torrentIds) {
		return $this->makeRequest("core.force_reannounce", array($torrentIds));
	}

	//Forces a data recheck on torrent_ids
	public function forceRecheck($torrentIds) {
		return $this->makeRequest("core.force_recheck", array($torrentIds));
	}

	//Returns a list of plugins available in the core
	public function getAvailablePlugins() {
		return $this->makeRequest("core.get_available_plugins", array());
	}

	//Returns a dictionary of the session’s cache status.
	public function getCacheStatus() {
		return $this->makeRequest("core.get_cache_status", array());
	}

	//Get all the preferences as a dictionary
	public function getConfig() {
		return $this->makeRequest("core.get_config", array());
	}

	//Get the config value for key
	public function getConfigValue($key) {
		return $this->makeRequest("core.get_config_value", array($key));
	}

	//Get the config values for the entered keys
	public function getConfigValues($keys) {
		return $this->makeRequest("core.get_config_values", array($keys));
	}

	//Returns a list of enabled plugins in the core
	public function getEnabledPlugins() {
		return $this->makeRequest("core.get_enabled_plugins", array());
	}

	//returns {field: [(value,count)] }for use in sidebar(s)
	public function getFilterTree($showZeroHits, $hideCat) {
		return $this->makeRequest("core.get_filter_tree", array($showZeroHits, $hideCat));
	}

	//Returns the number of free bytes at path
	public function getFreeSpace($path) {
		return $this->makeRequest("core.get_free_space", array($path));
	}

	//Returns the libtorrent version.
	public function getLibtorrentVersion() {
		return $this->makeRequest("core.get_libtorrent_version", array());
	}

	//Returns the active listen port
	public function getListenPort() {
		return $this->makeRequest("core.get_listen_port", array());
	}

	//Returns the current number of connections
	public function getNumConnections() {
		return $this->makeRequest("core.get_num_connections", array());
	}

	public function getPathSize($path) {
		return $this->makeRequest("core.get_path_size", array($path));
	}

	//Returns a list of torrent_ids in the session.
	public function getSessionState() {
		return $this->makeRequest("core.get_session_state", array());
	}

	//Gets the session status values for ‘keys’, these keys are takingfrom libtorrent’s session status.
	public function getSessionStatus($keys) {
		return $this->makeRequest("core.get_session_status", array($keys));
	}

	public function getTorrentStatus($torrentId, $keys, $diff) {
		return $this->makeRequest("core.get_torrent_status", array($torrentId, $keys, $diff));
	}

	//returns all torrents , optionally filtered by filter_dict.
	public function getTorrentsStatus($filterDict, $keys, $diff) {
		return $this->makeRequest("core.get_torrents_status", array($filterDict, $keys, $diff));
	}

	public function glob($path) {
		return $this->makeRequest("core.glob", array($path));
	}

	public function moveStorage($torrentIds, $dest) {
		return $this->makeRequest("core.move_storage", array($torrentIds, $dest));
	}

	//Pause all torrents in the session
	public function pauseAllTorrents() {
		return $this->makeRequest("core.pause_all_torrents", array());
	}

	public function pauseTorrent($torrentIds) {
		return $this->makeRequest("core.pause_torrent", array($torrentIds));
	}

	public function queueBottom($torrentIds) {
		return $this->makeRequest("core.queue_bottom", array($torrentIds));
	}

	public function queueDown($torrentIds) {
		return $this->makeRequest("core.queue_down", array($torrentIds));
	}

	public function queueTop($torrentIds) {
		return $this->makeRequest("core.queue_top", array($torrentIds));
	}

	public function queueUp($torrentIds) {
		return $this->makeRequest("core.queue_up", array($torrentIds));
	}

	//Removes a torrent from the session.
	//Parameters:
	//torrentId (string) – the torrentId of the torrent to remove
	//removeData (boolean) – if True, remove the data associated with this torrent
	public function removeTorrent($torrentId, $removeData) {
		return $this->makeRequest("core.remove_torrent", array($torrentId, $removeData));
	}

	//Rename files in torrent_id.  Since this is an asynchronous operation bylibtorrent, watch for the TorrentFileRenamedEvent to know when thefiles have been renamed.
	//Parameters:
	//torrentId (string) – the torrentId to rename files
	//filenames (((index, filename), ...)) – a list of index, filename pairs
	public function renameFiles($torrentId, $filenames) {
		return $this->makeRequest("core.rename_files", array($torrentId, $filenames));
	}

	//Renames the ‘folder’ to ‘new_folder’ in ‘torrent_id’.  Watch for theTorrentFolderRenamedEvent which is emitted when the folder has beenrenamed successfully.
	//Parameters:
	//torrentId (string) – the torrent to rename folder in
	//folder (string) – the folder to rename
	//newFolder (string) – the new folder name
	public function renameFolder($torrentId, $folder, $newFolder) {
		return $this->makeRequest("core.rename_folder", array($torrentId, $folder, $newFolder));
	}

	//Rescans the plugin folders for new plugins
	public function rescanPlugins() {
		return $this->makeRequest("core.rescan_plugins", array());
	}

	//Resume all torrents in the session
	public function resumeAllTorrents() {
		return $this->makeRequest("core.resume_all_torrents", array());
	}

	public function resumeTorrent($torrentIds) {
		return $this->makeRequest("core.resume_torrent", array($torrentIds));
	}

	//Set the config with values from dictionary
	public function setConfig($config) {
		return $this->makeRequest("core.set_config", array($config));
	}

	//Sets the auto managed flag for queueing purposes
	public function setTorrentAutoManaged($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_auto_managed", array($torrentId, $value));
	}

	//Sets a torrents file priorities
	public function setTorrentFilePriorities($torrentId, $priorities) {
		return $this->makeRequest("core.set_torrent_file_priorities", array($torrentId, $priorities));
	}

	//Sets a torrents max number of connections
	public function setTorrentMaxConnections($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_max_connections", array($torrentId, $value));
	}

	//Sets a torrents max download speed
	public function setTorrentMaxDownloadSpeed($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_max_download_speed", array($torrentId, $value));
	}

	//Sets a torrents max number of upload slots
	public function setTorrentMaxUploadSlots($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_max_upload_slots", array($torrentId, $value));
	}

	//Sets a torrents max upload speed
	public function setTorrentMaxUploadSpeed($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_max_upload_speed", array($torrentId, $value));
	}

	//Sets the torrent to be moved when completed
	public function setTorrentMoveCompleted($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_move_completed", array($torrentId, $value));
	}

	//Sets the path for the torrent to be moved when completed
	public function setTorrentMoveCompletedPath($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_move_completed_path", array($torrentId, $value));
	}

	//Sets the torrent options for torrent_ids
	public function setTorrentOptions($torrentIds, $options) {
		return $this->makeRequest("core.set_torrent_options", array($torrentIds, $options));
	}

	//Sets a higher priority to the first and last pieces
	public function setTorrentPrioritizeFirstLast($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_prioritize_first_last", array($torrentId, $value));
	}

	//Sets the torrent to be removed at ‘stop_ratio’
	public function setTorrentRemoveAtRatio($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_remove_at_ratio", array($torrentId, $value));
	}

	//Sets the torrent to stop at ‘stop_ratio’
	public function setTorrentStopAtRatio($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_stop_at_ratio", array($torrentId, $value));
	}

	//Sets the ratio when to stop a torrent if ‘stop_at_ratio’ is set
	public function setTorrentStopRatio($torrentId, $value) {
		return $this->makeRequest("core.set_torrent_stop_ratio", array($torrentId, $value));
	}

	//Sets a torrents tracker list.  trackers will be [{“url”, “tier”}]
	public function setTorrentTrackers($torrentId, $trackers) {
		return $this->makeRequest("core.set_torrent_trackers", array($torrentId, $trackers));
	}

	//Checks if the active port is open
	public function testListenPort() {
		return $this->makeRequest("core.test_listen_port", array());
	}

	public function uploadPlugin($filename, $filedump) {
		return $this->makeRequest("core.upload_plugin", array($filename, $filedump));
	}

	//Returns a list of the exported methods.
	public function getMethodList() {
		return $this->makeRequest("daemon.get_method_list", array());
	}

	//Returns some info from the daemon.
	public function info() {
		return $this->makeRequest("daemon.info", array());
	}

	public function shutdown(...$params) {
		return $this->makeRequest("daemon.shutdown", $params);
	}

	/////////////////////////////
	//
	//web ui functions
	//
	//parsed from https://web.archive.org/web/20150423143401/http://deluge-torrent.org:80/docs/master/modules/ui/web/json_api.html#module-deluge.ui.web.json_api
	//
	/////////////////////////////

	//Parameters:
	//host (string) – the hostname
	//port (int) – the port
	//username (string) – the username to login as
	//password (string) – the password to login with
	public function addHost($host, $port, $username, $password) {
		return $this->makeRequest("web.add_host", array($host, $port, $username, $password));
	}

	//Usage
	public function addTorrents($torrents) {
		return $this->makeRequest("web.add_torrents", array($torrents));
	}

	public function connect($hostId) {
		return $this->makeRequest("web.connect", array($hostId));
	}

	public function connected() {
		return $this->makeRequest("web.connected", array());
	}

	public function deregisterEventListener($event) {
		return $this->makeRequest("web.deregister_event_listener", array($event));
	}

	public function disconnect() {
		return $this->makeRequest("web.disconnect", array());
	}

	public function downloadTorrentFromUrl($url, $cookie) {
		return $this->makeRequest("web.download_torrent_from_url", array($url, $cookie));
	}

	/* in core
	public function getConfig() {
	return $this->makeRequest("web.get_config", array());
	*/

	public function getEvents() {
		return $this->makeRequest("web.get_events", array());
	}

	public function getHost($hostId) {
		return $this->makeRequest("web.get_host", array($hostId));
	}

	public function getHostStatus($hostId) {
		return $this->makeRequest("web.get_host_status", array($hostId));
	}

	public function getHosts() {
		return $this->makeRequest("web.get_hosts", array());
	}

	public function getTorrentFiles($torrentId) {
		return $this->makeRequest("web.get_torrent_files", array($torrentId));
	}

	public function getTorrentInfo($filename) {
		return $this->makeRequest("web.get_torrent_info", array($filename));
	}

	public function registerEventListener($event) {
		return $this->makeRequest("web.register_event_listener", array($event));
	}

	public function removeHost($connectionId) {
		return $this->makeRequest("web.remove_host", array($connectionId));
	}

	/*in core
	public function setConfig($config) {
	return $this->makeRequest("web.set_config", array($config));
	*/

	public function startDaemon($port) {
		return $this->makeRequest("web.start_daemon", array($port));
	}

	public function stopDaemon($hostId) {
		return $this->makeRequest("web.stop_daemon", array($hostId));
	}

	//Parameters:
	//keys (list) – the information about the torrents to gather
	//filterDict (dictionary) – the filters to apply when selecting torrents.
	public function updateUi($keys, $filterDict) {
		return $this->makeRequest("web.update_ui", array($keys, $filterDict));
	}

	private function makeRequest($method, $params) {
		$post_data = json_encode(array("id" => $this->request_id, "method" => $method, "params" => $params));
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post_data);

		$response = curl_exec($this->ch);

		if ($response === false) {
			throw new Exception("Request for method $method failed due to curl error (no. " . curl_errno($this->ch) . "): " . curl_error($this->ch));
		}

		list($response_header, $response_body) = explode("\r\n\r\n", $response, 2);
		$info = curl_getinfo($this->ch);
		$request_header = trim($info["request_header"]);
		$http_code = $info["http_code"];

		$this->last_http_transaction = new StdClass();
		$this->last_http_transaction->request_header = $request_header;
		$this->last_http_transaction->request_body = $post_data;
		$this->last_http_transaction->response_header = $response_header;
		$this->last_http_transaction->response_body = $response_body;

		if ($http_code != 200) {
			throw new Exception("Request for method $method returned unexpected http code: $http_code (expected 200)");
		}
		$response_obj = json_decode($response_body);
		if (!is_null($response_obj->error)) {
			throw new Exception("Request for method $method returned a deluge error (no. {$response_obj->error->code}): {$response_obj->error->message}");
		}
		if ($response_obj->id != $this->request_id) {
			throw new Exception("Sent request id {$this->request_id} but received response id {$response_obj->id}");
		}
		$this->request_id++;
		return $response_obj->result;
	}

	public function addTorrent($url) {
		//echo $url;
		$torrentPath = [];
		if (is_array($url)) {
			foreach ($url as $key => $value) {
				$filePath = $this->downloadTorrentFromUrl($value, NULL);
				$torrentPath[] = array('path' => $filePath, 'url' => $value, 'options' => array('download_path' => self::DOWNLOAD_PATH ));
			}
		} else {
			$filePath = $this->downloadTorrentFromUrl($url, NULL);
			$torrentPath[] = array('path' => $filePath, 'url' => $url, 'options' => array('download_path' => self::DOWNLOAD_PATH ) );
		}
		//print_r($this->addTorrents($torrentPath));
		return $this->addTorrents($torrentPath);
	}

	public function getTorrents() {
		$torrent = $this->updateUi(array(),array());

		$torrents = [];
		foreach ($torrent->torrents as $key => $value) {
			//print_r($this->getTorrentFiles($key));
			//print_r($value);
			$torrentInfo = array(
				'name' => $value->name,
				'hash' => $value->hash,
				'ratio' => $value->ratio,
				'num_files' => $value->num_files,
				'state' => $value->state,
				'progress' => $value->progress,
				'download_location' => $value->download_location,
				'move_on_completed_path' => $value->move_on_completed_path,
				'total_size' => $value->total_size,
				'files' => [],
			);
			$torrentFiles = [];
			foreach ($value->files as $keyFile => $file) {
				$torrentFiles[$file->index] = array(
					'path' => $file->path,
					'size' => $file->size,
					'progress' => $value->file_progress[$file->index]
				);
			}
			$torrentInfo['files'] = $torrentFiles;
			$torrents[] = $torrentInfo;
		}
		return $torrents;
	}
}

?>
