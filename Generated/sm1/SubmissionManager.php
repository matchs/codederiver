<?php
require 'CompleteSubmissionHandler.php';
class SubmissionManager {
	private $submissionHandler;
	public function __construct(CompleteSubmissionHandler $submissionHandler){
		$this->submissionHandler = $submissionHandler;
	}
	public function doSubmission($author_id, $abstract, $paper){
		$this->submissionHandler->submit($author_id,$abstract,$paper);
	}
}
?>
