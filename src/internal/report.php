<?php
include_once "database.php";

class Report
{
	public $report_id;
	public $report_date;
	public $reporter_ip;
	public $post_id;
	public $post_board;
	public $reason;

	public function write()
	{
		$database = new Database();

		$report_id = $database->sanitize($this->report_id);
		$reporter_ip = $database->sanitize($this->reporter_ip);
		$post_id = $database->sanitize($this->post_id);
		$post_board = $database->sanitize($this->post_board);
		$reason = $database->sanitize($this->reason);

		$database->query("
			insert into reports (
				reporter_ip, reported_post_id, reported_post_board, report_reason
			) values (
				'$reporter_ip', $post_id, '$post_board', '$reason'
			);
		");
	}
}

function report_list()
{
	$database = new Database();
	$result = $database->query("
		select * from reports order by report_date;  
	");

	$reports = array();

	while ($report_array = $result->fetch_assoc())
	{
		$report = new Report();
		$report->report_id = $report_array["report_id"];
		$report->report_date = $report_array["report_date"];
		$report->report_ip = $report_array["reporter_ip"];
		$report->post_id = $report_array["reported_post_id"];
		$report->post_board = $report_array["reported_post_board"];
		$report->reason = $report_array["report_reason"];

		array_push($reports, $report);
	}

	return $reports;
}

function report_delete($report_id)
{
	$database = new Database();
	$database->query("
		delete from reports where report_id = $report_id;
	");
}

// delete all reports for a post
function report_delete_for_post($board, $id)
{
	$database = new Database();
	$result = $database->query("
		select report_id from reports where reported_post_board = '$board' and reported_post_id = $id;
	");

	while ($report_id = $result->fetch_assoc())
	{
		report_delete($report_id["report_id"]);
	}
}

function report_exists($board, $id, $user_ip)
{
	$database = new Database();
	$user_ip = $database->sanitize($user_ip);
	$query = "
		select count(*) from reports where reported_post_board = '$board' and reported_post_id = $id and reporter_ip = '$user_ip';
	";
	$result = $database->query($query);

	return $result->fetch_assoc()["count(*)"] > 0;
}
