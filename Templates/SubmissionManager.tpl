@file_path foo/bar/baz
@file_ext php
@file_name SubmissionManager
@file_langs php

<?php

@features F001.002
require 'PartialSubmissionHandler.php';

@features F001.001
require 'CompleteSubmissionHandler.php';

class SubmissionManager {

	private $submissionHandler;

        @features F001.003
	private $fileManager;

        @features F001.001
	public function __construct(CompleteSubmissionHandler $submissionHandler){
		$this->submissionHandler = $submissionHandler;

                @features F001.003
		$this->fileManager = new FileManager();
	}

        @features F001.002
	public function __construct(PartialSubmissionHandler $submissionHandler){
		$this->submissionHandler = $submissionHandler;

                @features F001.003
		$this->fileManager = new FileManager();
	}

        @features F001.001
	public function doSubmission($author_id, $abstract, $paper){
		$this->submissionHandler->submit($author_id,$abstract,$paper);
	}

        @features F001.002
	public function doSubmission($author_id, $abstract){
		$this->submissionHandler->submit($author_id,$abstract);
	}

        @features F001.003 
	public function downloadPaperPdf($paper_id){
		
	}
}

?>
