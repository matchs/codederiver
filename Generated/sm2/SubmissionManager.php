<?php
require 'CompleteSubmissionHandler.php';
class SubmissionManager {
	private $submissionHandler;
	private $fileManager;
	public function __construct(CompleteSubmissionHandler $submissionHandler){
		$this->submissionHandler = $submissionHandler;
		$this->fileManager = new FileManager();
	}
	public function doSubmission($author_id, $abstract, $paper){
		$this->submissionHandler->submit($author_id,$abstract,$paper);
	}
	public function downloadPaperPdf($paper_id){
	}
}
?>
